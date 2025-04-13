<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\VerificationCode;
use App\Models\User;

use App\Http\Resources\UserResource;
use App\Models\Entity;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout','get_profile','update_my_profile','delete_user']);
    }
    
    /**
     * @OA\Post(
     * path="/register",
     * tags={"User - Auth"},
     * description="User registration.",
     * operationId="User_Register",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="phone_country_id", type="integer"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="national_id", type="string"),
     *              @OA\Property(property="country_id", type="integer"),
     *              @OA\Property(property="summary", type="string"),
     *              @OA\Property(property="image", type="file"),
     *                )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function register(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'string'],
            'email'             => ['required', 'string', 'email', 'unique:users'],
            'phone_country_id'  => ['integer', 'exists:countries,id'],
            'phone'             => ['string', 'size:8', 'unique:users'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
            'country_id'        => ['integer', 'exists:countries,id'],
            'summary'           => ['string'],
            'image'             => ['image'],
            'national_id'       => ['string', 'min:3', 'unique:users'],
        ]);

        $image = null;
        if ($request->image)
            $image = upload_file($request->image, 'users', 'user');

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone_country_id'   => $request->phone_country_id,
            'phone'              => $request->phone,
            'country_id'         => $request->country_id,
            'summary'            => $request->summary,
            'password'           => Hash::make($request->password),
            'image'              => $image,
            'national_id'       => $request->national_id,
        ]);
        $user->assignRole('مستخدم');

        $token = $user->createToken('Sanctum', [])->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);

        return response()->json(null, 200);
    }

    /**
     * @OA\Post(
     * path="/entity-register",
     * tags={"User - Auth"},
     * description="Entity registration.",
     * operationId="Entity_Register",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]","email","password", "address", "contact_number", "founding_date"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="contact_number", type="string"),
     *              @OA\Property(property="founding_date", type="date"),
     *              @OA\Property(property="branch_type_id", type="integer"),
     *              @OA\Property(property="branch_type_name", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="national_id", type="string"),
     *              @OA\Property(property="image", type="file"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function entity_register(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'array', translation_rule()],
            'address'           => ['required', 'string'],
            'email'             => ['required', 'string', 'unique:users,email'],
            'contact_number'    => ['required', 'string'],
            'founding_date'     => ['required', 'date_format:Y-m-d'],
            'image'             => ['image'],
            'branch_type_id'    => ['integer', 'exists:branch_types,id'],
            'branch_type_name'  => ['string'],
            'national_id'   => ['string', 'min:3', 'unique:users'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $image = null;
        if($request->image)
             $image = upload_file($request->image, 'entities', 'entity');

        $entity = Entity::create([
            'name'              => $request->name,
            'address'           => $request->address,
            'email'             => $request->email,
            'founding_date'     => $request->founding_date,
            'contact_number'    => $request->contact_number,
            'image'             => $image,
            'branch_type_id'    => $request->branch_type_id,
            'branch_type_name'  => $request->branch_type_name,
        ]);

        $user = User::create([
            'entity_id'          => $entity->id,
            'name'               => $request->name['ar'],
            'email'              => $request->email,
            'phone'              => $request->contact_number,
            'password'           => Hash::make($request->password),
            'is_owner'           => 1,
            'national_id'       => $request->national_id,
        ]);
        $user->assignRole('مستخدم');

        $token = $user->createToken('Sanctum', [])->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);

        return response()->json(null, 200);
    }

    /**
     * @OA\Post(
     * path="/login",
     * description="Login by email and password",
     * operationId="authLogin",
     * tags={"User - Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"email","password"},
     *              @OA\Property(property="email", format="email" ,type="string"),
     *              @OA\Property(property="password", type="password"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required'],
            'password' => ['required', 'min:6'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember ?? false)) {
            $user = to_user(Auth::user());
            $token = $user->createToken('Sanctum', [])->plainTextToken;

            $user->load(['entity', 'branch']);

            $cookie = cookie(
                'auth_token',
                $token,
                60 * 24 * 7,
                '/',
                '.gaduid.com',
                true,
                true,
                false,
                'None'
            );

            return response()->json([
                'user' => new UserResource($user),
            ], 200)->withCookie($cookie);
        }

        return response()->json([
            'message' => 'email or password is incorrect.',
            'errors' => [
                'email' => ['email or password is incorrect.']
            ]
        ], 422);
    }

    /**
     * @OA\Get(
     * path="/user",
     * description="Get your profile",
     * operationId="get_profile",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *  ),
     *  )
    */
    public function get_profile(Request $request)
    {
        $user = to_user(Auth::user());
        $user->load('nationality', 'phone_country');

        return response()->json(new UserResource($user),200);
    }

    /**
     * @OA\Post(
     * path="/user",
     * description="Edit your profile",
     *  tags={"User - Auth"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="phone_country_id", type="integer"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="country_id", type="integer"),
     *              @OA\Property(property="summary", type="string"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="national_id", type="string"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *           )
     *       )
     *   ),
     *     @OA\Response(
     *         response="200",
     *    description="Success"
     *     ),
     * )
    */

    public function edit_profile(Request $request){
        $user = to_user(Auth::user());
        //TODO: required if the user is a shaer

        $request->validate([
            'name'                  => ['required', 'string'],
            'phone_country_id'      => ['integer', 'exists:countries,id'],
            'phone'                 => ['required', 'size:8', Rule::unique('users', 'phone')->ignore($user->id)],
            'country_id'            => ['integer', 'exists:countries,id'],
            'summary'               => ['string'],
            'image'                 => ['image'],
            'national_id'   => ['string', 'min:3', 'unique:users'],
            // 'email'               => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        
        $image = null;
        if($request->image)
            $image = upload_file($request->image, 'users', 'user');
        
        $user->name = $request->name;
        $user->phone_country_id = $request->phone_country_id;
        $user->phone = $request->phone;
        $user->country_id = $request->country_id;
        $user->summary = $request->summary;
        $user->image = $image;
        $user->national_id = $request->national_id;

        $user->save();

        return response()->json(new UserResource($user),200);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * description="Logout authorized user",
     * operationId="authLogout",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     *     ),
     * )
    */

    public function logout(Request $request)
    {
        $user = to_user(Auth::user());
        to_token($user->currentAccessToken())->delete();
    }

    
    /**
     * @OA\Delete(
     * path="/users/delete_account",
     * description="Delete my account",
     * operationId="delete_account",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Success"
     * )
     *)
    */
    public function delete_user()
    {
        $user = to_user(Auth::user());
        $user->delete();
        return response()->json(null, 204);
    }
}
