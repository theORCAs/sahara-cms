<?php

namespace App\Http\Middleware;

use App\Http\Models\EgitimKayitlar;
use Closure;

class CheckPdf
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $param_arr = $request->route()->parameters();
        $egitim_kayit_id = $param_arr["egitim_kayit_id"];
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);
        if($egitim_kayitlar->aktifTeklif["invoice_pdf"] == '')
            return redirect()->back()->withErrors('Please create Invoice PDF...');
        if($egitim_kayitlar->aktifTeklif["confirmation_pdf"] == '')
            return redirect()->back()->withErrors('Please create Confirmation Letter PDF...');
        if($egitim_kayitlar->aktifTeklif["proposal_pdf"] == '')
            return redirect()->back()->withErrors('Please create Proposal PDF...');
        if($egitim_kayitlar->aktifTeklif["outline_pdf"] == '')
            return redirect()->back()->withErrors('Please create Course Outline PDF...');

        return $next($request);
    }
}
