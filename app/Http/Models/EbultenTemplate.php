<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EbultenTemplate extends Model
{
    use SoftDeletes;

    protected $table = "eb_template";

    protected $guarded = ['id'];

    public function turu() {
        return $this->hasOne('App\Http\Models\EbultenTemplateTurleri', 'id', 'tur_id')
            ->withDefault();
    }
}
