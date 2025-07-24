<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDependency extends Model
{
    protected $fillable = ['task_id', 'dependent_on_task_id'];

    public function dependent_on_task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'dependent_on_task_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
