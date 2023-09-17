<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => "required"
        ], [
            'email.required' => 'correo es campo requerido',
            'email.email' => 'El campor email debe ser un correo valido',
            'password.required' => 'password es requerida',
        ]);
        if($validation->fails()){
            $mensajes = collect($validation->errors()->messages())->flatten(1);
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => $mensajes
                ]
            ]);
        }
        $validateFields = $validation->validate();
        if (!$token = auth()->attempt($validateFields)) {
            //TODO: validar usuario existe para identificar si es el usuario o la contrasenia
            // return response()->json(['error' => 'Unauthorized'], 401);
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => [
                        "Password incorrect for: ". $validateFields['email']
                    ]
                ]
            ]);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'expires_in' => auth()->factory()->getTTL() * 60
        // ]);
        return response()->json([
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => [
                "token" => $token,
                "minutes_to_expire" => auth()->factory()->getTTL()
            ]
        ]);
    }
}
