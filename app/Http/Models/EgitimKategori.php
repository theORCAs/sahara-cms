<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgitimKategori extends Model
{
    use SoftDeletes;
    protected $table = "egitim_kategori";

    protected $guarded = ["id"];


}
