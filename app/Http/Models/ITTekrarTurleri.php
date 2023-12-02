<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ITTekrarTurleri extends Model
{
    use SoftDeletes;
    protected $table = "it_tekrar_turleri";

    protected $guarded = ['id'];
}
