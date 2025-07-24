<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'due_date', 'assigned_to_user_id', 'created_by_user_id', 'status',
    ];

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assigned_to_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function dependent_tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependent_on_task_id', 'task_id');
    }

    public function parent_tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependent_on_task_id');
    }

}
