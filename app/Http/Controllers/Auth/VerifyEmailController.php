<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Providers\RouteServiceProvider;
use App\Services\OtpService;
use App\Traits\ResponseHelper;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    use ResponseHelper;
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->responseSuccess();
        }

        (new OtpService($request->user()->email))->verify(OtpAction::EMAIL_VERIFICATION, $request->code);

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->responseSuccess();
    }
}
