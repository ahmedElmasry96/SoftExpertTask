<?php

namespace App\Http\Requests\API\Tasks;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateTaskStatusRequest extends FormRequest
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
        return [
            'status' => ['required', 'string', Rule::in(TaskStatusEnum::values())],
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, $this->returnValidationError($validator));
    }
}
