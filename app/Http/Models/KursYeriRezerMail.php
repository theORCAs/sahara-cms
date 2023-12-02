<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class KursYeriRezerMail extends Model
{
    protected $table = "kurs_yeri_rezermail";

    protected $quarded = ['id'];

    public function otelBilgi() {
        return $this->hasOne('App\Http\Models\Oteller', 'id', 'otel_id');
    }
}
