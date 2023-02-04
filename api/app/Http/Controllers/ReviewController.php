<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function reviewRegister(Request $request)
    {
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'name' => 'required',
            'email' => 'required|email',
            'description' => 'required',
            'workDone' => 'required',
            'rating' => 'required'
        ]);

        if(!$validateRequestData->fails()){

            $Review = new Review;
            $Review->name = $payloadObj->name;
            $Review->email = $payloadObj->email;
            $Review->description = $payloadObj->description;
            $Review->workDone = $payloadObj->workDone;
            $Review->ipLocation = $request->ip();
            $Review->rating = $payloadObj->rating;
            $Review->save();

            // Mail::to('esteveznicolas0@gmail.com')->send(new ContactMail($payloadArr));

            header("HTTP/1.1 200 REVIEW CREATED");
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The review has been created successfully.',
                'data' => $Review
            );
        }else{
            header("HTTP/1.1 404 NOT FOUND");
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'We are really sorry, the review could not be created, try it again.',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchReviews(){
        $reviews = Review::where('reviewStatus', '<>', '0')->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $reviews
        );

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function reviewUpdate($id, Request $request){

        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'reviewStatus' => 'required'
        ]);

        $reviews = Review::where([
            'id' => $id
        ])->first();

        if(!$validateRequestData->fails() && is_object($reviews)){
            unset($payloadArr['id']);
            unset($payloadArr['created_at']);

            Review::where('id', $id)->update($payloadArr);

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

    public function reviewDelete($id){
        $review = Review::where([
            'id' => $id
        ])->first();

        if(is_object($review) && $review['reviewStatus'] == 1 || $review['reviewStatus'] == 2){
            $newData['reviewStatus'] = 0;
            $userUpdate = Review::where('id', $id)->update($newData);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'Review deleted successfully.'
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The review does not exists or has been already deleted.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }
}
