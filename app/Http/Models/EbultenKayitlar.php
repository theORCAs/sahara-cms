<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EbultenKayitlar extends Model
{
    use SoftDeletes;

    protected $table = "eb_kayitlar";

    protected $guarded = ['id'];

    public function grup() {
        return $this->hasOne('App\Http\Models\EbultenGruplar', 'id', 'grup_id')
            ->orderby('adi', 'asc')
            ->withDefault();
    }

    public function ulke() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'ulke_id')
            ->withDefault();
    }

    public function referansSirket() {
        return $this->hasOne('App\Http\Models\Referanslar', 'id', 'referans_sirket_id')
            ->withDefault();
    }
}
