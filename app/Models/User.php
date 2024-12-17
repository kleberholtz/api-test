<?php

namespace App\Models;

use App\Casts\CastArray as cArray;
use App\Casts\CastJson as cJson;
use App\Exceptions\createException;
use App\Exceptions\deleteException;
use App\Exceptions\updateException;
use App\Traits\U32R;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use U32R;

    const TOKEN_SHORT_EXPIRATION = 24 * 7; // 7 day
    const TOKEN_LONG_EXPIRATION = 24 * 30; // 30 days

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'permissions',
        'verified_at',
        'ml_id',
        'data',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'email',
        'phone',
        'password',
        'verified_at',
        'ml_id',
        'data',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => cJson::class,
            'permissions' => cArray::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function save(array $options = []): bool
    {
        if (!isset($this->access_token->token)) {
            return parent::save($options);
        }

        $cacheKey = "user.{$this->access_token->token}";
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }

        if (isset($this->access_token)) {
            unset($this->access_token);
        }

        return parent::save($options);
    }

    /**
     * Custom find method.
     * 
     * @param string $id
     * 
     * @return ?self
     */
    public static function find(string $id): ?self
    {
        return self::where('id', $id)->first();
    }

    /**
     * Find by email.
     * 
     * @param string $email
     * 
     * @return mixed
     */
    public static function findByEmail(string $email): mixed
    {
        return self::where('email', $email)->first();
    }

    /**
     * Find by token.
     * 
     * @param string $token
     * 
     * @return ?self
     */
    public static function findByToken(string $token): ?self
    {
        $user = self::where('access_tokens.token', $token)
            ->join('access_tokens', 'access_tokens.user_id', '=', 'users.id')
            ->select('users.*')
            ->addSelect('access_tokens.token')
            ->addSelect('access_tokens.expires_at')
            ->first();

        if ($user === null) {
            return null;
        }

        $user->access_token = (object) [
            'token' => $user->token ?? null,
            'expires_at' => $user->expires_at ?? null,
        ];

        unset($user->token, $user->expires_at);

        return $user;
    }

    /**
     * Get user token.
     * 
     * @return mixed
     */
    public function getToken(): mixed
    {
        $token = $this->hasOne(AccessTokens::class, 'user_id', 'id')->first();
        if ($token === null) {
            $token = AccessTokens::createToken($this->id, now()->addHours(self::TOKEN_SHORT_EXPIRATION));
            if ($token === null || !$token) {
                return null;
            }
        }

        return $token;
    }

    public function refreshToken(bool $remember_me = false): mixed
    {
        $token = $this->getToken();
        $token->token = Str::random(AccessTokens::TOKEN_LENGTH);
        $token->expires_at = $remember_me ? now()->addHours(self::TOKEN_LONG_EXPIRATION) : now()->addHours(self::TOKEN_SHORT_EXPIRATION);
        if (!$token->save()) {
            return null;
        }

        return $token;
    }

    public function revokeToken(): bool
    {
        $token = $this->hasOne(AccessTokens::class, 'user_id', 'id')->first();
        if ($token === null) {
            return true;
        }
        
        if (!$token->delete()) {
            throw new deleteException('Error while revoking token');
        }

        Cache::forget("user.{$token->token}");
        return true;
    }

    /**
     * 
     * 
     * @param array $params
     * @param ?Carbon $expires
     * 
     * @return object
     */
    public static function createUser(array $params, ?Carbon $expires = null): object
    {
        $expires = $expires !== null ? $expires : now()->addHours(self::TOKEN_SHORT_EXPIRATION);

        $user = new self;
        $user->name = $params['name'];
        $user->email = $params['email'];
        $user->phone = $params['phone'];
        $user->password = Hash::make($params['password']);
        $user->permissions = [];
        $user->verified_at = null;
        $user->ml_id = null;
        $user->data = new \stdClass;
        $user->created_at = now();
        if (!$user->save()) {
            throw new createException('Error while creating user');
        }

        return (object) [
            'user' => $user,
            'access_token' => AccessTokens::createToken($user->id, $expires),
        ];
    }
}
