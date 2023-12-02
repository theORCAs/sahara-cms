<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PdfInvoice extends Model
{
    protected $table = "pdf_invoice";

    protected $guarded = ["id"];

    public function sirketUlkesi() {
        return $this->hasOne('App\Http\Models\Ulkeler', 'id', 'sirket_ulke_id');
    }
    public function paraBirim() {
        return $this->hasOne('App\Http\Models\ParaBirimi', 'id', 'para_birimi');
    }
}
