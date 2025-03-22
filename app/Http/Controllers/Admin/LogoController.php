<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogoRequest;
use App\Http\Resources\LogoResource;
use App\Models\Logo;
use Illuminate\Validation\ValidationException;

class LogoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:logos.read|logos.write')->only('show');
        $this->middleware('permission:logos.write')->only('update');
    }
    
    /**
     * @OA\Get(
     * path="/admin/logo",
     * description="Get logo.",
     * operationId="show_logo",
     * tags={"Admin - Logo"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show()
    {
        $logo = Logo::first(); 

        if(!$logo)
        {
            $logo = Logo::create([
                'mobile_light_logo' => null,
                'mobile_dark_logo' =>  null,
                'light_logo' =>  null,
                'dark_logo' =>  null,    
            ]);
        }

        return response()->json(new LogoResource($logo), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/logo",
     * description="Edit logo.",
     *  tags={"Admin - Logo"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"type", "logo"},
     *              @OA\Property(property="mobile_light_logo", type="file"),
     *              @OA\Property(property="mobile_dark_logo", type="file"),
     *              @OA\Property(property="light_logo", type="file"),
     *              @OA\Property(property="dark_logo", type="file"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     * )
    */
    public function update(LogoRequest $request)
    {
        $logo = Logo::first();

        if(!$logo)
        {
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

        foreach ($logos as $key => $value)
        {
          if($request->$key){
             if($request->$key == $logo->$key){
                $logos[$key] = $logo->$key;
            }else{
                if(!is_file($request->$key))
                    throw ValidationException::withMessages([$key => __('error_messages.Logo should be a file')]);

                delete_file_if_exist($logo->$key);
                $logos[$key] = upload_file($request->$key, 'logos', 'logo');
            }
        }
    }
        $logo->update($logos);

        return response()->json(new LogoResource($logo), 200);
    }
}
