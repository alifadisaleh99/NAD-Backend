<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateAnimalRequest extends BaseAnimalRequest
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
                'owner_type' => ['in:user,entity'],
                'owner_id'   => ['integer', 'exists:users,id'],
                'deleted_sensitivity_ids' => ['array'],
                'deleted_sensitivity_ids.*' => ['integer', 'exists:animal_sensitivities,id'],
                'deleted_pet_mark_ids' => ['array'],
                'deleted_pet_mark_ids.*' => ['integer', 'exists:pet_marks,id'],
                'deleted_media_ids' => ['array'],
                'deleted_media_ids.*' => ['integer', 'exists:media,id'],
                'photos' => ['required', 'array'],
                'photos.*' => ['required'],
            ]
        );
    }
}
