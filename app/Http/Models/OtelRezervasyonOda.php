<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class OtelRezervasyonOda extends Model
{
    protected $table = "otl_rezervasyon_oda";

    protected $guarded = ['id'];

    public function otel() {
        return $this->hasOne('App\Http\Models\Oteller', 'id', 'otel_id')
            ->withDefault();
    }
}
