<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OtelRezervasyon extends Model
{
    use SoftDeletes;

    protected $table = "otl_rezervasyon";

    protected $guarded = ['id'];

    public function teklif() {
        return $this->hasOne('App\Http\Models\Teklifler', 'id', 'teklif_id')->withDefault();
    }

    public function manzara() {
        return $this->hasOne('App\Http\Models\OtelManzaralari', 'id', 'manzara_id')->withDefault();
    }

    public function ilgiliPersonel() {
        return $this->hasOne('App\User', 'id', 'ilgili_kisi')->withDefault();
    }

    public function otelRezervasyonOda() {
        return DB::table('otl_rezervasyon_oda')
            ->where('otl_rezervasyon_oda.rezervasyon_id', $this->id)
            ->leftJoin('otl_oteller', 'otl_oteller.id', '=', 'otl_rezervasyon_oda.otel_id')
            ->leftJoin('otl_oda_tipleri', 'otl_oda_tipleri.id', '=', 'otl_rezervasyon_oda.oda_tipi_id')
            ->select('otl_rezervasyon_oda.id', 'otl_oteller.adi as otel_adi', 'otl_rezervasyon_oda.oda_sayisi', 'otl_rezervasyon_oda.gecelik_ucret', 'otl_oda_tipleri.adi as oda_tipi_adi',
                'otl_rezervasyon_oda.rm_tarih')
            ->get();
    }

    public function odalar() {
        return $this->hasMany('App\Http\Models\OtelRezervasyonOda', 'rezervasyon_id', 'id');
    }

    public function kisiler() {
        return $this->hasMany('App\Http\Models\OtelRezervasyonKisiler', 'rezervasyon_id', 'id')
            ->orderby('id', 'asc');
    }
}
