<?php

namespace App\Http\Requests\API\Tasks;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaskRequest extends FormRequest
{
    use ApiResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        Gate::authorize('update', Task::class);
        Gate::authorize('create', Task::class);

        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date|date_format:Y-m-d',
            'status' => ['nullable', 'string', Rule::in(TaskStatusEnum::values())],
            'assigned_to_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::exists('users', 'id')->where('role', 'user')
            ],
            'dependent_on_task_ids' => 'nullable|array',
            'dependent_on_task_ids.*' => 'integer|exists:tasks,id'
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, $this->returnValidationError($validator));
    }
}
