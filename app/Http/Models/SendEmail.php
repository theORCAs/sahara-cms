<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SendEmail extends Model
{
    protected $table = "send_email";

    protected $guarded = ["id"];
}
