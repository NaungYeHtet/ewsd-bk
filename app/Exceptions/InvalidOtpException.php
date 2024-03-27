<?php

namespace App\Exceptions;

use App\Traits\ResponseHelper;
use Exception;

class InvalidOtpException extends Exception
{
    use ResponseHelper;

    public function render()
    {
        return $this->responseError('OTP is invalid, please try again', code: 400);
    }
}
