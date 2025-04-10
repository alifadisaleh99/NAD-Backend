<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseAnimalRequest extends FormRequest
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
            'name'           => ['required', 'array', translation_rule()],
            'description'    => ['array', translation_rule()],
            'like'           => ['array', translation_rule()],
            'deslike'        => ['array', translation_rule()],
            'good_with'      => ['array', translation_rule()],
            'bad_with'       => ['array', translation_rule()],
            'sensitivities'  => ['array'],
            'sensitivities.*' => ['string'],

            'branch_id'      => ['required_if:owner_type,entity', 'integer', 'exists:branches,id'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id' => ['integer', 'exists:animal_types,id'],
            'animal_specie_id' => ['integer', 'exists:animal_species,id'],
            'animal_breed_id' => ['integer', 'exists:animal_breeds,id'],

            'pet_mark_ids'       => ['array'],
            'pet_mark_ids.*'     => ['integer', 'exists:pet_marks,id'],

            'primary_color_id'   => ['required', 'integer', 'exists:colors,id'],
            'secondary_color_id' => ['required', 'integer', 'exists:colors,id'],
            'tertiary_color_id'  => ['integer', 'exists:colors,id'],

            'age'    => ['in:young,adult,senior'],
            "weight" => ['min:0', 'numeric'],
            'gender' => ['required', 'in:male,female'],
            'size'   => ['in:small,medium,large'],
            'link'   => ['string'],
            'status' => ['required', 'in:1,0'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
