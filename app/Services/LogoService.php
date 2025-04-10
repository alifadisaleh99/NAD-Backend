<?php

namespace App\Services;

use App\Models\Logo;
use Illuminate\Validation\ValidationException;

class LogoService
{
    public function show()
    {
        $logo = Logo::first();

        if (!$logo) {
            $logo = Logo::create([
                'mobile_light_logo' => null,
                'mobile_dark_logo' =>  null,
                'light_logo' =>  null,
                'dark_logo' =>  null,
            ]);
        }

        return $logo;
    }

    public function update($request)
    {
        $logo = Logo::first();

        if (!$logo) {
            $logo = Logo::create([
                'mobile_light_logo' => null,
                'mobile_dark_logo' =>  null,
                'light_logo' =>  null,
                'dark_logo' =>  null,
            ]);
        }

        $logos = [
            'mobile_light_logo' => null,
            'mobile_dark_logo' => null,
            'light_logo' => null,
            'dark_logo' => null,
        ];

        foreach ($logos as $key => $value) {
            if ($request->$key) {
                if ($request->$key == $logo->$key) {
                    $logos[$key] = $logo->$key;
                } else {
                    if (!is_file($request->$key))
                        throw ValidationException::withMessages([$key => __('error_messages.Logo should be a file')]);

                    delete_file_if_exist($logo->$key);
                    $logos[$key] = upload_file($request->$key, 'logos', 'logo');
                }
            }
        }
        $logo->update($logos);

        return $logo;
    }
}
