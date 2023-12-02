<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EbultenGonderimGruplar extends Model
{
    protected $table = "eb_gonderim_gruplar";

    protected $guarded = ['id'];

    public function grupGetir() {
        return $this->hasOne('App\Http\Models\EbultenGruplar', 'id', 'grup_id')
            ->withDefault();
    }
}
