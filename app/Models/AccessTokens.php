<?php

namespace App\Models;

use App\Exceptions\createException;
use App\Traits\U40R;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccessTokens extends Model
{
    use U40R;

    /**
     * Token length
     * 
     * NOTE: Trait U40R is used to generate a random token of 40 characters.
     * 
     * @var int
     */
    const TOKEN_LENGTH = 40;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'access_tokens';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'token';

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
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'data',
        'user_id',
        'name',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'data',
        'user_id',
        'name',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Custom find method.
     * 
     * @param string $email
     */
    public static function find(string $token): ?self
    {
        return self::where('token', $token)->first();
    }

    public static function createToken(string $user_id, ?Carbon $expires_at = null): self
    {
        $token = new self;
        $token->user_id = $user_id;
        $token->expires_at = $expires_at === null ? now()->addDay() : $expires_at;
        if (!$token->save()) {
            throw new createException("Unable to create access token");
        }

        return $token;
    }

    public static function refreshToken(string $token, ?Carbon $expires_at = null): mixed
    {
        $token = self::find($token);
        $token->token = Str::random(self::TOKEN_LENGTH);
        $token->expires_at = $expires_at === null ? now()->addDay() : $expires_at;
        if (!$token->save()) {
            return false;
        }

        return $token;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
