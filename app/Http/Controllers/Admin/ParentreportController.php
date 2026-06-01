<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\StudentRec;
use App\Models\StudentAbsent;
use Illuminate\Support\Facades\Validator;

class ParentreportController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:Parentreport', ['only' => ['index', 'store', 'delete']]);
    }

    public function index(Request $request)
    {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value . ' - Parent Report',
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        $breadcrumbs = [
            ['disabled' => false, 'url' => 'admin', 'title' => 'Dashboard'],
            ['disabled' => false, 'url' => '#', 'title' => 'Parent Report'],
        ];

        Carbon::setLocale('id');

        if ($request->ajax()) {
            $students = DB::table('students')->get();

            $data = $students->map(function ($student) {
                $absentData = DB::table('student_absents')
                    ->select(
                        DB::raw('SUM(CASE WHEN kehadiran = 0 THEN 1 ELSE 0 END) as hadir_sum'),
                        DB::raw('SUM(CASE WHEN kehadiran = 1 THEN 1 ELSE 0 END) as izinsakit_sum'),
                        DB::raw('SUM(CASE WHEN kehadiran = 2 THEN 1 ELSE 0 END) as izin_sum'),
                        DB::raw('SUM(CASE WHEN kehadiran = 3 THEN 1 ELSE 0 END) as noinfo_sum')
                    )
                    ->where('nik', $student->nik)
                    ->groupBy('nik')
                    ->first();

                return [
                    'nik' => $student->nik,
                    'nama' => $student->nama,
                    'hadir_sum' => $absentData->hadir_sum ?? 0,
                    'izinsakit_sum' => $absentData->izinsakit_sum ?? 0,
                    'izin_sum' => $absentData->izin_sum ?? 0,
                    'noinfo_sum' => $absentData->noinfo_sum ?? 0,
                    'tidakhadir_sum' => ($absentData->izinsakit_sum ?? 0) + ($absentData->izin_sum ?? 0) + ($absentData->noinfo_sum ?? 0)
                ];
            });

            return DataTables::of($data)->addIndexColumn()->make(true);
        }

        return view('admin.parent-report', compact('config', 'breadcrumbs'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rec_date' => 'required|date_format:Y-m-d',
            'name' => 'required|max:255',
            'kehadiran' => 'required|in:1,2,3',
            'note' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $data = $validator->validated();
        $data['student_id'] = null;


        $data['created_at'] = now();
        $data['updated_at'] = now();

        try {
            StudentAbsent::create($data);
            return response()->json(['message' => 'Data has been saved successfully']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'An error occurred while saving the data'], 500);
        }
    }
}
