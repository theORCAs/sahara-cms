<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitimDegerlendirme extends Model
{
    protected $table = "egitim_degerlendirme";

    protected $guarded = ['id'];

    public function hoca() {
        return $this->hasOne('App\Http\Models\Egitmenler', 'kullanici_id', 'hoca_id');
    }
}
