<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kasa extends Model
{
    use SoftDeletes;

    protected $table = "hes_kasa";

    protected $guarded = ['id'];

    public function islemYapan() {
        return $this->hasOne('App\User', 'id', 'islem_yapan')
            ->withDefault();
    }

    public function ilgiliKisi() {
        return $this->hasOne('App\User', 'id', 'ilgili_kisi')
            ->withDefault();
    }

    public function gelirKalem() {
        return $this->hasOne('App\Http\Models\GelirKalemleri', 'id', 'gelir_kalem_id')
            ->withDefault();
    }

    public function giderKalem() {
        return $this->hasOne('App\Http\Models\GiderKalemleri', 'id', 'gider_kalem_id')
            ->withDefault();
    }
}
