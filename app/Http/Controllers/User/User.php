<?php

namespace App\Http\Controllers\User;

use App\goHoltz\API\Response as API;
use App\Http\Controllers\Controller;
use App\Models\AccessTokens as mAccessTokens;
use App\Models\User as mUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class User extends Controller
{
    /**
     * Token expiration in hours
     * 
     * Default: 7 days
     * 
     * @var int
     */
    const TOKEN_EXPIRATION_HOURS = 7 * 24;

    /**
     * @var array<string, mixed>
     */
    protected array $fields = [];

    /**
     * Constructor
     * 
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
        $this->fields = $this->request->all();
    }

    /**
     * User Authentication
     * 
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        if (($params = API::validate([
            'name' => ['required', 'string', 'min:4', 'max:128', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'email' => ['nullable', 'email', 'min:6', 'max:128', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'phone' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^\+[0-9]{10,15}$/'],
            'password' => ['required', 'string', 'min:4', 'max:255'],
        ], $this->fields, $response, [
            'email' => null,
            'phone' => null
        ])) instanceof JsonResponse) {
            return $params;
        }

        if ($params['email'] === null && $params['phone'] === null) {
            return API::fail($response, "Email or phone is required");
        } elseif ($params['email'] !== null) {
            $user = mUsers::findByEmail($params['email']);
            if ($user !== null) {
                return API::fail($response, "This email is already in use");
            }
        } elseif ($params['phone'] !== null) {
            $user = mUsers::findByPhone($params['phone']);
            if ($user !== null) {
                return API::fail($response, "This phone is already in use");
            }
        }

        $data = mUsers::createUser($params);
        return API::success($response, [
            'name' => $data->user->name,
            'email' => $data->user->email,
            'email_verified' => $data->user->verified_at !== null,
            'phone' => $data->user->phone,
            'permissions' => $data->user->permissions,
            'avatar' => $data->user->avatar,
            'access_token' => $data->access_token,
        ]);
    }
}
