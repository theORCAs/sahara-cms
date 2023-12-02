<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ulkeler extends Model
{
    use SoftDeletes;

    protected $table = "ulkeler";

    protected $guarded = ["id"];
}
