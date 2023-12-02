<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teklifler extends Model
{
    use SoftDeletes;

    protected $table = "teklifler";

    protected $guarded = ["id"];

    public function egitimKayit() {
        return $this->belongsTo('App\Http\Models\EgitimKayitlar', 'id', 'ref_teklif_id')->withDefault();
    }

    public function egitimHocalar() {
        return $this->hasMany('App\Http\Models\EgitimHocalar', 'teklif_id', 'id')
            ->orderby('ders_sira', 'asc')
            ->orderby('saat_sira', 'asc');
    }

    public function kursYeri() {
        //return $this->belongsTo('App\Http\Models\KursYeri', 'id', 'teklif_id');
        return $this->hasOne('App\Http\Models\KursYeri', 'teklif_id', 'id')
            ->orderby('id', 'desc')
            ->withDefault();
    }

    public function kursYeriRezerMail() {
        return $this->hasMany('App\Http\Models\KursYeriRezerMail', 'teklif_id', 'id')
            ->orderby('id', 'desc');
    }

    public function visaDavetMailGonderenKisi() {
        return $this->hasOne('App\User', 'id', 'vdm_gonderen');
    }

    public function visaDavetPdfGonderenKisi() {
        return $this->hasOne('App\User', 'id', 'vpm_gonderen');
    }

    public function airportTrasnferMailiGonderenKisi() {
        return $this->hasOne('App\User', 'id', 'apt_gonderen');
    }

    public function otelRezervasyonMailiGonderenKisi() {
        return $this->hasOne('App\User', 'id', 'orm_gonderen');
    }

    public function kursaTalipler() {
        return $this->hasMany('App\Http\Models\EgitmenKursTalip', 'teklif_id', 'id');
    }

    public function degerlendirme() {
        return $this->hasMany('App\Http\Models\EgitimDegerlendirme', 'teklif_id', 'id');
    }

    public function verilenStokSayisi() {
        return $this->hasOne('App\Http\Models\Stoklar', 'teklif_id', 'id')
            ->selectRaw('sum(cikis) toplam_cikis')
            ->groupBy('teklif_id')
            ->withDefault();
    }

    public function airportTransferFormu() {
        return $this->hasOne('App\Http\Models\HavaalaniTransfer', 'teklif_id', 'id')
            ->withDefault();
    }

    public function otelRezervasyon() {
        return $this->hasOne('App\Http\Models\OtelRezervasyon', 'teklif_id', 'id')
            ->withDefault();
    }

    public function guneHocaAtanmismi($tarih) {
        $row = EgitimHocalar::where('teklif_id', $this->id)->where('ders_tarihi', $tarih)
            ->select('hoca_kisa_adi')
            ->selectraw('count(id) as atanmis')
            ->whereNotIn('hoca_id', [426])
            ->first();

        return $row;
    }

    public function katilimciResimleri() {
        return $this->hasMany('App\Http\Models\KatilimcilarFoto', 'teklif_id', 'id');
    }

    public function teklifFormlar($tur_id=1) {
        $return = $this->hasMany('App\Http\Models\TeklifFormlar', 'teklif_id', 'id');
        return $return->where('tur_id', $tur_id);
    }

    public function pdfInvoiceBilgileri() {
        return $this->hasOne('App\Http\Models\PdfInvoice', "teklif_id", "id")
            ->withDefault();
    }
}
