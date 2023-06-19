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
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // Store the image file
        $image = $request->file('image')->store('images', 'public');

        // Create the image record in the database
        $image = new image();
        $image->user_id = $user->id;
        $image->image = $image;
         $image->status = $request->status;
        $image->save();

        return response()->json(['message' => 'Image saved successfully'], 201);
    }
}
