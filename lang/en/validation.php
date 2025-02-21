<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'exists' => 'The selected :attribute is invalid.',
    'required' => 'The :attribute field is required.',
    'numeric' => 'The :attribute must be a number.',
    'min' => [
        'array' => 'The :attribute must have at least :min items.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'max' => [
        'array' => 'The :attribute must not have more than :max items.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute must not be greater than :max.',
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'date_format' => 'The :attribute does not match the format :format.',
    'file' => 'The :attribute must be a file.',
    'string' => 'The :attribute must be a string.',
    'boolean' => 'The :attribute field must be true or false.',
    'regex' => 'The :attribute format is invalid.',
    'unique' => 'The :attribute has already been taken.',
    'array' => 'The :attribute must be an array.',
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'after' => 'The :attribute must be a date after :date.',
    'email' => 'The :attribute must be a valid email address.',
    'url' => 'The :attribute must be a valid URL.',
    'integer' => 'The :attribute must be an integer.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'before' => 'The :attribute must be a date before :date.',
    'password' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'mixed' => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'symbols' => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'uploaded' => 'The :attribute must not be greater than 2 megabytes.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'name',
        'image' => 'image',
        'category_id' => 'category id',
        'featured' => 'featured',
        'status' => 'status',
        'permissions' => 'permissions',
        'low_stock_limit' => 'low stock limit',
        'min_order_amount' => 'min order amount',
        'cash_back_percentage' => 'cash back percentage',
        'min_value_to_earn_cash_back' => 'the minimum order value',
        'transfer_limit' => 'transfer limit',
        'cash_back_expiry_period' => 'expiry period for cash back',
        'phone' => 'phone',
        'role_id' => 'role id',
        'type' => 'type',
        'full_name' => 'full name',
        'password' => 'password',
        'media_id' => 'media id',
        'linked_type' => 'linked type',
        'linked_id' => 'linked id',
        'external_link' => 'external link',
        'old_password' => 'old password',
        'start_date' => 'start date',
        'end_date' => 'end date',
        'operation_type' => 'operation type',
        'amount' => 'amount',
        'date' => 'date',
        'quantity' => 'quantity',
        'product_stock_id' => 'product stock id',
        'areas_ids' => 'areas_ids',
        


    ],
];
