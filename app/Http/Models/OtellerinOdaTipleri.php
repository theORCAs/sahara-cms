<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class OtellerinOdaTipleri extends Model
{
    protected $table = 'otl_oteller_odatip';

    protected $guarded = ['id'];

    public function odaTipi() {
        return $this->hasOne('App\Http\Models\OtelOdaTipleri', 'id', 'oda_tip_id')
            ->withDefault();
    }
}
