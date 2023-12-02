<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WSMenuler extends Model
{
    use SoftDeletes;

    protected $table = "ws_menu";

    protected $guarded = ['id'];

    public function modul() {
        return $this->hasOne('App\Http\Models\WSModuller', 'id', 'ws_modul_id')
            ->withDefault();
    }
}
