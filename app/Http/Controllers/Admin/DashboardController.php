<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAbsent;
use Carbon\Carbon;
use DB;


class DashboardController extends Controller
{
    public function index()
    {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value . ' - Dashboard',
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        $breadcrumbs = [
            ['disabled' => false, 'url' => 'admin', 'title' => 'Dashboard'],
            ['disabled' => false, 'url' => '#', 'title' => ''],
        ];

        //Donut Chart
        $today = Carbon::today()->toDateString();
        $izin_sakit = StudentAbsent::where('kehadiran', '1')->where('rec_date', $today)->count();
        $izin = StudentAbsent::where('kehadiran', '2')->where('rec_date', $today)->count();
        $no_info = StudentAbsent::where('kehadiran', '3')->where('rec_date', $today)->count();

        //Line Chart
        $hasDataToday = ($izin_sakit > 0 || $izin > 0 || $no_info > 0);
        $todays = Carbon::today();
        $months = [];
        $startMonth = $todays->copy()->subMonths(5);
        $currentMonth = $startMonth->copy();

        for ($i = 0; $i < 6; $i++) {
            $months[] = $currentMonth->format('Y-m');
            $currentMonth->addMonth();
        }

        $attendanceData = StudentAbsent::select(
            DB::raw('DATE_FORMAT(rec_date, "%Y-%m") as month'),
            DB::raw('SUM(CASE WHEN kehadiran = 0 THEN 1 ELSE 0 END) as hadir_count'),
            DB::raw('SUM(CASE WHEN kehadiran IN (1, 2, 3) THEN 1 ELSE 0 END) as tidak_hadir_count')
        )
            ->whereIn(DB::raw('DATE_FORMAT(rec_date, "%Y-%m")'), $months)
            ->groupBy(DB::raw('DATE_FORMAT(rec_date, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(rec_date, "%Y-%m")'))
            ->get();

        $monthlyAttendance = array_fill_keys($months, ['hadir' => 0, 'tidak_hadir' => 0]);

        foreach ($attendanceData as $data) {
            $monthlyAttendance[$data->month]['hadir'] = $data->hadir_count;
            $monthlyAttendance[$data->month]['tidak_hadir'] = $data->tidak_hadir_count;
        }

        $monthNames = [];
        $hadirCounts = [];
        $tidakHadirCounts = [];

        foreach ($monthlyAttendance as $month => $counts) {
            $monthNames[] = Carbon::createFromFormat('Y-m', $month)->locale('id')->isoFormat('MMMM');
            $hadirCounts[] = $counts['hadir'];
            $tidakHadirCounts[] = $counts['tidak_hadir'];
        }

        //Kehadiran tertinggi
        $highestAttendance = StudentAbsent::select('nik', DB::raw('COUNT(*) as hadir_count'))
            ->where('kehadiran', 0)
            ->groupBy('nik')
            ->orderByDesc('hadir_count')
            ->first();
        $hadir_result = null;
        if ($highestAttendance) {
            $student = Student::where('nik', $highestAttendance->nik)->first();
            if ($student) {
                $hadir_result = $student->name;
            }
        }

        //Kehadiran terendah
        $lowestAttendance = StudentAbsent::select('nik', DB::raw('COUNT(*) as tidakhadir_count'))
            ->where('kehadiran', 1)
            ->where('kehadiran', 2)
            ->where('kehadiran', 3)
            ->groupBy('nik')
            ->orderByDesc('tidakhadir_count')
            ->first();
        $tidakhadir_result = null;
        if ($lowestAttendance) {
            $student = Student::where('nik', $lowestAttendance->nik)->first();
            if ($student) {
                $tidakhadir_result = $student->name;
            }
        }

        //Waktu terkini
        $currentHour = Carbon::now()->format('H');
        $image = 'malam.png';
        if ($currentHour >= 6 && $currentHour < 10) {
            $image = 'pagi.png';
        } elseif ($currentHour >= 10 && $currentHour < 15) {
            $image = 'siang.png';
        } elseif ($currentHour >= 15 && $currentHour < 18) {
            $image = 'sore.png';
        }
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');

        //Total Siswa
        $totalStudents = Student::count();
        $totalMaleStudents = Student::where('jk', 'L')->count();
        $totalFemaleStudents = Student::where('jk', 'P')->count();

        return view('admin.dashboard', compact(
            'config',
            'breadcrumbs',
            'izin_sakit',
            'izin',
            'no_info',
            'monthNames',
            'hadirCounts',
            'tidakHadirCounts',
            'hasDataToday',
            'hadir_result',
            'tidakhadir_result',
            'image',
            'currentDateTime',
            'totalStudents',
            'totalMaleStudents',
            'totalFemaleStudents'
        ));
    }
}
