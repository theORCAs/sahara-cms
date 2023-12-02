<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitimTarihleri extends Model
{
    protected $table = "egitim_tarihleri";

    protected $guarded = ["id"];

    public function egitimYeri() {
        return  $this->hasOne("App\Http\Models\EgitimYerleri", "id", "egitim_yeri_id")->withDefault();
    }

    public function egitimPart() {
        return $this->hasOne("App\Http\Models\EgitimPart", "id", "egitim_part_id")->withDefault();
    }

    public function egitimUcretiGetir() {
        if(floatval($this->ucret) > 0)
            return $this->ucret." ".ParaBirimi::find($this->ucret_para_birimi)["kisaltma"];
        return EgitimKategori::find(Egitimler::find($this->egitim_id)["kategori_id"])["ucret"];
    }

    public function egitimUcretiNumber() {
        if(floatval($this->ucret) > 0)
            return floatval($this->ucret);
        return floatval(EgitimKategori::find(Egitimler::find($this->egitim_id)["kategori_id"])["ucret"]);
    }
}
