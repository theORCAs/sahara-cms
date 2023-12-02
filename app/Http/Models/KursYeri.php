<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KursYeri extends Model
{
    use SoftDeletes;

    protected $table = "kurs_yeri";

    protected $guarded = ['id'];

    public function otelBilgi() {
        return $this->hasOne('App\Http\Models\Oteller', 'id', 'otel_id')
            ->withDefault();
    }

    public function katilimciMailiGonderen() {
        return $this->hasOne('App\User', 'id', 'mail_katilimci_gonderen')
            ->select(['id', 'adi_soyadi']);
    }

    public function egitmenMailiGonderen() {
        return $this->hasOne('App\User', 'id', 'mail_egitmen_gonderen')
            ->select(['id', 'adi_soyadi']);
    }
}
