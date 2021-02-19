<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;
use App\Models\Rating;

use Carbon\Carbon;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function ratings() {
        return $this->hasMany('App\Models\Rating', 'user_id');
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->attributes['created_at'])->format("d/m/y");
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->attributes['updated_at'])->format("d/m/y");
    }
}
