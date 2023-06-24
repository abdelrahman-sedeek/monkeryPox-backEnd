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
        if (!auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user(); // Assuming you have authentication set up and the authenticated user is creating the image
        
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        // Store the image file
        // $imageReq = $request->file('image')->store('images', 'public');
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);

            $imageModel = new image();
            $imageModel->user_id = $user->id;
            $imageModel->image = $imageName;
            $imageModel->status = $request->status;
            $imageModel->save();
            return response()->json(['image_path' => '/images/' . $imageName]);
        } 
        return response()->json(['error' => 'No image file uploaded.'], 400);
        // dd($image);
        // Create the image record in the database
       

    }
}
