<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    //
    protected $casts = [
        'location' => 'array',
        'temperature' => 'array'
    ];
}
