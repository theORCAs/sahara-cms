<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetup extends Model
{
    use SoftDeletes;

    protected $table = "sistem_ayarlar";

    protected $guarded = ['id'];


}
