<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtelBolgeleri extends Model
{
    use SoftDeletes;

    protected $table = "otl_bolge";

    protected $guarded = ['id'];


    public function sehir() {
        return $this->hasOne('App\Http\Models\OtelSehirleri', 'id', 'sehir_id')
            ->orderBy('adi', 'asc');
    }


}
