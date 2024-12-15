<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
/**
 * @OA\Info(
 *    title="News Api",
 *    version="1.0.0",
 * )
 */

class UserController extends Controller
{
    /**
     * Register a new user
     * 
     * @OA\Post(
     * path="/news_project/public/api/register",
     * operationId="Register",
     * tags={"User"},
     * summary="User Register",
     * description="User Register here",
     * @OA\RequestBody(
     * @OA\JsonContent(),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * type="object",
     * required={"name","email", "password", "password_confirmation"},
     * @OA\Property(property="name", type="text", example="Sadqain"),
     * @OA\Property(property="email", type="email", example="Sadqain@mail.com"),
     * @OA\Property(property="password", type="password", example="12345678"),
     * @OA\Property(property="password_confirmation", type="password", example="12345678")
     * ),
     * ),
     * ),
     * @OA\Response(
     * response=201,
     * description="Register Successfully",
     * @OA\JsonContent()
     * ),
     * @OA\Response(
     * response=422,
     * description="Unprocessable Entity",
     * @OA\JsonContent()
     * ),
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Registered Successfully', 'user' => $user], 201);
    }

    /**
     * Login user
     * 
     * @OA\Post(
     * path="/news_project/public/api/login",
     * operationId="Login",
     * tags={"User"},
     * summary="User Login",
     * description="User Login here",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * type="object",
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", example="Sadqain@mail.com"),
     * @OA\Property(property="password", type="string", example="12345678"),
     * ),
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Login Successfully",
     * @OA\JsonContent()
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized"
     * ),
     * )
     */


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    /**
     * @OA\Post(
     *     path="/news_project/public/api/logout",
     *     operationId="logoutUser",
     *     tags={"User"},
     *     summary="Logout user",
     *     description="Logout the authenticated user by invalidating their current access token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * @OA\Post(
     *     path="/news_project/public/api/send-password-link",
     *     operationId="sendPasswordLink",
     *     tags={"User"},
     *     summary="Reset Password",
     *     description="Send a password reset link to the user's email address.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="sadqainsaddy12@gmail.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset link sent")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to send password reset link",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unable to send password reset link")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */
    public function sendPasswordLink(Request $request)
    {
        // Validate the email input
        $request->validate(['email' => 'required|email']);

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // Generate a password reset token (using remember_token column)
        $token = Str::random(60);

        // Store the token in the remember_token field
        $user->update(['remember_token' => $token]);

        // Send the reset email
        Mail::to($request->email)->send(new PasswordResetMail($token));

        return response()->json(['message' => 'Password reset link sent']);
    }

    /**
    * * Profile
    * 
    * @OA\Get(
    *     path="/news_project/public/api/profile",
    *     operationId="getProfile",
    *     tags={"User"},
    *     summary="Get user profile",
    *     description="Retrieve user profile information.",
    *     security={{"bearerAuth":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     )
    * )
    *
    * @OA\SecurityScheme(
    *     securityScheme="bearerAuth",
    *     type="http",
    *     scheme="bearer",
    *     bearerFormat="JWT"
    * )
    */

    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()], 200);
    }


   
   /**
 * @OA\Post(
 *     path="/news_project/public/api/password-reset",
 *     operationId="resetPassword",
 *     tags={"User"},
 *     summary="Reset Password",
 *     description="Reset the user's password using a token sent to their email.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "token", "password", "password_confirmation"},
 *             @OA\Property(property="email", type="string", format="email", example="sadqainsaddy12@gmail.com"),
 *             @OA\Property(property="token", type="string", example="randomToken123"),
 *             @OA\Property(property="password", type="string", format="password", example="NewPassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password successfully reset",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password successfully reset")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token or email",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid token or email")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="The email field is required.")
 *         )
 *     )
 * )
 */
public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'password' => 'required|confirmed|min:8',
    ]);

    // Find the user by email and token (stored in remember_token)
    $user = User::where('email', $request->email)
                ->where('remember_token', $request->token)
                ->first();

    if (!$user) {
        return response()->json(['error' => 'Invalid token or email'], 400);
    }

    // Reset the password
    $user->password = Hash::make($request->password);
    $user->remember_token = null; // Clear the token after use
    $user->save();

    return response()->json(['message' => 'Password successfully reset']);
}

   
}