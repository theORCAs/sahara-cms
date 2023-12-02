<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EbultenCikisNedenleri extends Model
{
    protected $table = "eb_cikis_nedenleri";

    protected $guarded = ['id'];

    public function cikanKayitSayilari() {
        return $this->hasOne('App\Http\Models\EbultenCikanKayitlar', 'cikis_neden_id', 'id')
            ->selectRaw('count(1) sayisi')
            ->groupby('cikis_neden_id')
            ->withDefault();
    }
}
