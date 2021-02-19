<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_rated_id', 'rate', 'rate_description'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
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