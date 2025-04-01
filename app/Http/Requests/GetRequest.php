<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
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
            'with_paginate'      => ['integer', 'in:0,1'],
            'per_page'           => ['integer', 'min:1'],
            'q'                  => ['string'],
            'animal_id'          =>  ['integer', 'exists:animals,id'],
            'owner_id'           =>  ['integer', 'exists:users,id'],
            'branch_type_id'     =>  ['integer', 'exists:branch_types,id'],
            'uaid'                 => ['string', 'exists:animals,uaid'],
            'tag_number'           => ['string', 'exists:tags,number'],
            'category_id'        => ['integer', 'exists:categories,id'],
            'animal_type_id'     => ['integer', 'exists:animal_types,id'],
            'animal_specie_id'   => ['integer', 'exists:animal_species,id'],
            'animal_breed_id'    =>  ['integer', 'exists:animal_breeds,id'],
            'branch_id'            =>  ['integer', 'exists:branches,id'],
            'entity_id'          => ['integer', 'exists:entities,id'],
            'search'        => ['string'],
            'role_id'        => ['exists:roles,id'],
            'status'              => ['integer', 'in:1,0'],
            'start_date'          => ['date_format:Y-m-d'],
            'end_date'            => ['date_format:Y-m-d'],
            'type'                => ['in:employee'],
            'user_is'             => ['in:single_user,entity_user'],
            'is_owner'              => ['integer', 'in:1,0'],
            'pet_status'              => ['string', 'in:found,lost,dead'],
            'primary_color_id'       => ['integer', 'exists:colors,id'],
            'secondary_color_id'       => ['integer', 'exists:colors,id'],
            'tertiary_color_id'       => ['integer', 'exists:colors,id'],          
             'gender'                => ['string', 'in:male,female'],
        ];
    }
}
