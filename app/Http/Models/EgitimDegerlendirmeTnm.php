<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgitimDegerlendirmeTnm extends Model
{
    use SoftDeletes;

    protected $table = "egitim_degerlendirme_tnm";

    protected $guarded = ['id'];
}
