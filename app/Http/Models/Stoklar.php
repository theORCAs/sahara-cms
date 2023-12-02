<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Stoklar extends Model
{
    protected $table = "st_stoklar";

    protected $guarded = ['id'];

    public function kullanici() {
        return $this->hasOne('App\User', 'id', 'created_by')
            ->withDefault();
    }

    public function teklif() {
        return $this->hasOne('App\Http\Models\Teklifler', 'id', 'teklif_id')
            ->withDefault();
    }
}
