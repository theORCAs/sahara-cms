<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EbultenGruplar extends Model
{
    use SoftDeletes;

    protected $table = "eb_gruplar";

    protected $guarded = ['id'];

    public function dinamikSorgu() {
        return $this->hasOne('App\Http\Models\EbultenDinamikGrupSorgular', 'id', 'dinamik_grup_id')
            ->withDefault();
    }

    public function yetkili1() {
        return $this->hasOne('App\User', 'id', 'yetkili_1')
            ->withDefault();
    }
    public function yetkili2() {
        return $this->hasOne('App\User', 'id', 'yetkili_2')
            ->withDefault();
    }
    public function yetkili3() {
        return $this->hasOne('App\User', 'id', 'yetkili_3')
            ->withDefault();
    }

    public function statikEmailSayisi() {
        return $this->hasOne('App\Http\Models\EbultenKayitlar', 'grup_id', 'id')
            ->selectRaw('count(id) as sayisi')
            ->withDefault();
    }

    public function cikanKayitSayisi() {
        return $this->hasOne('App\Http\Models\EbultenCikanKayitlar', 'grup_id', 'id')
            ->selectraw('count(id) as sayisi')
            ->withDefault();
    }

}
