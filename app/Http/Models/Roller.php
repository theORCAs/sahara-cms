<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roller extends Model
{
    use SoftDeletes;

    protected $table = "roller";

    protected $dates = ['deleted_at'];

    public function aktifKullaniciSayisi() {
        return $this->hasOne('App\Http\Models\KullaniciRolleri', 'rol_id', 'id')
            ->whereHas('kullanicilar', function ($query) {
                $query->where('flg_durum', '=', '1');
            })
            ->selectRaw('count(id) as sayisi')
            ->withDefault();
    }

    public function pasifKullaniciSayisi() {
        return $this->hasOne('App\Http\Models\KullaniciRolleri', 'rol_id', 'id')
            ->whereHas('kullanicilar', function ($query) {
                $query->where('flg_durum', '=', '0');
            })
            ->selectRaw('count(id) as sayisi')
            ->withDefault();
    }

}
