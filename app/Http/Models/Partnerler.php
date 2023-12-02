<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partnerler extends Model
{
    use SoftDeletes;

    protected $table = "partnerler";

    protected $guarded = ['id'];

    public function kategori() {
        return $this->hasOne('App\Http\Models\PartnerKategorileri', 'id', 'kategori_id')
            ->orderby('adi', 'asc')
            ->withDefault();
    }
}
