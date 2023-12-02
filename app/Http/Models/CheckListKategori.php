<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckListKategori extends Model
{
    use SoftDeletes;
    protected $table = "chklist_kategori";

    protected $guarded = ['id'];
}
