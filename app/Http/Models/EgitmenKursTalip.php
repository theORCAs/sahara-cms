<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitmenKursTalip extends Model
{
    protected $table = "egitmen_kurstalip";

    protected $guarded = ['id'];

    public function egitmen() {
        return $this->hasOne('App\Http\Models\Egitmenler', 'kullanici_id', 'kullanici_id')->withDefault();
    }

    public function onayMailGonderenKisi() {
        return $this->hasOne('App\User', 'id', 'onay_mail_gonderen')->withDefault();
    }

    public function iptalMailGonderenKisi() {
        return $this->hasOne('App\User', 'id', 'iptal_mail_gonderen')->withDefault();
    }
}
