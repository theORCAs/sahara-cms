<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParaBirimi extends Model
{
    use SoftDeletes;

    protected $table = "para_birimi";

    protected $guarded = ["id"];
    public function paraBirim() {
        return $this->hasOne('App\Http\Models\ParaBirimi', 'id', 'para_birimi');
    }
}
