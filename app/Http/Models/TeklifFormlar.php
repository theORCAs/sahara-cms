<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeklifFormlar extends Model
{
    use SoftDeletes;

    protected $table = "teklif_formlar";

    protected $guarded = ["id"];
}
