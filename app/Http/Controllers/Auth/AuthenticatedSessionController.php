<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    use ResponseHelper;
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $staff = Staff::where('email', $request->email)->first();

        if (! $staff) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (!Hash::check($request->password, $staff->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $this->responseSuccess(data: [
            'token' => $staff->createToken('AUTH TOKEN')->plainTextToken,
            'staff' => new StaffResource($staff),
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
