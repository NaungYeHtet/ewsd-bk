<?php

namespace App\Http\Controllers;

use App\Data\StaffData;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return $this->responseSuccess([
            'result' => StaffData::from(Staff::find(auth()->id())),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $staff = DB::transaction(function () use ($request) {
            $staff = Staff::find(auth()->id());

            if ($request->hasFile('avatar')) {
                if ($staff->avatar) {
                    Storage::disk('public')->delete($staff->avatar);
                }

                $file = $request->file('avatar');
                $ext = $file->extension();
                $staff->avatar = $file->storeAs('/images/avatars', uniqid().'.'.$ext, ['disk' => 'public']);
            }

            $staff->name = $request->name;

            if ($request->has('password')) {
                $staff->password = bcrypt($request->password);
            }

            $staff->save();
            $staff->refresh();

            return $staff;
        }, 5);

        return $this->responseSuccess([
            'staff' => StaffData::from($staff),
        ], 'Profile updated successfully', 200);
    }
}
