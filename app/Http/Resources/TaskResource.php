<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'created_by_user' => new UserResource($this->created_by_user),
            'assigned_to_user' => new UserResource($this->assigned_to_user),
            'dependent_tasks' => TaskResource::collection($this->dependent_tasks), // tasks dependent on this task
        ];
    }
}
