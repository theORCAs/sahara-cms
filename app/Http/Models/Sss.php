<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sss extends Model
{
    use SoftDeletes;

    protected $table = "ws_sss";

    protected $guarded = ['id'];
}
