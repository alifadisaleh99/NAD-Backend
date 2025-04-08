<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreAnimalRequest extends BaseAnimalRequest
{
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
        return array_merge(
            parent::rules(),
            [
                'owner_type' => ['required', 'in:user,entity'],
                'owner_id'   => ['required', 'integer', 'exists:users,id'],
                'photos'     => ['required', 'array'],
                'photos.*'   => ['image'],
            ]
        );
    }
}
