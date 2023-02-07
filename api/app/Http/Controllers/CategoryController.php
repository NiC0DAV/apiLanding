<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function registerCategory(Request $request){
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'categoryName' => 'string|required|unique:categories',
            'categoryDescription' => 'string|required'
        ]);

        if(!$validateRequestData->fails()){

            $category = new Category;
            $category->categoryName = $payloadArr['categoryName'];
            $category->categoryDescription = $payloadArr['categoryDescription'];
            $category->save();

            header("HTTP/1.1 200 CATEGORY CREATED");
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The category has been registered successfully',
                'data' => $category,
                'date' => date('Y-m-d H:i:s')
            );
        }else{
            $serviceResponse = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'The category could not be registered',
                'data' => $validateRequestData->errors(),
                'date' => date('Y-m-d H:i:s')
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function updateCategory($id, Request $request){
        $payload = $request->getContent();
        $payloadObj = json_decode($payload);
        $payloadArr = json_decode($payload, true);

        $validateRequestData = Validator::make($payloadArr, [
            'status' => 'string|required',
            'categoryName' => 'string|required|unique:categories',
            'categoryDescription' => 'string|required'
        ]);

        $validateErr = json_encode($validateRequestData->errors());

        $validateErrArr = json_decode($validateErr, true);

        $category = Category::where([
            'id' => $id
        ])->latest()->first();

        if(is_object($category) && (!$validateRequestData->fails())){

            Category::where('id', $id)->update($payloadArr);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The category has been updated successfully.',
                'data' => $payloadArr
            );
        }elseif(count($validateErrArr) == '1' && array_key_exists('categoryName', $validateErrArr) && $category->categoryName == $payloadObj->categoryName){

            Category::where('id', $id)->update($payloadArr);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'The category has been updated successfully.',
                'data' => $payloadArr,
                'validation' => $validateErr
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'There was an error updating the category.',
                'data' => $validateRequestData->errors()
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);

    }

    public function deleteCategory($id){
        $category = Category::where([
            'id' => $id
        ])->first();

        if(is_object($category) && $category['status'] == 1 || $category['status'] == 2){
            $newData['status'] = 0;
            $userUpdate = Category::where('id', $id)->update($newData);

            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'message' => 'Category deleted successfully.'
            );
        }else{
            $serviceResponse = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'The Category does not exists or has been already deleted.'
            );
        }

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchAllCategories(){
        $category = Category::where('status', '<>', '0')->get();

        $serviceResponse = array(
            'code' => 200,
            'status' => 'Success',
            'data' => $category
        );

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function fetchCategoryById($id){
        $category = Category::where([
            'id' => $id
        ])->first();

        if (is_object($category)) {
            $serviceResponse = array(
                'code' => 200,
                'status' => 'Success',
                'data' => $category
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
}
