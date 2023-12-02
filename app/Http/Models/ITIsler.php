<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ITIsler extends Model
{
    use SoftDeletes;

    protected $table = "it_isler";

    protected $guarded = ['id'];

    public function referansSirket() {
        return $this->hasOne('App\Http\Models\Referanslar', 'id', 'ref_sirket_id')
            ->withDefault();
    }

    public function ulke() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'ulke_id')
            ->withDefault();
    }

    public function isTuru() {
        return $this->hasOne('App\Http\Models\ITIsTurleri', 'id', 'isturu_id')
            ->withDefault();
    }

    public function istekYapan() {
        return $this->hasOne('App\User', 'id', 'istek_yapan')
            ->withDefault();
    }

    public function atananKisi() {
        return $this->hasOne('App\User', 'id', 'atanan_kisi')
            ->withDefault();
    }




}
