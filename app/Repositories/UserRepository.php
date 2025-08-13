<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserStatus;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function findActiveById(int $id): ?User
    {
        return User::active()->find($id);
    }

    public function getActiveUsers(?string $name = null, int $excludeId = null)
    {
        return User::active()
                  ->searchByName($name)
                  ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
                  ->paginate(20);
    }
}