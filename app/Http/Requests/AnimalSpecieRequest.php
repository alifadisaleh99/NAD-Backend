<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalSpecieRequest extends FormRequest
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
        return [
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'    => ['required', 'integer', 'exists:animal_types,id'],
            'name'              => ['required', 'array', translation_rule()],
            'image'           => request()->isMethod('post') ? ['image'] : [''],
        ];
    }
}
