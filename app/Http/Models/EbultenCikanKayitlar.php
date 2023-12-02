<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EbultenCikanKayitlar extends Model
{
    protected $table = "eb_cikan_kayitlar";

    protected $guarded = ['id'];

    public function grup() {
        return $this->hasOne('App\Http\Models\EbultenGruplar', 'id', 'grup_id')
            ->withDefault();
    }

    public function cikisNedeni() {
        return $this->hasOne('App\Http\Models\EbultenCikisNedenleri', 'id', 'cikis_neden_id')
            ->withDefault();
    }
}
