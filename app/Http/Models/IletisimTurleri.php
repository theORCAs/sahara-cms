<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class IletisimTurleri extends Model
{
    protected $table = "iletisim_turleri";

    protected $guarded = ['id'];

    public function kategori() {
        return $this->hasOne('App\Http\Models\IletisimTurleriKategorileri', 'id', 'kategori_id')
            ->withDefault();
    }
}
