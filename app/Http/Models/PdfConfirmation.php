<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PdfConfirmation extends Model
{
    protected $table = "pdf_confirmation";

    protected $guarded = ["id"];
}
