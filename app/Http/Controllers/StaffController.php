<?php

namespace App\Http\Controllers;

use App\Data\StaffData;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreStaffRequest;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $request->validate([
            'username' => ['string'],
            'roles' => ['array', 'min:1', 'max:5'],
            'name' => ['string'],
            'email' => ['string'],
        ]);

        $staffs = Staff::query()
            ->when($request->has('search'), function (Builder $query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->when($request->has('name'), function (Builder $query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->when($request->has('email'), function (Builder $query) use ($request) {
                $query->where('email', $request->email);
            })
            ->when($request->has('username'), function (Builder $query) use ($request) {
                $query->where('username', $request->username);
            })
            ->when($request->has('roles'), function (Builder $query) use ($request) {
                $query->whereHas('roles', function (Builder $query) use ($request) {
                    $query->whereIn('name', $request->roles);
                });
            })
            ->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => StaffData::collect($staffs, PaginatedDataCollection::class)->include('role'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request)
    {
        $role = Role::where('name', $request->role)->first();

        if (! $role) {
            return $this->responseError('Role not found', code: 400);
        }

        $staff = DB::transaction(function () use ($request) {
            $fileName = '';

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $ext = $file->extension();
                $fileName = $file->storeAs('/images/avatars', uniqid().'.'.$ext, ['disk' => 'public']);
            }

            $staff = Staff::create([
                'name' => $request->name,
                'email1' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => $fileName,
            ]);

            $staff->refresh();

            $staff->assignRole($request->role);

            return $staff;
        }, 5);

        return $this->responseSuccess([
            'staff' => StaffData::from($staff),
        ], 'Staff created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        //
    }
}
