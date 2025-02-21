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
    'exists' => 'قيمة :attribute غير صالحة.',
    'required' => 'حقل :attribute اجباري.',
    'numeric' => ':attribute يجب أن يكون رقم.',
    'min' => [
        'numeric' => ':attribute يجب أن يكون على الأقل :min.',
    ],
    'max' => [
        'numeric' => ':attribute يجب أن يكون على الأكثر :max.',
    ],
    'date_format' => ':attribute لا يتوافق مع الصيغة التالية :format.',
    'file' => ':attribute يجب أن يكون ملف.',
    'string' => ':attribute يجب أن يكون سلسلة نصية.',
    'boolean' => ':attribute يجب أن يكون نعم أو لا.',
    'regex' => 'صيغة :attribute غير صالحة .',
    'unique' => ':attribute موجود مسبقاً.',
    'array' => ':attribute يجب أن يكون مصفوفة.',
    'image' => ':attribute يجب أن تكون صورة.',
    'in' => 'قيمة :attribute غير صالحة.',
    'mimes' => ':attribute يجب أن يكون ملف من نوع: :values.',
    'after' => ':attribute يجب أن يكون بعد :date.',
    'email' => ':attribute يجب أن يكون إيميل صحيح.',
    'url' => ':attribute يجب ان يكون رابط صحصح.',
    'integer' => ':attribute يجب أن تكون رقم صحيح.',
    'confirmed' => ':كلمة السر وتأكيدها غير متتطابقين.',
    'date' => ':attribute يجب أن يكون تاريخ.',
    'before' => ':attribute يجب أن يكون قبل :date.',
    'password' => [
        'min' => 'حقل :attribute يجب أن يكون من 6 محارف على الأقل',
        'letters' => 'The :attribute must contain at least one letter.',
        'mixed' => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'symbols' => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'phone' => [
        'size' => 'حقل :attribute يجب أن يكون من 8 خانات'
    ],
    'required_if' => 'حقل :attribute اجباري عندما :other تساوي :value.',
    'required_with' => 'حقل :attribute اجباري إذا كان :values موجود.',
    'required_without' => 'حقل :attribute غير اجباري إذا كان :values موجود.',
    'uploaded' => ':attribute يجب أن يكون على الأكثر 2 ميغابايت.',

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
        'name' => 'الاسم',
        'image' => 'الصورة',
        'category_id' => 'الصنف',
        'featured' => 'الشائع',
        'status' => 'الحالة',
        'permissions' => 'السماحيات',
        'low_stock_limit' => 'حد المخزن',
        'min_order_amount' => 'الحد الأدنى لمبلغ الطلب',
        'cash_back_percentage' => 'نسبة استرداد النقود',
        'min_value_to_earn_cash_back' => 'الحد الأدنى لقيمة الطلب',
        'transfer_limit' => 'حد النقل',
        'cash_back_expiry_period' => 'فترة انتهاء صلاحية استرداد النقود',
        'phone' => 'رقم الهاتف',
        'role_id' => 'الصلاحية',
        'type' => 'النوع',
        'full_name' => 'الاسم الكامل',
        'email' => 'الايميل',
        'password' => 'كلمة المرور',
        'media_id' => 'الميديا',
        'linked_type' => 'نوع الربط',
        'linked_id' => 'الجهة المطلوبة',
        'external_link' => 'اللينك الخارجي',
        'old_password' => 'كلمة المرور القديمة',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'operation_type' => 'نوع العملية',
        'amount' => 'القيمة',
        'date' => 'التاريخ',
        'quantity' => 'الكمية',
        'product_stock_id' => 'عنصر المخزن',
        'areas_ids' => 'المناطق',

        
    ],
];
