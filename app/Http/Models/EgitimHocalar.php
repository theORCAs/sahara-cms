<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitimHocalar extends Model
{
    protected $table = "egitim_hocalar";

    protected $guarded = ['id'];

    public function hocaBilgi() {
        return $this->hasOne('App\Http\Models\Egitmenler', 'kullanici_id', 'hoca_id')
            ->withDefault();
    }

    public function egitimKayit() {
        return $this->hasOne('App\Http\Models\EgitimKayitlar', 'id', 'egitim_kayit_id');
    }

    public function egitimMateryal($kullanici_id, $teklif_id) {
        return EgitimMateryal::where('kullanici_id', $kullanici_id)->where('teklif_id', $teklif_id)->get();
    }

    public function egitimMateryal_() {
        return $this->hasMany('App\Http\Models\EgitimMateryal', 'kullanici_id', 'hoca_id');
        //return $this->belongsToMany('App\Http\Models\EgitimMateryal', 'teklifler', 'hoca_id', 'id');
        // return $this->belongsToMany('App\Http\Models\Teklifler', 'egitim_materyal', 'hoca_id', 'teklif_id');
        //return $this->belongsToMany('App\User', 'egitim_materyal', 'teklif_id', 'kullanici_id');

    }

    public function teklif() {
        return $this->hasOne('App\Http\Models\Teklifler', 'id', 'teklif_id')->withDefault();
    }

    public function egitmenDegerlendirme() {
        return $this->hasMany('App\Http\Models\EgitmenDegerlendirme', 'egitim_hoca_id', 'id');
    }

}
