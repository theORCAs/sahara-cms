<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class KullaniciRolleri extends Model
{
    protected $table = "kullanici_rolleri";

    protected $guarded = ['id'];

    public function kullanicilar() {
        return $this->hasOne('App\User', 'id', 'kullanici_id')
            ->withDefault();
    }

    public function roller() {
        return $this->hasOne('App\Http\Models\Roller', 'id', 'rol_id')
            ->withDefault();
    }

    public function kullanicilarTest() {
        return $this->hasOne('App\User', 'id', 'kullanici_id')
            ->where('flg_durum', '=', 1)
            ->select('adi_soyadi')
            ->withDefault();
    }
}
