<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Odemeler extends Model
{
    use SoftDeletes;

    protected $table = "odemeler";

    protected $guarded = ['id'];

    public function odemeBeklemeTurleri() {
        return $this->hasOne('App\Http\Models\OdemeBeklemeTurleri', 'id', 'dekont_durum_id')
            ->withDefault();
    }

    public function ekleyenKisi() {
        return $this->hasOne('App\User', 'id', 'created_by')
            ->withDefault();
    }

    public function guncelleyenKisi() {
        return $this->hasOne('App\User', 'id', 'updated_by')
            ->withDefault();
    }

    public function partner() {
        return $this->hasOne('App\Http\Models\Partnerler', 'id', 'partner_id')
            ->withDefault();
    }
}
