<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller
{

   /**
     * @OA\Post(
     *   path="/api/register",
     *   summary="register",
     *   description="register the user for login",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"firstname","lastname","email", "password", "password_confirmation"},
     *               @OA\Property(property="firstname", type="string"),
     *               @OA\Property(property="lastname", type="string"),
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="User successfully registered"),
     *   @OA\Response(response=401, description="The email has already been taken"),
     * )
     * It takes a POST request and required fields for the user to register
     * and validates them if it validated, creates those field including 
     * values in DataBase and returns success response
     *
     *@return \Illuminate\Http\JsonResponse
     */
    function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|between:2,100',
            'lastname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:150',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/login",
     *   summary="login",
     *   description="login",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="password", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="login Success"),
     *   @OA\Response(response=401, description="we can not find the user with that e-mail address You need to register first"),
     * )
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid credentials entered'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email is not registered',
            ], 402);
        } elseif (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong password'
            ], 403);
        }

        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
        ], 200);
    }


    /**
     * * @OA\Get(
     *   path="/api/logout",
     *   summary="logout",
     *   description="logout",
     *   @OA\RequestBody(
     *   @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"token"},
     *               @OA\Property(property="token", type="string"),
     *    ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="User successfully registered"),
     *   @OA\Response(response=401, description="The email has already been taken"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
 
    public function logout(Request $request)
    {

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ],501);
        }
    }


    /**
     * * @OA\Get(
     *   path="/api/getuser",
     *   summary="getuser",
     *   description="getuser",
     *   @OA\RequestBody(
     *    ),
     *   @OA\Response(response=201, description="Found User successfully"),
     *   @OA\Response(response=401, description="User cannot be found"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * getuser
     *
     * @return \Illuminate\Http\JsonResponse
     */
 
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }

    /**
     *  @OA\Post(
     *   path="/api/forgotPassword",
     *   summary="forgot password",
     *   description="forgot user password",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email"},
     *               @OA\Property(property="email", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Password Reset link is send to your email"),
     *   @OA\Response(response=400, description="we can not find a user with that email address"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This API Takes the request which is the email id and validates it and check where that email id
     * is present in DataBase or not, if it is not,it returns failure with the appropriate response code and
     * checks for password reset model once the email is valid and calling the function Mail::Send
     * by passing args and successfully sending the password reset link to the specified email id.
     *
     * @return success reponse about reset link.
     */

    public function forgotPassword(Request $request)
    {

        $email = $request->only('email');

        //validate email
        $validator = Validator::make($email, [
            'email' => 'required|email'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email is not registered',
            ], 402);
        } else {

            $token = JWTAuth::fromUser($user);
            $data = array('name' => "$user->firstname;", "resetlink" => $token);

            // Mail::send('mail', $data, function ($message) {
            //     $message->to(env('MAIL_USERNAME'), 'name')->subject('Reset Password');
            //     $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            // });

            Mail::send('mail', $data, function($message) {
                $message->to('mabbupremkumar@gmail.com', 'prem')->subject('Reset Password');
                $message->from('mabbupremkumar@gmail.com','Laravel');
             });
            return response()->json([
                'message' => 'Reset link Sent to your Email',
            ], 201);
        }
    }

    /**
     *   @OA\Post(
     *   path="/api/resetPassword",
     *   summary="reset password",
     *   description="reset user password",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"new_password","password_confirmation"},
     *               @OA\Property(property="new_password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Password reset successfull!"),
     *   @OA\Response(response=400, description="we can't find the user with that e-mail address"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This API Takes the request which has new password and confirm password and validates both of them
     * if validation fails returns failure resonse and if it passes it checks with DataBase whether the token
     * is there or not if not returns a failure response and checks the user email also if everything is
     * ok it will reset the password successfully.
     */

    public function resetPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:6|max:50',
            'password_confirmation' => 'required|same:new_password',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $currentUser = JWTAuth::authenticate($request->token);

        if (!$currentUser) {
            return response()->json([
                'message'=>'Invalid Authorization Token',], 401);
        } else {
            $user = User::updatePassword($currentUser, $request->new_password);
            return response()->json([
                'message' => 'Password Reset Successful'
            ], 201);
        }
    }
}










    