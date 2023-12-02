<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class StokUrunler extends Model
{
    protected $table = "st_urunler";

    protected $guarded = ['id'];

    public function stoklar() {
        return $this->hasMany('App\Http\Models\Stoklar', 'stok_urun_id', 'id');
    }

    public function stokDurumlar() {
        return $this->hasOne('App\Http\Models\Stoklar', 'stok_urun_id', 'id')
            ->selectRaw('st_stoklar.stok_urun_id, sum(giris - cikis) as stok_durumu')
            ->groupBy('st_stoklar.stok_urun_id')
            ->withDefault();
    }
}
