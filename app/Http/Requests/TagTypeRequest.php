<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagTypeRequest extends FormRequest
{
    public $method_post;

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
        $this->method_post  =  $this->isMethod('post');
       
        return [
            'name'           => ['required', 'array', translation_rule()],
            'icon'           => $this->method_post ? ['image'] : [''],
        ];
    }
}
