<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgitimArastirma extends Model
{
    use SoftDeletes;
    
    protected $table = "egitim_arastirma";

    protected $guarded = ['id'];

    public function unvan() {
        return $this->hasOne('App\Http\Models\Unvanlar', 'id', 'unvan_id')
            ->withDefault();
    }

    public function referansSirket() {
        return $this->hasOne('App\Http\Models\Referanslar', 'id', 'ref_sirket_id')
            ->withDefault();
    }

    public function ulke() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'ulke_id')
            ->withDefault();
    }

    public function egitim() {
        return $this->hasOne('App\Http\Models\Egitimler', 'id', 'egitim_id')
            ->withDefault();
    }
}
