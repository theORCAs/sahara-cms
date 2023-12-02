<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HavaalaniTransferTnm extends Model
{
    use SoftDeletes;

    protected $table = "havaalani_transfer_tnm";

    protected $guarded = ['id'];
}
