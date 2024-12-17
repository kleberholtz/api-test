<?php

namespace App\Models;

use App\Casts\CastArray as cArray;
use App\Casts\CastJson as cJson;
use App\Exceptions\createException;
use App\Exceptions\deleteException;
use App\Exceptions\updateException;
use App\Traits\U40R;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Items extends Authenticatable
{
    use U40R;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'items';

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
        'description',
        'price',
        'images',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
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
            'images' => cArray::class,
            'data' => cJson::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
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
}
