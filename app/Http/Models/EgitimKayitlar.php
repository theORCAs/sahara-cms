<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EgitimKayitlar extends Model
{
    use SoftDeletes;

    protected $table = "egitim_kayitlar";

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    public function egitimler() {
        return $this->hasOne('App\Http\Models\Egitimler', 'id', 'egitim_id')
            ->withDefault();
    }

    public function egitimTarihi() {
        return $this->hasOne("App\Http\Models\EgitimTarihleri", "id", "egitim_tarih_id")
            ->orderBy('baslama_tarihi')
            ->withDefault();
    }

    public function sirketReferans() {
        return $this->hasOne('App\Http\Models\Referanslar', 'id', 'referans_id')
            ->withDefault();
    }

    public function sirketUlke() {
        return $this->hasOne("App\Http\Models\Ulkeler", "id", "sirket_ulke_id")
            ->withDefault();
    }

    public function kontakKisiUnvan() {
        return $this->hasOne("App\Http\Models\Unvanlar", "id", "ct_unvan_id")
            ->withDefault();
    }

    public function katilimcilar() {
        return $this->hasMany('App\Http\Models\Katilimcilar', 'egitim_kayit_id', 'id')
            ->orderby('adi_soyadi');
    }

    public function teklifler() {
        return $this->hasMany('App\Http\Models\Teklifler', 'egitim_kayit_id', 'id');
    }

    public function aktifTeklif() {
        return $this->hasOne('App\Http\Models\Teklifler', 'id', 'ref_teklif_id')
            ->withDefault();
    }

    public function pdfInvoice() {
        return $this->hasOne('App\Http\Models\PdfInvoice', 'teklif_id', 'ref_teklif_id')
            ->withDefault();
    }

    public function pdfConfirmation() {
        return $this->hasOne('App\Http\Models\PdfConfirmation', 'teklif_id', 'ref_teklif_id')
            ->withDefault();
    }

    public function pdfProposal() {
        return $this->hasOne('App\Http\Models\PdfProposal', 'teklif_id', 'ref_teklif_id')
            ->withDefault();
    }

}
