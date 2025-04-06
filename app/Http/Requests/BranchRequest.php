<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
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
    public function rules()
    {
        $is_post = request()->isMethod('post');

        $rules = [
            'address'           => ['required', 'string'],
        ];

        if ($is_post)
            $rules['entity_id'] = ['required', 'integer', 'exists:entities,id'];

        return $rules;
    }
}
