<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Create a new user in the database.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email',$email)->first();
    }
}


?>
