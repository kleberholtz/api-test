<?php

namespace App\Http\Controllers\User;

use App\goHoltz\API\Response as API;
use App\Http\Controllers\Controller;
use App\Models\AccessTokens as mAccessTokens;
use App\Models\User as mUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Auth extends Controller
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
    public function login(): JsonResponse
    {
        if (($params = API::validate([
            'email' => ['required', 'email', 'min:6', 'max:128', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'password' => ['required', 'string', 'min:4', 'max:255'],
            'remember_me' => ['nullable', 'in:true,false,1,0,on,off'],
        ], $this->fields, $response, [
            'remember_me' => false
        ])) instanceof JsonResponse) {
            return $params;
        }

        $params['remember_me'] = in_array($params['remember_me'], [true, 'true', '1', 'on'], true);

        $user = mUsers::findByEmail($params['email']);
        if ($user === null || !password_verify($params['password'], $user->password)) {
            return API::fail($response, "Email or password incorrect.");
        }

        $access_token = $user->getToken();

        // Refresh token if expired
        $expires_at = now()->parse($access_token->expires_at);
        if ($expires_at->isPast()) {
            $params['remember_me'] = in_array($params['remember_me'], [true, 'true', '1', 'on'], true);
            $access_token = mAccessTokens::refreshToken($access_token->token, $params['remember_me'] ? now()->addHours(mUsers::TOKEN_LONG_EXPIRATION) : now()->addHours(mUsers::TOKEN_SHORT_EXPIRATION));
        }

        return API::success($response, [
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->verified_at !== null,
            'phone' => $user->phone,
            'permissions' => $user->permissions,
            'avatar' => $user->avatar,
            'ml_id' => $user->ml_id,
            'data' => $user->data,
            'access_token' => $access_token,
        ]);
    }

    /**
     * User Logout
     * 
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        if (($params = API::validate([], $this->fields, $response)) instanceof JsonResponse) {
            return $params;
        }

        $user = $this->request->user();
        if (!$user->revokeToken()) {
            return API::fail($response, "Error while revoking token");
        }

        return API::successMsg($response, "User logged out");
    }
}
