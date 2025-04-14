<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
            'animal_id'         => ['required', 'integer', 'exists:animals,id'],

            'deleted_tag_ids'   => ['array'],
            'deleted_tag_ids.*' => ['integer', 'exists:tags,id'],

            'tags'              => ['array'],
            'tags.*.id'         => ['integer', 'exists:tags,id'],
            'tags.*.tag_type_id'   => ['required','integer', 'exists:tag_types,id'],                   
            'tags.*.factory_number'      => ['string'],
            'tags.*.number'      => ['required', 'string'],
            'tags.*.status'      => ['required', 'in:0,1'],
        ];
    }
}
