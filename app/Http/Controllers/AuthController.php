<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
  
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $error = $validator->errors()->toJson();
        if ($validator->fails()) {
            return response()->json(['error' => $error], 401);
        }
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }
        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
        ]);
        $error = $validator->errors()->toJson();
        if($validator->fails()){
            return response()->json(["message"=>$error], 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        $token = auth()->attempt($validator->validated());
        $new_token = $this->createNewToken($token);
        
        Auth::login($user); // Log in the user
        return $this->createNewToken($token);
        
    }


    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.*/
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.*/
   
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    public function getUserData(Request $request)
    {
      
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        
        ];

        return response()->json($userData);
    }
    public function history()
    {
        $ngrok="https://3e97-105-37-106-140.ngrok-free.app/";
        $url="{$ngrok}monkey%20pox%20detection/backEnd/public/images";
         if (!Auth::check()) {
            return response('Unauthorized', 401);
        }

        $user = Auth::user();
        $images = image::where('user_id', $user->id)->get();
        $imageUrls = $images->map(function ($image) use ($url) {
            $imageUrl = $url . '/' . $image->image;
            return [
                'status' => $image->status,
                'image' => $imageUrl,
            ];
        });
        header('Access-Control-Allow-Origin: *');
        return response()->json($imageUrls);
    }
    }
