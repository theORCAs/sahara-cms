<?php

namespace App\Http\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;

class Moduller extends Model
{
    protected $table = 'moduller';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];

}
