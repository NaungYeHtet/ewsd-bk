<?php

namespace App\Traits;

use App\Data\AcademicData;
use App\Data\SidebarData;
use App\Models\Academic;
use Illuminate\Support\Facades\Auth;

trait ResponseHelper
{
    protected function responseSuccess(array $data = [], string $message = 'Success', int $status = 200): \Illuminate\Http\JsonResponse
    {
        $data['message'] = $message;
        if (Auth::check()) {
            $data['currentAcademic'] = AcademicData::from(Academic::where('start_date', '<=', now())->where('final_closure_date', '>=', now())->first())->except('isActive');
        }

        return response()->json($data, $status);
    }

    protected function responseError(string $message = 'Internal Server Error', array $data = [], int $code = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}
