<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitmenKursOneri extends Model
{
    protected $table = "oneri_egitim";

    protected $guarded = ['id'];

    public function egitmen() {
        return $this->hasOne('App\Http\Models\Egitmenler', 'kullanici_id', 'kullanici_id')
            ->withDefault();
    }

    public function kategori() {
        return $this->hasOne('App\Http\Models\EgitimKategori', 'id', 'kategori_id')
            ->select('id', 'adi')
            ->withDefault();
    }
}
