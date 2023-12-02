<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Katilimcilar extends Model
{
    protected $table = "katilimcilar";

    protected $guarded = ["id"];

    public function katilimciEkBilgi($teklif_id) {
        return $this->hasOne('App\Http\Models\KatilimcilarEk', 'katilimci_id', 'id')
            ->where('teklif_id', '=', $teklif_id)
            ->withDefault()
            ->first();
    }

    public function yasadigiUlke() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'yasadigi_ulke_id')
            ->withDefault();
    }

    public static function getKatilimcilarUL($egitim_kayit_id) {
        $return_text = "";
        $result = Katilimcilar::where('egitim_kayit_id', $egitim_kayit_id)->get();
        foreach($result as $row) {
            $return_text .= "<li>$row->adi_soyadi</li>";
        }
        return $return_text != "" ? "<ul>$return_text</ul>" : "";
    }
}
