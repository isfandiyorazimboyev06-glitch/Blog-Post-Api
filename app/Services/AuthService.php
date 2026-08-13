<?php

namespace App\Services;

use App\Models\User;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    // Inject the UserRepository layer here
    public function __construct(
        protected UserRepository $userRepo
    ){}

    /**
     * Handle user registration and return user with token
     */
    public function register(array $data): array
    {
        // Service layer handles business data processing (hashing)
        $data['password'] = Hash::make($data['password']);

        // Repository layer handles saving to db
        $user = $this->userRepo->create($data);

        // Service layer performs secondary actions (token generation)
        $token = $user->createToken('myapptoken')->plainTextToken;

        return compact('user','token');
    }
    /**
     * Handle user authentication.
     */
    public function login(array $data): ?array
    {
        // Defer database lookup to the repository
        $user = $this->userRepo->findByEmail($data['email']);

        // Service layer handles validation check logic
        if (!$user || !Hash::check($data['password'], $user->password))
        {
            return null;
        }

        // Service layer performs secondary actions (token generation)
        $token = $user->createToken('myapptoken')->plainTextToken;

        return compact('user','token');
    }
}
?>
