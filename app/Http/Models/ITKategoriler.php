<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ITKategoriler extends Model
{
    use SoftDeletes;
    protected $table = "it_kategori";

    protected $guarded = ['id'];
}
