<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function responseBadRequest($validator){
        return response()->json(['ok' => false, 'errors' => $validator->errors(), 'message' => "Request didn't pass the validation."], 400);
    }

    protected function responseOk($data, $message = "Success!"){
        return response()->json(['ok' => true, 'data' => $data, 'message' => $message]);
    }
    
    protected function responseCreated($data, $message = "Created!"){
        return response()->json(['ok' => true, 'data' => $data, 'message' => $message], 201);
    }

    protected function responseNotFound($message = "Not found!"){
        return response()->json(['ok' => false, 'message' => $message], 404);
    }

    protected function responseUnauthorized($message = "Unauthorized!"){
        return response()->json(['ok' => false, 'message' => $message], 401);
    }

    protected function responseForbidden($permission, $message = "Forbidden!"){
        return response()->json(['ok' => false, 'message' => $message, 'permission' => $permission], 403);
    }

    protected function log(Request $request, $description, $properties, $table_name = null, $model = null){
        ActivityLog::create([
            "table_name" => $table_name,
            "model_id" => $model ? $model->id : null,
            "description" => $description,
            "user_id" => $request->user() ? $request->user()->id : null,
            "properties" => json_encode($properties),
            "ip" => $request->ip(),
            "user_agent" => $request->userAgent()
        ]);
    }

}
