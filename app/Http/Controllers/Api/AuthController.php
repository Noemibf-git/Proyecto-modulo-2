<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;



class AuthController extends Controller
{
    #[OA\Post(path: '/api/login', summary: 'Iniciar sesión', tags: ['Autenticación'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password'),
    ]))]
    #[OA\Response(response: 200, description: 'Login correcto')]
    #[OA\Response(response: 401, description: 'Credenciales incorrectas')]
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email) -> first();
    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id' => $user->id,
            'username' => $user->usermane,
            'email' => $user->email,
            'role' => $user->role,
        ]
    ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    #[OA\Post(path: '/api/register', summary: 'Registrar usuario', tags: ['Autenticación'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'username', type: 'string', example: 'Maria'),
        new OA\Property(property: 'email', type: 'string', example: 'maria@ejemplo.com'),
        new OA\Property(property: 'password', type: 'string', example: 'contraseña123'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: 'contraseña123'),
    ]))]
    #[OA\Response(response: 201, description: 'Usuario registrado')]
    public function register(Request $request)
{
    $data = $request->validate([
        'username' => 'required|string|max:255|unique:users',
        'email'    => 'required|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'username' => $data['username'],
        'email'    => $data['email'],
        'password' => bcrypt($data['password']),
        'role'     => 'user',
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
            'role'     => $user->role,
        ],
    ], 201);
}
}