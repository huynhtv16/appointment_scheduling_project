<?php

namespace App\Http\Resources;

trait ApiResponse
{
    /**
     * response json success.
     *
     * @param $data
     * @param string $message
     * @param int $code
     */
    public function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * response json error.
     *
     * @param $data
     * @param string $message
     * @param int $code
     */
    public function error($message = 'Error', $code = 400, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
