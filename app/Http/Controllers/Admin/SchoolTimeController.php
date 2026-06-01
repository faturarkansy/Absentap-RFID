<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Models\SchoolTime;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SchoolTimeController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:SchoolTime', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value . ' - School Time',
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        $breadcrumbs = [
            ['disabled' => false, 'url' => 'admin', 'title' => 'Dashboard'],
            ['disabled' => false, 'url' => '#', 'title' => 'School Time'],
        ];

        if ($request->ajax()) {
            $data = SchoolTime::all();
            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
                $actionBtn = '<a href="#" data-bs-toggle="modal" data-bs-target="#modalView" data-bs-id="' . $row->id . '" data-bs-name="' . $row->name . '" data-bs-time_start="' . $row->time_limit_start . '" data-bs-time_end="' . $row->time_limit_end . '" class="btn btn-sm btn-outline-primary">Edit</a>';
                return $actionBtn;
            })
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="schooltime" data-id="' . $row->id . '">';
                })
                ->rawColumns(['checkbox', 'action'])->make(true);
        }
        return view('admin.school-time', compact('config', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'time_limit_start' => 'required',
            'time_limit_end' => 'required',
        ]);

        if (!$validator->fails()) {
            $data = $validator->safe()->all();
            $data['time_limit_start'] = $data['time_limit_start'] . ':00';
            $data['time_limit_end'] = $data['time_limit_end'] . ':00';
            DB::beginTransaction();
            try {
                SchoolTime::create($data);
                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Data has been save', 'redirect' => 'admin/school-time']);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['error' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['error' => $validator->errors()]);
        }
        return $response;
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'time_limit_start' => 'required',
            'time_limit_end' => 'required',
        ]);

        if (!$validator->fails()) {
            $data = $validator->safe()->all();
            $data['time_limit_start'] = $data['time_limit_start'] . ':00';
            $data['time_limit_end'] = $data['time_limit_end'] . ':00';
            DB::beginTransaction();
            try {
                $get = SchoolTime::findOrFail($id);
                $get->update($data);
                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Data has been updated', 'redirect' => 'admin/school-time']);
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['error' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['error' => $validator->errors()]);
        }
        return $response;
    }

    public function delete(Request $request)
    {
        $id_array = $request->input('id');
        $data = SchoolTime::whereIn('id', $id_array);
        if ($data->delete()) {
            $response = response()->json(['status' => 'success', 'message' => 'Data has been delete']);
        } else {
            $response = response()->json(['status' => 'failed', 'message' => 'Data cant delete']);
        }
        return $response;
    }
}
