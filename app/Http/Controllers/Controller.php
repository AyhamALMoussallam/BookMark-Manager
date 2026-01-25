<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    // Standardized responses
    protected function notFound($resource = 'Resource')
    {
        return response()->json([
            'message' => "$resource not found"
        ], 404);
    }
    
    protected function unauthorized($message = 'Unauthorized')
    {
        return response()->json([
            'message' => $message
        ], 403);
    }
    
    protected function validationError($errors, $message = 'Validation failed')
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], 422);
    }
    
    protected function success($data = [], $message = 'Success', $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $status);
    }
    
    protected function created($data = [], $message = 'Created successfully')
    {
        return $this->success($data, $message, 201);
    }
}