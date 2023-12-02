<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtelManzaralari extends Model
{
    use SoftDeletes;

    protected $table = "otl_manzara_tip";

    protected $guarded = ['id'];
}
