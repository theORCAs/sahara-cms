<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Egitmenler extends Model
{
    use SoftDeletes;

    protected $table = "egitmenler";

    protected $guarded = ['id'];

    public function unvani() {
        return $this->hasOne('App\Http\Models\Unvanlar', 'id', 'unvan_id')
            ->withDefault();
    }

    public function ekBilgiler() {
        return $this->hasOne('App\Http\Models\EgitmenlerBilgi', 'egitmen_id', 'id')
            ->withDefault();
    }

    public function yasadigiUlke() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'yasadigi_ulke')->withDefault();
    }

    public function girisLoglar() {
        return $this->hasMany('App\Http\Models\KullaniciGirisler', 'kullanici_id', 'kullanici_id');
    }

    public function sectigiKategoriler() {
        return $this->belongsToMany('App\Http\Models\EgitimKategori', 'egitmen_egitimkategori', 'egitmen_id', 'egitim_kategori_id', 'id')
            ->orderBy('egitim_kategori.sira');
        // return $this->hasManyThrough('App\Http\Models\EgitimKategori', 'App\Http\Models\EgitmenEgitimKategori', 'egitmen_id', 'id', 'egitim_kategori_id');

        /*
         * select `egitim_kategori`.*, `egitmen_egitimkategori`.`egitim_kategori_id`
         * from `egitim_kategori`
         *          inner join `egitmen_egitimkategori` on `egitmen_egitimkategori`.`id` = `egitim_kategori`.`egitmen_id`
         * where `egitmen_egitimkategori`.`egitim_kategori_id` = 1616
         */
    }

    public function sectigiEgitimler() {
        return $this->belongsToMany('App\Http\Models\Egitimler', 'egitmen_ek_secim', 'egitmen_id', 'egitim_id', 'id')
            ->orderby('egitimler.adi', 'asc');
    }

    public function verdigiKurslar() {
        return  $this->hasMany('App\Http\Models\EgitimHocalar', 'hoca_id', 'kullanici_id');
    }

    public function kullaniciBilgi() {
        return $this->hasOne('App\User', 'id', 'kullanici_id')->withDefault();
    }

    public function background() {
        return $this->hasMany('App\Http\Models\EgitmenBackground', 'egitmen_id', 'id');
    }

    public function egitimAldigiOkullar() {
        return $this->hasMany('App\Http\Models\EgitmenOkullar', 'egitmen_id', 'id')
            ->orderby('mezun_tarih', 'asc');
    }

    public function calistigiIsler() {
        return $this->hasMany('App\Http\Models\EgitmenIsler', 'egitmen_id', 'id')
            ->orderby('baslama_tarihi', 'desc');
    }

    public function aldigiKurslar() {
        return $this->hasMany('App\Http\Models\EgitmenAldigiKurslar', 'egitmen_id', 'id')
            ->orderby('baslama_tarihi', 'desc');
    }

    public function aldigiEgitimler() {
        return $this->hasMany('App\Http\Models\EgitmenAldigiEgitimler', 'egitmen_id', 'id')
            ->orderby('baslama_tarihi', 'desc');
    }
}
