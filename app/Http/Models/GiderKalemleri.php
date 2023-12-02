<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiderKalemleri extends Model
{
    use SoftDeletes;

    protected $table = "hes_gider_kalemleri";

    protected $guarded = ['id'];

    public function kasaHareketleri() {
        return $this->hasMany('App\Http\Models\Kasa', 'gider_kalem_id', 'id');
    }
}
