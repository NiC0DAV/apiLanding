<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Imagick;

class ImageController extends Controller
{
    public function uploadImage(Request $request){
        $payload = $request->all();

        $validateRequestData = Validator::make($payload, [
            'image' => 'file|mimes:jpg,png,gif,jpeg,webp|max:10240',
            'imageTitle' => 'string|required',
            'imageDescription' => 'string|required',
            'shortDescription' => 'string|required',
            'categoryId' => 'required'
        ]);

        if($request->hasFile('image') && !$validateRequestData->fails()){
            $completeFileName = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $filePrepName = str_replace(' ', '_', $fileName).'-'.rand().'_'.time().'.'.$extension;
            $path = $request->file('image')->storeAs('public/images', $filePrepName);

            $image = new Image;
            $image->imageTitle = $payload['imageTitle'];
            $image->imageDescription = $payload['imageDescription'];
            $image->shortDescription = $payload['shortDescription'];
            $image->categoryId = intval($payload['categoryId']);
            // $image->sliderStatus = intval($payload['sliderStatus']);
            // $image->imageStatus = intval($payload['imageStatus']);
            $image->pathImage = $filePrepName;

            $save = $image->save();

            if($save){
                header("HTTP/1.1 200 IMAGE  UPDATED");
                $serviceResponse = array(
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'The image has been uploaded successfully',
                    'data' => $image,
                    'date' => date('Y-m-d H:i:s')
                );
            }else{
                header("HTTP/1.1 404 USER CREATED");
                $serviceResponse = array(
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'The image could not be uploaded, try it again.',
                    'data' => $image,
                    'date' => date('Y-m-d H:i:s')
                );
            }

        }else{
            header("HTTP/1.1 404 USER CREATED");
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'The image could not be uploaded, check the data you just entered and try it again.',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchImages(){
        // $image = Image::where('imageStatus', '<>', '0')->get();
        $image = Image::select('images.id','categories.categoryName','images.imageTitle', 'images.imageDescription', 'images.imageTitle', 'images.shortDescription','images.categoryId','images.sliderStatus','images.imageStatus', 'images.pathImage')
        ->where('imageStatus', '<>', '0')
        ->join('categories', 'images.categoryId', '=', 'categories.id')
        ->get();

        $newImage = json_encode($image);
        $newImage = json_decode($newImage, true);

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $image
        );

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getImagesByCategory($id){
        // $images = Image::where('categoryId', $id)->get();
        $images = Image::select('images.id','categories.categoryName','images.imageTitle', 'images.imageDescription', 'images.imageTitle', 'images.shortDescription','images.categoryId','images.sliderStatus','images.imageStatus', 'images.pathImage')
        ->where('categoryId',$id)
        ->where('imageStatus', '<>', '0')
        ->join('categories', 'images.categoryId', '=', 'categories.id')
        ->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $images
        );

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getImageById($id){
        $images = Image::where('id', $id)->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $images
        );

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getImage($name){
        //http://127.0.0.1:8000/Storage/images/2-1957283293_1674436005.png
        $issetImage = Storage::disk('images')->exists($name);

        if($issetImage){
            $file = Storage::disk('images')->get($name);
            return new Response($file, 200);
        }else{
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'There was an error, the image does not exists.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);

    }

    public function updateImage(Request $request, $id){
        $payload = $request->all();

        $validateRequestData = Validator::make($payload, [
            'image' => 'file|mimes:jpg,png,gif,jpeg,webp|max:10240',
            'imageTitle' => 'string|required',
            'imageDescription' => 'string|required',
            'shortDescription' => 'string|required',
            'categoryId' => 'integer|required'
        ]);

        $validateErr = json_encode($validateRequestData->errors());

        $validateErrArr = json_decode($validateErr, true);

        $imageSearch = Image::where([
            'id' => $id
        ])->latest()->first();

        if(!$validateRequestData->fails()){
            if($request->hasFile('image')){
                $completeFileName = $request->file('image')->getClientOriginalName();
                $fileName = pathinfo($completeFileName, PATHINFO_FILENAME);
                $extension = $request->file('image')->getClientOriginalExtension();
                $filePrepName = str_replace(' ', '_', $fileName).'-'.rand().'_'.time().'.'.$extension;
                $path = $request->file('image')->storeAs('public/images', $filePrepName);

                $payloadArr['pathImage'] = $filePrepName;
            }

            $payload['imageStatus'] = $payload['imageStatus'] ? $payload['imageStatus'] : 1;
            $payload['sliderStatus'] = $payload['sliderStatus'] ? $payload['sliderStatus'] : 0;

            $payloadArr['imageTitle'] = $payload['imageTitle'];
            $payloadArr['imageDescription'] = $payload['imageDescription'];
            $payloadArr['shortDescription'] = $payload['shortDescription'];
            $payloadArr['categoryId'] = $payload['categoryId'];
            $payloadArr['sliderStatus'] = (int)$payload['sliderStatus'];
            $payloadArr['imageStatus'] = (int)$payload['imageStatus'];

            $update = Image::where('id', $id)->update($payloadArr);

            if($update){
                header("HTTP/1.1 200 IMAGE UPDATED");
                $serviceResponse = array(
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'The image has been updated successfully',
                    'data' => $payloadArr,
                    'date' => date('Y-m-d H:i:s')
                );
            }else{
                header("HTTP/1.1 404 ERROR UPDATING IMAGE");
                $serviceResponse = array(
                    'code' => 400,
                    'status' => 'Success',
                    'message' => 'The image could not be updated, try it again.',
                    'data' => $payloadArr,
                    'date' => date('Y-m-d H:i:s')
                );
            }

        } elseif ($validateRequestData->fails() && !$request->hasFile('image')) {
            $payload['imageStatus'] = $payload['imageStatus'] ? $payload['imageStatus'] : 1;
            $payload['sliderStatus'] = $payload['sliderStatus'] ? $payload['sliderStatus'] : 0;

            $payloadArr['imageTitle'] = $payload['imageTitle'];
            $payloadArr['imageDescription'] = $payload['imageDescription'];
            $payloadArr['shortDescription'] = $payload['shortDescription'];
            $payloadArr['categoryId'] = $payload['categoryId'];
            $payloadArr['sliderStatus'] = (int)$payload['sliderStatus'];
            $payloadArr['imageStatus'] = (int)$payload['imageStatus'];

            $update = Image::where('id', $id)->update($payloadArr);

            if($update){
                header("HTTP/1.1 200 IMAGE UPDATED");
                $serviceResponse = array(
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'The image has been updated successfully',
                    'data' => $payloadArr,
                    'date' => date('Y-m-d H:i:s')
                );
            }else{
                header("HTTP/1.1 404 ERROR UPDATING IMAGE");
                $serviceResponse = array(
                    'code' => 400,
                    'status' => 'Success',
                    'message' => 'The image could not be updated, try it again.',
                    'data' => $payloadArr,
                    'date' => date('Y-m-d H:i:s')
                );
            }
        }else{
            header("HTTP/1.1 404 IMAGE UPLOADED");
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'The image could not be updated, check the data you just entered and try it again.',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function deleteImage($id){
        $image = Image::where([
            'id' => $id
        ])->first();

        if(is_object($image) && $image['imageStatus'] == 1 || $image['imageStatus'] == 2){
            $newData['imageStatus'] = 0;
            $userUpdate = Image::where('id', $id)->update($newData);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'Image deleted successfully.'
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The image does not exists or has been already deleted.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

}
