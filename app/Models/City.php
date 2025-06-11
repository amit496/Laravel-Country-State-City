<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'state_id'];

    /**
     * Get the state that owns the city.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country that owns the city.
     */
    public function country()
    {
        return $this->belongsToThrough(Country::class, State::class);
    }
}
