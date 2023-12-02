<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ITIsTurleri extends Model
{
    use SoftDeletes;

    protected $table = "it_isturleri";

    protected $guarded = ['id'];

    public function kategori() {
        return $this->hasOne('App\Http\Models\ITKategoriler', 'id', 'kategori_id')
            ->orderby('it_kategori.adi', 'asc')
            ->withDefault();
    }

    public function tekrarTuru() {
        return $this->hasOne('App\Http\Models\ITTekrarTurleri', 'id', 'tekrar_id')
            ->orderby('sira', 'asc')
            ->withDefault();
    }
}
