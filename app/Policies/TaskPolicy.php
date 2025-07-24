<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->role === 'manager' || $task->assigned_to_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'manager';
    }

    public function update(User $user): bool
    {
        return $user->role === 'manager';
    }

    public function updateStatus(User $user, Task $task): bool
    {
        return $user->role === 'manager' || $task->assigned_to_user_id === $user->id;
    }

    public function delete(User $user): bool
    {
        return $user->role === 'manager';
    }
}
