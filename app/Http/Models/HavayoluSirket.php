<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HavayoluSirket extends Model
{
    use SoftDeletes;

    protected $table = "havayolu_sirketleri";

    protected $guarded = ['id'];

}
