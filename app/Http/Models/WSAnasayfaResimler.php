<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WSAnasayfaResimler extends Model
{
    use SoftDeletes;

    protected $table = "ws_anasayfaresim";

    protected $guarded = ['id'];
}
