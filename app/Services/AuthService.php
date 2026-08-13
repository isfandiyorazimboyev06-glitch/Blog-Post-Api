<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    /**
     * Handle user registration and return user with token
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return compact('user','token');
    }
    /**
     * Handle user authentication.
     */
    public function login(array $data): ?array
    {
        $user = User::where('email',$data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password))
        {
            return null;
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return compact('user','token');
    }
}
?>
