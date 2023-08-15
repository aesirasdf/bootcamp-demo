<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

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

}
