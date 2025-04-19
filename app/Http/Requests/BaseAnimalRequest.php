<?php

namespace App\Http\Requests;

use App\Models\Tag;
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

            'branch_id'      => ['integer', 'exists:branches,id'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'animal_specie_id' => ['integer', 'exists:animal_species,id'],
            'animal_breed_id' => ['integer', 'exists:animal_breeds,id'],

            'pet_mark_ids'       => ['array'],
            'pet_mark_ids.*'     => ['integer', 'exists:pet_marks,id'],

            'primary_color_id'   => ['required', 'integer', 'exists:colors,id'],
            'secondary_color_id' => ['integer', 'exists:colors,id'],
            'tertiary_color_id'  => ['integer', 'exists:colors,id'],

            'age'    => ['in:young,adult,senior'],
            "weight" => ['min:0', 'numeric'],
            'gender' => ['required', 'in:male,female'],
            'size'   => ['in:small,medium,large'],
            'link'   => ['string'],
            'status' => ['required', 'in:1,0'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'digital_link' => ['string'],
            'generate_public' => ['in:1,0'],
            'ownership_date' => ['date', 'before_or_equal:today'],

            'attachments' => ['array'],
            'attachments.*.name' => ['string'],
            'attachments.*.attachments_date' => ['date'],
            'attachments.*.source' => ['string'],

            'vaccinations' => ['array'],
            'vaccinations.*.name' => ['string'],
            'vaccinations.*.vaccinations_date' => ['date'],
            'vaccinations.*.duration' => ['integer'],
            'vaccinations.*.is_expired' => ['in:0,1'],

            'tags'              => ['array'],
            'tags.*.tag_type_id'   => ['required','integer', 'exists:tag_types,id'],
            'tags.*.factory_number'      => ['string'],
            'tags.*.number'      => ['required', 'string'],
            'tags.*.status'      => ['required', 'in:0,1'],

            'photos' => ['array'],
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tags = $this->input('tags');
            $deleted_tag_ids = $this->input('deleted_tag_ids', []);

            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    if (isset($tag['number'])) {
                        $query = Tag::where('number', $tag['number']);

                        if (!empty($deleted_tag_ids)) {
                            $query->whereNotIn('id', $deleted_tag_ids);
                        }

                        if (isset($tag['id'])) {
                            $query->where('id', '!=', $tag['id']);
                        }

                        if ($query->exists()) {
                            $validator->errors()->add('number', __('error_messages.number_used', ['number' => $tag['number']]));
                        }
                    }
                }
            }
        });
    }
}
