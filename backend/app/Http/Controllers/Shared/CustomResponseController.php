<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CustomResponseController extends Controller
{
    public function response($code, $message, $data): JsonResponse
    {
        return response()->json($this->getResponse($code, $message, $data));
    }

    public function getResponse($code, $message, $data): array
    {
        return ['status' => ['code' => $code, 'message' => $message], 'data' => $data];
    }
}
