<?php

namespace App\Services;

class UserService
{
    private array $users = [
        1 => ['id' => 1, 'name' => 'Alice'],
        2 => ['id' => 2, 'name' => 'Bob'],
    ];

    /**
     * Find user by ID
     */
    public function find(int $id): array|null
    {
        return $this->users[$id] ?? null;
    }
}
