<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'code'];

    /**
     * Get the states for the country.
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the cities for the country.
     */
    public function cities()
    {
        return $this->hasManyThrough(City::class, State::class);
    }
}
