<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Havaalanlari extends Model
{
    use SoftDeletes;

    protected $table = "havaalanlari";

    protected $guarded = ['id'];
}
