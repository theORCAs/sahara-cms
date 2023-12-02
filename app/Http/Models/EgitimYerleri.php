<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgitimYerleri extends Model
{
    use SoftDeletes;

    protected $table = "egitim_yerleri";

    protected $guarded = ["id"];


}
