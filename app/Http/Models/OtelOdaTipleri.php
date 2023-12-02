<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtelOdaTipleri extends Model
{
    use SoftDeletes;
    protected $table = "otl_oda_tipleri";

    protected $guarded = ['id'];
}
