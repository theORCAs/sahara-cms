<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KullaniciGirisler extends Model
{
    use SoftDeletes;

    protected $table = "kullanici_girisler";

    protected $guarded = ['id'];
}
