<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OdemeBeklemeTurleri extends Model
{
    use SoftDeletes;
    protected $table = "odeme_bekleme_turleri";

    protected $guarded = ['id'];
}
