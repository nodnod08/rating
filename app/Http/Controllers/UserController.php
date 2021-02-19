<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => 'Validation errors', 'errors' =>  $validator->errors(), 'status' => false], 422);
        }
        $input = $request->all();
        $user = User::whereEmail($input['email'])->first();

        if(is_null($user)) {
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
        
            /**Take note of this: Your user authentication access token is generated here **/
            $data['token'] =  $user->createToken('MyApp')->accessToken;
            $data['name'] =  $user->name;

            return response(['data' => $data, 'message' => 'Account created successfully!', 'status' => true], 200);
        } else {
            return response(['data' => [], 'message' => 'Email is already registered!', 'status' => false], 422);
        }
    }

    public function login(Request $request) {
        $user = User::where('email', $request->email)->first();

        if ($user) {

            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token, 'user' => $user, 'message' => 'Login Successfuly', 'success' => true];
                return response($response, 200);
            } else {
                $response = ['token' => $token, 'user' => $user, 'message' => 'Incorrect credentials', 'success' => false];
                return response($response, 422);
            }

        } else {
            $response = 'User does not exist';
            return response($response, 422);
        }
    }

    public function logout(Request $request) {
            DB::table('oauth_access_tokens')
            ->where('user_id', $request->user()->id)
            ->update([
                'revoked' => true
            ]);
            $data['success'] = true;
            $data['message'] = "User logout";
            return response($data);
    }
}