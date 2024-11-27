<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    /**  @return EloquentCollection<int, User> */
    public function index(): EloquentCollection
    {
        return User::all();
    }

    public function store(array $data): User
    {
        try {
            return User::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create User: ' . $e->getMessage());
        }
    }
}
