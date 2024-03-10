<?php

namespace App\Http\Controllers\Auth;

use App\Data\SidebarData;
use App\Data\StaffData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Staff;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use ResponseHelper;

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $staff = Staff::where('email', $request->email)->first();

        if ((bool) $staff->disabled_at) {
            throw ValidationException::withMessages([
                'email' => __('auth.disabled'),
            ]);
        }

        if (! $staff || ! Hash::check($request->password, optional($staff)->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $isFirstLogin = ! (bool) $staff->last_logged_in_at;

        $staff->update([
            'last_logged_in_at' => now(),
        ]);

        return $this->responseSuccess(data: [
            'token' => $staff->createToken('AUTH TOKEN')->plainTextToken,
            'staff' => StaffData::from($staff),
            // 'staff_with_avatar' => StaffData::from(Staff::whereNotNull('avatar')->first()),
            'sidebarData' => SidebarData::getData($staff),
            'isFirstLogin' => $isFirstLogin,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
