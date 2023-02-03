<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProspectController extends Controller
{
    public function prospectRegister(Request $request)
    {
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);
        $payloadArr['phoneNumber'] = !$payloadArr['phoneNumber'] ? '0' : $payloadArr['phoneNumber'];

        $validateRequestData = Validator::make($payloadArr, [
            'name' => 'required',
            'email' => 'required|email',
            'phoneNumber' => 'required',
            'message' => 'required'
        ]);
        
        if(!$validateRequestData->fails()){

            $prospect = new Prospect;
            $prospect->name = $payloadObj->name;
            $prospect->email = $payloadObj->email;
            $prospect->phoneNumber = $payloadArr['phoneNumber'];
            $prospect->message = $payloadObj->message;
            $prospect->save();

            Mail::to('esteveznicolas0@gmail.com')->send(new ContactMail($payloadArr));

            header("HTTP/1.1 200 USER CREATED");
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The information has been sent successfully.',
                'data' => $prospect
            );
        }else{
            header("HTTP/1.1 404 NOT FOUND");
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'We are really sorry, the information could not been sent, try it again.',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getProspects(){
        $prospects = Prospect::orderBy('created_at', 'desc')->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $prospects
        );     
        
        return response()->json($serviceResponse);
    }

}
