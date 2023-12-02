<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgitimMateryal extends Model
{
    use SoftDeletes;

    protected $table = "egitim_materyal";

    protected $guarded = ['id'];
}
