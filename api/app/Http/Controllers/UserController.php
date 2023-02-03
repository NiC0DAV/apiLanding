<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function userRegister(Request $request)
    {
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);
        
        if(!$validateRequestData->fails()){
            $hashedPass = Hash::make($payloadObj->password);

            $user = new User;
            $user->name = $payloadObj->name;
            $user->surname = $payloadObj->surname;
            $user->email = $payloadObj->email;
            $user->password = $hashedPass;
            $user->save();

            header("HTTP/1.1 200 USER CREATED");
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The user has been created successfully',
                'data' => $user
            );
        }else{
            header("HTTP/1.1 404 NOT FOUND");
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'The user could not be registered, the email has been already taken.',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(!$validateRequestData->fails()){
            $jwtGen = $jwtAuth->userLogin($payloadObj->email, $payloadObj->password);
            if (!empty($jwtGen) && $jwtGen != '' && empty($jwtGen['code'])) {
                $serviceResponse = array(
                    'code' => 200,
                    'accessToken' => $jwtGen,
                    'message' => 'Successful login, you are being redirected to the administration dashboard.',
                    'expirationTime' => "12 hrs",
                    'date' => date('Y-m-d H:i:s')
                );
            } else {
                $serviceResponse = array(
                    'code' => 404,
                    'message' => 'Invalid email or password, check the data and try it again.',
                    'traceId' => '109L',
                    'date' => date('Y-m-d H:i:s')
                );
            }
        }else{
            header("HTTP/1.1 404 NOT FOUND");
            $serviceResponse = array(
                'code' => 404,
                'traceId' => '102L',
                'status' => 'Error',
                'message' => 'There was an error logging you',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);

    }

    public function userUpdate($id, Request $request){

        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'rol' => 'required',
            'status' => 'required'
        ]);
        $user = User::where([
            'id' => $id
        ])->first();
        
        if(!$validateRequestData->fails() && is_object($user)){
            unset($payloadArr['id']);
            unset($payloadArr['created_at']);

            $payloadArr['password'] = Hash::make($payloadObj->password);
            User::where('id', $id)->update($payloadArr);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The user has been updated successfully.',
                'data' => $payloadArr
            );
        }else if($validateRequestData->fails() && $user->email == $payloadObj->email){
            unset($payloadArr['id']);
            unset($payloadArr['created_at']);
            $payloadArr['password'] = Hash::make($payloadObj->password);

            $userUpdate = User::where('id', $id)->update($payloadArr);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The user has been updated successfully.',
                'data' => $payloadArr
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'There was an error updating the user.',
                'data' => $validateRequestData->errors()
            );
        }


        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function userDelete($id){
        $user = User::where([
            'id' => $id
        ])->first();
        
        if(is_object($user) && $user['status'] == 1 || $user['status'] == 2){
            $newData['status'] = 0;
            $newData['rol'] = 0;
            $userUpdate = User::where('id', $id)->update($newData);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'User deleted successfully.'
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The user does not exists or has been already deleted.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchUser($id){
        $user = User::where([
            'id' => $id
        ])->first();

        if (is_object($user)) {
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'data' => $user
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The user that you are searching for does not exists.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchUsers(){
        $users = User::where('status', '<>', '0')->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $users
        );

        return response()->json($serviceResponse, $serviceResponse['code']);

    }
}
