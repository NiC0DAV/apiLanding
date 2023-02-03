<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Console\View\Components\Info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InformationController extends Controller
{
    
    public function registerWebInformation(Request $request){
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);
        $validateRequestData = Validator::make($payloadArr, [
            'name' => 'string|required|unique:information',
            'welcomeSection' => 'array|required',
            'servicesSection' => 'array|required',
            'CharacteristicsSection' => 'array|required',
            'footerSection' => 'array|required',
            'facebookUrl' => 'string|required',
            'instagramUrl' => 'string|required',
            'whatsappNumber' => 'string|required'
        ]);

        // try {
            if(!$validateRequestData->fails()){

                $information = new Information;
                $information->name = $payloadArr['name'];
                $information->welcomeSection = $payloadArr['welcomeSection']; 
                $information->servicesSection = $payloadArr['servicesSection']; 
                $information->CharacteristicsSection = $payloadArr['CharacteristicsSection']; 
                $information->footerSection = $payloadArr['footerSection']; 
                $information->facebookUrl = $payloadArr['facebookUrl']; 
                $information->instagramUrl = $payloadArr['instagramUrl']; 
                $information->whatsappNumber = $payloadArr['whatsappNumber']; 
                $information->save();
    
                header("HTTP/1.1 200 USER CREATED");
                $serviceResponse = array(
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'The information has been registered successfully',
                    'data' => $information,
                    'date' => date('Y-m-d H:i:s')
                );
    
            }else{
                $serviceResponse = array(
                    'code' => 404,
                    'status' => 'Error',
                    'message' => 'The information could not be registered',
                    'data' => $validateRequestData->errors(),
                    'date' => date('Y-m-d H:i:s')
                );
            }
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     $serviceResponse = array(
        //         'code' => 404,
        //         'status' => 'Error',
        //         'message' => 'The information could not be registered',
        //         'data' => $validateRequestData->errors(),
        //         'date' => date('Y-m-d H:i:s')
        //     );
        // }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchWebInformation($id){
        $information = Information::where([
            'id' => $id
        ])->first();

        if (is_object($information)) {
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'data' => $information
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The web information that you are searching for does not exists.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchAllWebInformation(){
        $information = Information::where('status', '<>', '0')->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $information
        );

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function updateWebInformation($id, Request $request){
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'name' => 'string|required|unique:information',
            'status' => 'string|required',
            'welcomeSection' => 'array|required',
            'servicesSection' => 'array|required',
            'CharacteristicsSection' => 'array|required',
            'footerSection' => 'array|required',
            'facebookUrl' => 'string|required',
            'instagramUrl' => 'string|required',
            'whatsappNumber' => 'string|required'
        ]);

        $validateErr = json_encode($validateRequestData->errors());

        $validateErrArr = json_decode($validateErr, true);

        $information = Information::where([
            'id' => $id
        ])->latest()->first();

        if($payloadObj->status == '2'){
            $statusQuantity = Information::where([
                'status' => '2'
            ])->first();
            
            if(is_object($statusQuantity) && $statusQuantity->id != $id){
                $serviceResponse = array(
                    'code' => 400,
                    'status' => 'Error',
                    'message' => 'There is currently a web information with status active, you have to disable it if you want to change it.'
                );
            }elseif (!$validateRequestData->fails() && is_object($information)) {
                    unset($payloadArr['id']);
                    unset($payloadArr['created_at']);
                    Information::where('id', $id)->update($payloadArr);
        
                    $serviceResponse = array(
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'The information has been updated successfully.',
                        'data' => $payloadArr
                    );
                }else if(count($validateErrArr) == '1' && array_key_exists('categoryName', $validateErrArr) && $information->name == $payloadObj->name){
                    unset($payloadArr['id']);
                    unset($payloadArr['created_at']);
                    Information::where('id', $id)->update($payloadArr);
        
                    $serviceResponse = array(
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'The information has been updated successfully.',
                        'data' => $payloadArr
                    );
                }else{
                    $serviceResponse = array(
                        'code' => 400,
                        'status' => 'Error',
                        'message' => 'There was an error updating the information.',
                        'data' => $validateRequestData->errors()
                    );
                }
        }elseif ($payloadObj->status != '2' && !$validateRequestData->fails() && is_object($information)) {
            unset($payloadArr['id']);
            unset($payloadArr['created_at']);
            Information::where('id', $id)->update($payloadArr);
    
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The information has been updated successfully.',
                'data' => $payloadArr
            );
        }else if(count($validateErrArr) == '1' && array_key_exists('categoryName', $validateErrArr) && $information->name == $payloadObj->name){
            unset($payloadArr['id']);
            unset($payloadArr['created_at']);
            Information::where('id', $id)->update($payloadArr);
    
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The information has been updated successfully.',
                'data' => $payloadArr
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'There was an error updating the information.',
                'data' => $validateRequestData->errors()
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function deleteWebInformation($id){
        $information = Information::where([
            'id' => $id
        ])->first();
        
        if(is_object($information) && $information['status'] == 1 || $information['status'] == 2){
            $newData['status'] = 0;
            $userUpdate = Information::where('id', $id)->update($newData);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'Information deleted or deactivated successfully.'
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The Information does not exists or has been already deleted.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }
}
