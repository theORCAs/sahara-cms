<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referanslar extends Model
{
    use SoftDeletes;

    protected $table = "referanslar";

    protected $guarded = ["id"];

    public function ulke() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'ulke_id')
            ->withDefault();
    }

    public function sektor() {
        return $this->hasOne('App\Http\Models\Sektorler', 'id', 'sektor_id')
            ->withDefault();
    }
}
