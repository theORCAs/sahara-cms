<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtelSehirleri extends Model
{
    use SoftDeletes;

    protected $table = "otl_sehir";

    protected $guarded = ['id'];
}
