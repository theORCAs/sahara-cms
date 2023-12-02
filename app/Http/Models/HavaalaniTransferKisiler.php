<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HavaalaniTransferKisiler extends Model
{
    use SoftDeletes;

    protected $table = "havaalani_transfer_kisiler";

    protected $guarded = ['id'];
}
