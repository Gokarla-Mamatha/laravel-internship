<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function register(Request $request)
    {


        $validator = Validator::make(

            $request->all(),
            [
                'name' => ['required'],
                'email' => ['email', 'unique:users,email'],
                'password' => ['required', 'min:8', 'confirmed'],
                'password_confirmation' => ['required']
            ]
        );


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400); // or 422, your choice
        } else {

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'license_key' => $randomInt = rand(1000, 9999),
                'password' => md5($request->password)
            ];

            DB::beginTransaction();

            try {
                $result = User::create($data);
                $result->save();
                DB::Commit();
                $token = $result->createToken('access_token')->accessToken;
            } catch (\Exception $e) {
                DB::rollBack();
                // p($e->getMessage());
                echo '<pre>';
                print_r($e->getMessage());
                echo '</pre>';
                $result = null;
            }
            if ($result != null) {

                $user = User::with(['userLevel'])->find($result->id);

                return response()->json([
                    'status' => true,
                    'message' => 'User Register Successfully',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 3600,
                    'data' => $user,
                    'redirect_to' => '/homepage',
                    'is_existing_user' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Internal server error',
                    'status' => false,
                ], 500);
            }
        }
    }
    /**
     * Display the specified resource.
     */

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'country_code' => ['required'],
            'mobile' => ['required', 'numeric'],
            'password' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        } else {

            // Fetch user matching credentials
            $user = User::where('country_code', $request->country_code)
                ->where('mobile', $request->mobile)
                ->where('password', md5($request->password))
                ->first();

            if ($user) {
                // If using Passport and it's configured properly
                if (method_exists($user, 'createToken')) {
                    $token = $user->createToken('auth_token')->accessToken;
                } else {
                    $token = null; // or generate token your way if needed
                }

                if ($user) {
                    $usersubcription = SubscriptionPurchase::where('user_id', $user->id)->get();
                }
                return response()->json([
                    'status' => true,
                    'message' => 'User Login Successful.',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 3600,
                    'data' => $user,
                    'usersubcription' => [
                        'usersubcription' => $usersubcription ? $usersubcription : 'Purchase Details Not Found',
                    ],
                    'redirect_to' => '/homepage',
                    'is_existing_user' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'status' => 0
                ], 401);
            }
        }
    }
}