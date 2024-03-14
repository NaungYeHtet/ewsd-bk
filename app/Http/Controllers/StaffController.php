<?php

namespace App\Http\Controllers;

use App\Data\StaffData;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Department;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
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
            'results' => StaffData::collect($staffs, PaginatedDataCollection::class)->include('role', 'department'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request)
    {
        $staff = DB::transaction(function () use ($request) {
            $fileName = '';

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $ext = $file->extension();
                $fileName = $file->storeAs('/images/avatars', uniqid().'.'.$ext, ['disk' => 'public']);
            }

            $staff = Staff::create([
                'department_id' => Department::findBySlug($request->department),
                'name' => $request->name,
                'email' => $request->email,
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
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $staff = DB::transaction(function () use ($request, $staff) {
            if ($request->hasFile('avatar')) {
                Storage::disk('public')->delete($staff->avatar);

                $file = $request->file('avatar');
                $ext = $file->extension();
                $staff->avatar = $file->storeAs('/images/avatars', uniqid().'.'.$ext, ['disk' => 'public']);
            }

            $staff->name = $request->name;
            $staff->department_id = Department::findBySlug($request->department)->id;

            if ($request->has('password')) {
                $staff->password = bcrypt($request->password);
                $staff->tokens()->delete();
            }

            $staff->save();

            if (! $staff->hasRole($request->role)) {
                $staff->syncRoles([$request->role]);
                $staff->tokens()->delete();
            }
            $staff->refresh();

            return $staff;
        }, 5);

        return $this->responseSuccess([
            'staff' => StaffData::from($staff),
        ], 'Staff updated successfully', 200);
    }

    public function disable(Staff $staff)
    {
        $staff->update([
            'disabled_at' => now(),
        ]);

        $staff->tokens()->delete();

        return $this->responseSuccess([
            'staff' => StaffData::from($staff->refresh()),
        ], 'Staff disabled successfully', 200);
    }

    public function enable(Staff $staff)
    {
        $staff->update([
            'disabled_at' => null,
        ]);

        return $this->responseSuccess([
            'staff' => StaffData::from($staff->refresh()),
        ], 'Staff enabled successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        abort(404);
    }
}
