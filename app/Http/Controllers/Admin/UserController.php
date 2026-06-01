<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {

    function __construct() {
        $this->middleware('permission:user-list', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-reset', ['only' => ['resetpassword']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value . ' - Users',
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        $breadcrumbs = [
            ['disabled' => false, 'url' => 'admin', 'title' => 'Dashboard'],
            ['disabled' => false, 'url' => '#', 'title' => 'Users'],
        ];

        if ($request->ajax()) {
            $active = $request['active'];
            $data = User::with('roles')->when($active, function ($query, $active) {
                if ($active == 'non_active') {
                    return $query->where('active', '0');
                } else {
                    return $query->where('active', '1');
                }
            })->get();
            $data = $data->reject(function ($user, $key) {
                return $user->hasRole('Super Admin');
            });
            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
                $actionBtn = '<div class="dropdown">
                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog fa-fw"></i> Aksi</button>
                <ul class="dropdown-menu">';
                if (auth()->user()->hasPermissionTo('user-edit') || auth()->user()->hasRole('Super Admin')) {
                    $actionBtn .= '<li><a class="dropdown-item" href="admin/users/' . $row->id . '/edit">Ubah</a></li>';
                }
                if (auth()->user()->hasPermissionTo('user-reset') || auth()->user()->hasRole('Super Admin')) {
                    $actionBtn .= '<li><a href="#" data-bs-toggle="modal" data-bs-target="#modalReset" data-bs-id="' . $row->id . '" class="dropdown-item">Reset Password</a></li>';
                }
                if (auth()->user()->hasPermissionTo('user-delete') || auth()->user()->hasRole('Super Admin')) {
                    $actionBtn .= '<li><a href="#" data-bs-toggle="modal" data-bs-target="#modalDelete" data-bs-id="' . $row->id . '" class="delete dropdown-item">Hapus</a></li>';
                }
                $actionBtn .= '</ul></div>';
                return $actionBtn;
            })->addColumn('role', function ($row) {
                $role = '';
                foreach ($row->getRoleNames() as $val) {
                    $role = '<span class="badge text-bg-primary">' . $val . '</span>';
                }
                return $role;
            })->rawColumns(['action', 'role'])->make(true);
        }
        return view('admin.users.index', compact('config', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value . ' - Tambah Users',
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        $breadcrumbs = [
            ['disabled' => false, 'url' => 'admin', 'title' => 'Dashboard'],
            ['disabled' => true, 'url' => '#', 'title' => 'Tambah'],
        ];
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('admin.users.form', compact('config', 'breadcrumbs', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'position' => 'required|max:255',
            'roles' => 'required',
            'address' => 'nullable',
            'username' => 'required|min:3|max:255|unique:users,username',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:5|max:255|confirmed',
            'password_confirmation'  => 'nullable|same:password',
            'active' => 'required|between:0,1',
            'image' => 'image|mimes:jpg,png,jpeg',
        ], [
            'same' => ':attribute and :other must match.',
            'unique' => ':attribute is not available'
        ]);

        if (!$validator->fails()) {
            $data = $validator->safe()->except(['password_confirmation', 'roles']);
            $data['password'] = Hash::make($data['password']);
            DB::beginTransaction();
            try {
                if (isset($data['image']) && !empty($data['image'])) {
                    $image = $data['image'];
                    $data['image'] = $data['username'] . '_' . time() . '.' . $image->extension();
                    $destinationPath = public_path('/avatar');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 755, true);
                    }
                    $img = Image::make($image->path());
                    $img->fit(300, 300, function ($constraint) {
                        $constraint->upsize();
                    })->save($destinationPath . '/' . $data['image']);
                }
                if (!empty($data['address'])) {
                    $data['address'] = json_encode($data['address']);
                }
                $data['email_verified_at'] = now();
                $user = User::create($data);
                $user->assignRole($request->roles);
                DB::commit();
                $response = response()->json(['message' => 'Data has been save', 'redirect' => 'admin/users']);
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

    /**
     * Display the specified resource.
     */
    // public function show(string $id) {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        $data = Setting::whereIn('name', ['web_title', 'web_description'])->get();
        $identity = Setting::whereIn('name', ['logo_one', 'logo_two', 'title_one', 'title_two'])->get();
        $config = [
            'title' => $data[0]->value . ' - Edit Users',
            'description' => $data[1]->value,
            'first_logo' => $identity[0]->value,
            'second_logo' => $identity[1]->value,
            'first_title' => $identity[2]->value,
            'second_title' => $identity[3]->value
        ];
        $breadcrumbs = [
            ['disabled' => false, 'url' => 'admin', 'title' => 'Dashboard'],
            ['disabled' => true, 'url' => '#', 'title' => 'Edit'],
        ];
        $data = User::find($id);
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        $userRole = $data->roles->pluck('id');
        $userRole = $userRole->first();
        return view('admin.users.form', compact('config', 'breadcrumbs', 'data', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'position' => 'required|max:255',
            'roles' => 'required',
            'address' => 'nullable',
            'username' => 'required|min:3|max:255|unique:users,username,' . $id,
            'email' => 'required|unique:users,email,' . $id,
            'active' => 'required|between:0,1',
            'image' => 'image|mimes:jpg,png,jpeg',
        ], [
            'same' => ':attribute and :other must match.',
            'unique' => ':attribute is not available'
        ]);

        if (!$validator->fails()) {
            $data = $validator->safe()->except(['roles']);
            DB::beginTransaction(); 
            try {
                $user = User::find($id);
                $destinationPath = public_path('/avatar');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 755, true);
                };
                if (!empty($user->image) && file_exists($destinationPath . $user->image)) {
                    unlink($destinationPath . $user->image);
                };
                if (isset($data['image']) && !empty($data['image'])) {
                    $image = $data['image'];
                    $data['image'] = $data['username'] . '_' . time() . '.' . $image->extension();
                    $img = Image::make($image->path());
                    $img->fit(300, 300, function ($constraint) {
                        $constraint->upsize();
                    })->save($destinationPath . '/' . $data['image']);
                };
                if (!empty($data['address'])) {
                    $data['address'] = json_encode($data['address']);
                }
                $user->update($data);
                $user->roles()->detach();
                $user->assignRole($request->roles);
                DB::commit();
                $response = response()->json(['message' => 'Data has been save', 'redirect' => 'admin/users']);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $data = User::findOrFail($id);
        if ($data->delete()) {
            if (isset($data->image) && !empty($data->image)) {
                unlink(public_path('/avatar') . '/' . $data->image);
            }
            $response = response()->json(['status' => 'success', 'message' => 'Data has been delete']);
        } else {
            $response = response()->json(['status' => 'failed', 'message' => 'Data cant delete']);
        }
        return $response;
    }

    public function resetpassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if (!$validator->fails()) {
            $data = User::findOrFail($request->id);
            $req['password'] = Hash::make($data['username']);
            DB::beginTransaction();
            try {
                $data->update($req);
                DB::commit();
                $response = response()->json(['status' => 'success', 'message' => 'Password has been reset', 'redirect' => 'admin/users']);
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
}
