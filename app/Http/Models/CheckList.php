<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckList extends Model
{
    use SoftDeletes;

    protected $table = "chklist_liste";

    protected $guarded = ['id'];

    public function kategori() {
        return $this->hasOne('App\Http\Models\CheckListKategori', 'id', 'kategori_id')
            ->withDefault();
    }
}
