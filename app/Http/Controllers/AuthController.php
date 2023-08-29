<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "password" => "required"
        ]);

        if($validator->fails()){
            $this->ResponseBadRequest($validator);
        }
        $validated = $validator->validated();
        $key = filter_var($validated['name'], FILTER_VALIDATE_EMAIL) ? "email" : "name";

        if(auth()->attempt([
            $key => $validated['name'],
            "password" => $validated['password']
        ])){
            //if success ...
            $user = auth()->user();
            $user->profile;
            $token = $user->createToken("library-api")->accessToken;
            $user->token = $token;

            return $this->ResponseOk($user, "Login Success!");
        }
        else{
            //if fails
            return $this->responseUnauthorized();
        }
    }

    public function register(Request $request){
        if(User::all()->count() === 0){
            $validator = Validator::make($request->all(), [
                "name" => ["required", "min:4", "alpha_num", "max:32", "unique:users"],
                "email" => "required|email|max:64|unique:users",
                "password" => "required|min:8|confirmed|max:64",
                "firstname" => "required|max:64|regex:/^[a-z ,.'-]+$/i",
                "middlename" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
                "lastname" => "required|max:64|regex:/^[a-z ,.'-]+$/i",
                "gender" => "required|min:0|max:3",
                "birthdate" => "sometimes|date|before:" . now()->addDays(1)
            ]);
            if($validator->fails()){
                return response()->json([
                    'ok' => false,
                    'message' => "Request didn't pass the validation!",
                    'errors' => $validator->errors()
                ], 400);
            }
            $validated = $validator->safe()->only('name', 'email', 'password');
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password'])
            ]);
            $user->profile()->create($validator->safe()->except('name', 'email', 'password'));
            $user->profile;
            $user->token = $user->createToken('library-api')->accessToken;
            return $this->ResponseCreated($user, "Registered!");
        }

        return $this->responseUnauthorized();
    }
}
