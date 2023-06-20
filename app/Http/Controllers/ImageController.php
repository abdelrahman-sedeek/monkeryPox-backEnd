<?php

namespace App\Http\Controllers;
use App\Models\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class ImageController extends Controller
{
    public function storeImage(Request $request)
    {
        if (auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user(); // Assuming you have authentication set up and the authenticated user is creating the image
        
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        // Store the image file
        $imageReq = $request->file('image')->store('images', 'public');
        // dd($image);
        // Create the image record in the database
        $imageModel = new image();
        $imageModel->user_id = $user->id;
        $imageModel->image = $imageReq;
        $imageModel->status = $request->status;
        $imageModel->save();

        return response()->json(['message' => 'Image saved successfully'], 201);
    }
}
