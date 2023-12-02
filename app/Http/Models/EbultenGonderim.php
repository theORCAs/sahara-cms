<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EbultenGonderim extends Model
{
    protected $table = "eb_gonderim";

    protected $guarded = ['id'];

    public function gruplar() {
        return $this->hasMany('App\Http\Models\EbultenGonderimGruplar', 'gonderim_id', 'id');
    }

    public function sablonBilgi() {
        return $this->hasOne('App\Http\Models\EbultenTemplate', 'id', 'template_id')
            ->withDefault();
    }
}
