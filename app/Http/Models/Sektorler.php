<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sektorler extends Model
{
    use SoftDeletes;

    protected $table = "sektorler";

    protected $guarded = ['id'];
}
