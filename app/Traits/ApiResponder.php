<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponder
{
    public function successResponse($message, $data, $code = Response::HTTP_OK)
    {

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function failureResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
        ], $code);
    }
}
