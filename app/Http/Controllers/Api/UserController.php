<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\models\User;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user(); // Get the authenticated user
    
        if ($user->role === 'admin') {
            // Admin can see all users
            $users = User::all();
        } else {
            // Regular user can only see their own details
            $users = User::where('id', $user->id)->get();
        }
    
        return ApiResponse::success('Users retrieved successfully', [
            'users' => $users
        ], 200);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4'],
            'contact'=>['required','min:10','max:10','unique:users,contact'],
            'role'=> ['nullable', 'in:user,admin'] 
        ]);
    
        // If validation fails, return errors
        if ($validator->fails()) {
            return ApiResponse::error('validation failed',422,$validator->messages());
        }
    
        // Prepare data to create user
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'contact' => $request->contact,
            'role' =>  $request->role ?? 'user', // Default to 'user' if no role is provided
        ];
    
        DB::beginTransaction();
        try {
            // Attempt to create the user
            $user = User::create($data);
    
            // Generate a token for the newly created user
            $token = $user->createToken('auth_token')->accessToken;
    
            DB::commit();
            return ApiResponse::success('User created successfully', [
                'user' => $user,
                'access_token' => $token,
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();
    
            // Log detailed error message for debugging
            logger()->error('Error while creating user: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
    
            // Return a response with the error message
            logger()->error('Error while creating user: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $authenticatedUserId = auth()->id(); // Get the authenticated user's ID

        $user=User::find($id);
        if (is_null($user)){
            return ApiResponse::error('User Not Found',404);
        }
        elseif ($authenticatedUserId != $id) {
            return ApiResponse::error('Unauthorized access', 403); // Unauthorized if the IDs don't match
        }else{
            return ApiResponse::success('User Found',[
                'user_details'=>$user
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function login(Request $request)
    {
        // Validate the incoming request to ensure the necessary fields are present
        $request->validate([
            'contact' => 'required',
            'password' => 'required|string|min:4'
        ]);
    
        // Manually fetch user based on email
        $user = User::where('contact', $request->contact)->first();
    
        // Check if the user exists and if the password matches
        if (!$user) {
            return ApiResponse::error('User Not Found',401);
        }
    
        // Verify the password
        if (!Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Invalid Password',401);
        }
    
        // Create an access token
        $token = $user->createToken('auth_data')->accessToken;
    
        return ApiResponse::success('User Login Successfully',[
            'access_token' => $token
        ]);
    }

}
