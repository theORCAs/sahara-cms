<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Egitimler extends Model
{
    use SoftDeletes;

    protected $table = "egitimler";

    protected $guarded = ["id"];

    public function egitimKategori() {
        return $this->hasOne('App\Http\Models\EgitimKategori', 'id', 'kategori_id')
            ->withDefault();
    }

    public function egitimProgram() {
        return $this->hasMany('App\Http\Models\EgitimProgram', 'egitim_id', 'id')->where("flg_gosterme", 0)
            ->orderBy('gun')->orderBy('id');
    }

    public function teklifEden() {
        return $this->hasOne('App\User', 'id', 'teklif_eden_kisi')->withDefault();
    }

    public function egitimGelecekTarihler() {
        return $this->hasMany('App\Http\Models\EgitimTarihleri', 'egitim_id', 'id')
            ->whereRaw('baslama_tarihi >= curdate()')
            ->orderby('baslama_tarihi', 'asc');
    }

    public function sonGuncelleyen() {
        return $this->hasOne('App\User', 'id', 'guncel_id')->withDefault();
    }

    public function egitimPart() {
        return $this->hasOne('App\Http\Models\EgitimPart', 'id', 'egitim_part_id')
            ->withDefault();
    }
    public function egitimDil() {
        return $this->hasOne('App\Http\Models\Diller', 'id', 'dil_id')
            ->withDefault();
    }
}
