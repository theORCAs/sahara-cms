<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GelirKalemleri extends Model
{
    use SoftDeletes;

    protected $table = "hes_gelir_kalemleri";

    protected $guarded = ['id'];

    public function kasaHareketleri() {
        return $this->hasMany('App\Http\Models\Kasa', 'gelir_kalem_id', 'id');
    }
}
