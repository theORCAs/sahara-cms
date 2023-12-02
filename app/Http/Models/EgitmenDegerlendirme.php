<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitmenDegerlendirme extends Model
{
    protected $table = "egitmen_degerlendirme";

    protected $guarded = ['id'];

    public function egitimHoca() {
        return $this->hasOne('App\Http\Models\EgitimHocalar', 'id', 'egitim_hoca_id')->withDefault();
    }

    public function katilimci() {
        return $this->hasOne('App\Http\Models\Katilimcilar', 'id', 'katilimci_id')->withDefault();
    }
}
