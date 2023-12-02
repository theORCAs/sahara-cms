<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Oteller extends Model
{
    use SoftDeletes;

    protected $table = "otl_oteller";

    protected $guarded = ['id'];

    public function sehir() {
        return $this->hasOne('App\Http\Models\OtelSehirleri', 'id', 'sehir_id')
            ->withDefault();
    }

    public function bolge() {
        return $this->hasOne('App\Http\Models\OtelBolgeleri', 'id', 'bolge_id')
            ->withDefault();
    }

    public function derece() {
        return $this->hasOne('App\Http\Models\OtelDerece', 'id', 'derece_id')
            ->withDefault();
    }

    public function sonGuncelleyen() {
        return $this->hasOne('App\User', 'id', 'guncel_id')->withDefault();
    }

    public function otelOdaTipleri() {
        return DB::table('otl_oteller_odatip')
            ->leftJoin('otl_oda_tipleri', 'otl_oda_tipleri.id', '=', 'otl_oteller_odatip.oda_tip_id')
            ->where('otel_id', $this->id)
            ->where('ucret_satis', '>', '0')
            ->whereOr('flg_na', '1')
            ->select('otl_oda_tipleri.kisaltma as oda_tipi', 'otl_oteller_odatip.*')
            ->get();
    }

    public function iletisimListesi() {
        return $this->hasMany('App\Http\Models\OtelIletisim', 'otel_id', 'id');
    }

    public function odaTipleri() {
        return $this->hasMany('App\Http\Models\OtellerinOdaTipleri', 'otel_id', 'id');
    }
}
