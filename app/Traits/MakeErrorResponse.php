<?php

namespace App\Traits;

use Flugg\Responder\Contracts\Responder;
use Flugg\Responder\Http\Responses\ErrorResponseBuilder;

trait MakeErrorResponse
{
    /**
     * Build an error response.
     *
     * @param  mixed|null  $errorCode
     * @param  string|null $message
     * @return \Flugg\Responder\Http\Responses\ErrorResponseBuilder
     */
    public function errorTrans($errorCode = null, $parse): ErrorResponseBuilder
    {
        if (is_array($parse)) {
            $parse = trans('errors.' . $errorCode, $parse);
        }

        return app(Responder::class)->error(...func_get_args());
    }
}
