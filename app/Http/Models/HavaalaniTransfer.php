<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HavaalaniTransfer extends Model
{
    use SoftDeletes;

    protected $table = "havaalani_transfer";

    protected $guarded = ['id'];

    public function teklif() {
        return $this->hasOne('App\Http\Models\Teklifler', 'id', 'teklif_id')
            ->withDefault();
    }

    public function otel() {
        return $this->hasOne('App\Http\Models\Oteller', 'id', 'otel_id')
            ->withDefault();
    }

    public function gelisHavayolu() {
        return $this->hasOne('App\Http\Models\HavayoluSirket', 'id', 'gelis_havayolu_id')
            ->withDefault();
    }

    public function gelisHavaalani() {
        return $this->hasOne('App\Http\Models\Havaalanlari', 'id', 'gelis_havaalani_id')
            ->withDefault();
    }

    public function gelisTransferFirma() {
        return $this->hasOne('App\Http\Models\Partnerler', 'id', 'gelis_tasima_firma_id')
            ->withDefault();
    }

    public function kisiler() {
        return $this->hasMany('App\Http\Models\HavaalaniTransferKisiler', 'transfer_id', 'id');
    }

    public function gelisOnaylanBilgi() {
        return $this->hasOne('App\User', 'id', 'gelis_onaylayan_kisi')
            ->withDefault();
    }

    public function gelisTransferFirmaOnayBilgi() {
        return $this->hasOne('App\User', 'id', 'gelis_tasima_onay_id')
            ->withDefault();
    }

    public function gidisHavayolu() {
        return $this->hasOne('App\Http\Models\HavayoluSirket', 'id', 'gidis_havayolu_id')
            ->withDefault();
    }

    public function gidisHavaalani() {
        return $this->hasOne('App\Http\Models\Havaalanlari', 'id', 'gidis_havaalani_id')
            ->withDefault();
    }

    public function gidisTransferFirma() {
        return $this->hasOne('App\Http\Models\Partnerler', 'id', 'gidis_tasima_firma_id')
            ->withDefault();
    }
}
