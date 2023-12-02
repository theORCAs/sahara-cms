<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerKategorileri extends Model
{
    use SoftDeletes;

    protected $table = "partner_kategori";

    protected $guarded = ['id'];

    public function firmalarListesi() {
        return $this->hasMany('App\Http\Models\Partnerler', 'kategori_id', 'id')
            ->orderby('adi', 'asc');
    }
}
