<?php

namespace App\Http\Controllers;

use App\Models\StudentRec;
use Carbon\Carbon;
use App\Models\SchoolTime;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAbsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    public function index()
    {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value,
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        return view('index', compact('config', 'data'));
    }

    public function sendAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required'
        ]);

        if (!$validator->fails()) {
            $student = Student::where('card_code', $request->code)->first();
            if (isset($student) && !empty($student)) {
                $date_now = date('Y-m-d');
                $holiday = DB::table('holidays')->where('date', $date_now)->first();
                if ($holiday) {
                    return response()->json(['status' => 'error', 'message' => 'Hari ini libur, presensi ditiadakan!']);
                }
                $time_now = date('H:i:s');
                $student_rec = StudentRec::where([['student_nik', $student->nik], ['rec_date', $date_now]])->first();
                if ($student_rec == null) {
                    $update = false;
                } else {
                    $update = true;
                }
                $attendance = SchoolTime::whereRaw('CURRENT_TIME() BETWEEN `time_limit_start` AND `time_limit_end`')->first();
                if ($attendance) {
                    DB::beginTransaction();
                    try {
                        if ($update == false) {
                            $data['student_nik'] = $student->nik;
                            $data['rec_date'] = $date_now;
                            $data['rec_times'] = SchoolTime::count();
                            $rec_detail[0]['id'] = $attendance->id;
                            $rec_detail[0]['name'] = $attendance->name;
                            $rec_detail[0]['time'] = $time_now;
                            $data['rec_detail'] = json_encode($rec_detail, TRUE);
                            $data['rec_sum'] = 1;
                            $view['student_name'] = $student->nama;
                            $view['student_nik'] = $student->nik;
                            $view['attendance_name'] = $attendance->name;
                            $view['attendance_time'] = $time_now;
                            StudentRec::create($data);
                            $response = response()->json(['status' => 'success', 'message' => 'Data has been created', 'data' => $view]);
                        } else {
                            $rec_detail = json_decode($student_rec->rec_detail, TRUE);
                            $row['id'] = $attendance->id;
                            $row['name'] = $attendance->name;
                            $row['time'] = $time_now;
                            $foundId = FALSE;
                            foreach ($rec_detail as $key => $value) {
                                if ($value['id'] == $row['id']) {
                                    $foundId = TRUE;
                                }
                            }
                            if ($foundId == FALSE && $student_rec->rec_sum <= $student_rec->rec_times) {
                                array_push($rec_detail, $row);
                                $data['rec_detail'] = json_encode($rec_detail, TRUE);
                                $data['rec_sum'] = $student_rec->rec_sum + 1;
                                $view['student_name'] = $student->nama;
                                $view['student_nik'] = $student->nik;
                                $view['attendance_name'] = $attendance->name;
                                $view['attendance_time'] = $time_now;
                                $student_rec->update($data);
                                $response = response()->json(['status' => 'success', 'message' => 'Data has been updated', 'data' => $view]);
                            } else {
                                $response = response()->json(['status' => 'error', 'message' => 'Anda Sudah Melakukan Absensi']);
                            }
                        }
                        DB::commit();
                    } catch (\Throwable $throw) {
                        DB::rollBack();
                        Log::error($throw);
                        $response = response()->json(['error' => $throw->getMessage()]);
                    }

                } else {
                    $response = response()->json(['status' => 'error', 'message' => 'Saat ini bukan waktu absen']);
                }
                // if (empty($attendance)) {
                //     $record = StudentRecord::where('student_id', $student->id)->first();
                //     $start_time = SchoolStartTime::where([['student_category_id', $student->student_category_id], ['student_group_id', $student->student_group_id]])->first();
                //     DB::beginTransaction();
                //     try {
                //         $data['name'] = $student['full_name'];
                //         $data['photo'] = $student['student_photo'];
                //         if (isset($data['photo']) && !empty($data['photo'])) {
                //             $setting = Setting::where('name', 'school_erp_url')->first();
                //             $data['photo'] = $setting['value'] . $data['photo'];
                //         } else {
                //             $data['photo'] = asset('images/man.png');
                //         }
                //         $data['roll_number'] = $student['roll_no'];
                //         if (time() <= strtotime($start_time['time_limit'])) {
                //             $req['attendance_type'] = 'P';
                //         } else {
                //             $req['attendance_type'] = 'L';
                //         }
                //         $data['status'] = $req['attendance_type'];
                //         $req['notes'] = 'Attendance By System';
                //         $req['attendance_date'] = date('Y-m-d');
                //         $req['created_at'] = date('Y-m-d H:i:s');
                //         $req['updated_at'] = date('Y-m-d H:i:s');
                //         $req['student_id'] = $record->student_id;
                //         $req['student_record_id'] = $record->id;
                //         $req['class_id'] = $record->class_id;
                //         $req['section_id'] = $record->section_id;
                //         $req['created_by'] = '1';
                //         $req['updated_by'] = '1';
                //         $req['school_id'] = $record->school_id;
                //         $req['academic_id'] = $record->academic_id;
                //         $req['active_status'] = $record->active_status;
                //         SmStudentAttendance::create($req);
                //         DB::commit();
                //         $response = response()->json(['status' => 'success', 'message' => 'Data has been updated', 'data' => $data]);
                //     } catch (\Throwable $throw) {
                //         DB::rollBack();
                //         Log::error($throw);
                //         $response = response()->json(['error' => $throw->getMessage()]);
                //     }
                // } else {
                //     $response = response()->json(['status' => 'error', 'message' => 'Anda sudah hadir!']);
                // }
            } else {
                $response = response()->json(['status' => 'error', 'message' => 'Kartu anda tidak terdaftar!']);
            }
        } else {
            $error = $validator->errors()->first();
            $response = response()->json(['error' => $error]);
        }
        return $response;
    }

    public function login()
    {
        return redirect()->route('admin.home');
    }

    public function check()
    {
        $studentRecs = StudentRec::whereColumn('rec_times', 'rec_sum')->get();
        $date_now = Carbon::now()->format('Y-m-d');

        foreach ($studentRecs as $rec) {
            $save_nik = $rec->student_nik;
            $student = Student::where('nik', $save_nik)->first();
            
            if ($student) {
                $save_nama = $student->nama;
                $student_absent = [
                    'nik' => $save_nik,
                    'name' => $save_nama,
                    'kehadiran' => 0,
                    'rec_date' => $date_now,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('student_absents')->insert($student_absent);
                $response = response()->json(['status' => 'success', 'message' => 'Data has been updated']);
            } else {
                
                $response = response()->json(['error']);
        }
        return $response;
        }
    }
}