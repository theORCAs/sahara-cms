<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EgitmenBackground extends Model
{
    protected $table = "egitmen_background";

    protected $guarded = ['id'];

    public function egitim() {
        return $this->hasOne('App\Http\Models\Egitimler', 'id', 'egitim_id')
            ->withDefault();
    }
}
