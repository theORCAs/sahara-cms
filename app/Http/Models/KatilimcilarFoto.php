<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KatilimcilarFoto extends Model
{
    use SoftDeletes;

    protected $table = "katilimcilar_foto";

    protected $guarded = ['id'];

    public function katilimci() {
        return $this->hasOne('App\Http\Models\Katilimcilar', 'id', 'katilimci_id')
            ->withDefault();
    }
}
