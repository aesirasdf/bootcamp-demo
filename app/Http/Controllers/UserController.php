<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request){
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
        return response()->json([
            'ok' => true,
            'message' => "User has been created!",
            'data' => $user
        ], 201);
    }

    public function index(){
        return response()->json([
            'ok' => true, 
            'data' => User::all(), 
            'message' => 'Users has been retrieved.'
        ], 200);
    }

    public function update(Request $request, User $user){
        $validator = Validator::make($request->all(), [
            "name" => ["sometimes", "min:4", "alpha_num", "max:32", "unique:users,name," . $user->id],
            "email" => "sometimes|email|max:64|unique:users,email," . $user->id,
            "password" => "sometimes|min:8|confirmed|max:64",
            "firstname" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "middlename" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "lastname" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "gender" => "sometimes|min:0|max:3",
            "birthdate" => "sometimes|date|before:" . now()->addDays(1)
        ]);
        
        if($validator->fails()){
            return response()->json([
                'ok' => false,
                'message' => "Request didn't pass the validation!",
                'errors' => $validator->errors()
            ], 400);
        }
        $validated = $validator->safe()->only("name", "email", "password");
        if(isset($validated['password'])){
            $validated['password'] = bcrypt($validated['password']);
        }
        $user->update($validated);
        $user->profile->update($validator->safe()->except("name", "email", "password"));

        return response()->json([
            'ok' => true,
            'data' => $user,
            'message' => 'User has been updated.'
        ]);
    }

    public function destroy(User $user){
        $user->delete();
        return response()->json([
            'ok' => true,
            'message' => 'User has been deleted.'
        ]);
    }

}
