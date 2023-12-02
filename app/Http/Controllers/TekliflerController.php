<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimHocalar;
use App\Http\Models\EgitimKayitlar;
use App\Http\Models\EgitmenBackground;
use App\Http\Models\EgitmenKursTalip;
use App\Http\Models\Egitmenler;
use App\Http\Models\EmailSablon;
use App\Http\Models\Katilimcilar;
use App\Http\Models\KatilimcilarEk;
use App\Http\Models\KursYeri;
use App\Http\Models\OtelBolgeleri;
use App\Http\Models\Oteller;
use App\Http\Models\OtelSehirleri;
use App\Http\Models\SendEmail;
use App\Http\Models\Teklifler;
use App\Http\Models\Unvanlar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PharIo\Manifest\Email;
use Auth;
use Validator;
use PDF;

class TekliflerController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\Teklifler  $teklifler
     * @return \Illuminate\Http\Response
     */
    public function show(Teklifler $teklifler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Teklifler  $teklifler
     * @return \Illuminate\Http\Response
     */
    public function edit(Teklifler $teklifler)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Teklifler  $teklifler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teklifler $teklifler)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Teklifler  $teklifler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teklifler $teklifler)
    {
        //
    }

    public function search(Request $request) {
        session(['FILTRE_YIL' => $request->filtre_yil]);
        session(['FILTRE_ULKE_ID' => $request->filtre_ulke_id]);
        session(['FILTRE_SIRKET_ID' => $request->filtre_sirket_id]);
        session(['FILTRE_HOCA_ODEME' => $request->filtre_hoca_odeme]);
        session(['FILTRE_EGITIM_ODEME' => $request->filtre_egitim_odeme]);

        return redirect('/'.session('PREFIX'));
    }

    public function cc_now() {
        if(!Auth::user()->isAllow('cca_cc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $query = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'teklifler.id', '=', 'egitim_kayitlar.ref_teklif_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->where('teklifler.flg_silindi', 0)
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) > curdate()')
            ->orderby('egitim_tarihleri.baslama_tarihi', 'asc')
            ->orderby('teklifler.id', 'desc')
            ->select('teklifler.*', 'egitim_tarihleri.baslama_tarihi');



        $filtre_yil_liste = Teklifler::where('teklifler.durum', 2)
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->selectraw('year(egitim_tarihleri.baslama_tarihi) as yil')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('yil')
            ->orderby('yil', 'desc');

        $filtre_ulke_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->wherenotnull('ulkeler.id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc');

        $filtre_ref_sirket_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('referanslar', 'referanslar.id', '=', 'egitim_kayitlar.referans_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->wherenotnull('egitim_kayitlar.referans_id')
            ->select('referanslar.id', 'referanslar.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('referanslar.id')
            ->orderby('referanslar.adi', 'asc');

        $filtre_hoca_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('egitim_hocalar', 'egitim_hocalar.teklif_id', '=', 'teklifler.id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->wherenotnull('egitim_hocalar.id')
            ->selectraw('count(distinct if(egitim_hocalar.odeme_yapilma_tarih is null, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(distinct if(egitim_hocalar.odeme_yapilma_tarih is not null, teklifler.id, null)) as paid_sayi');

        $filtre_egitim_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->selectraw('count(if(teklifler.flg_odendi = 0, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(teklifler.flg_odendi = 1, teklifler.id, null)) as paid_sayi');

        if(intval(session('FILTRE_YIL')) > 0) {
            $query->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('FILTRE_YIL'));
            $filtre_ulke_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('FILTRE_YIL'));
            $filtre_ref_sirket_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('FILTRE_YIL'));
        }
        if(intval(session('FILTRE_ULKE_ID'))) {
            $query->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('FILTRE_ULKE_ID'));
            $filtre_ref_sirket_liste->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('FILTRE_ULKE_ID'));
        }
        if(intval(session('FILTRE_SIRKET_ID'))) {
            $query->where('egitim_kayitlar.referans_id', '=', session('FILTRE_SIRKET_ID'));
        }
        if((int) session('PAST_FILTRE_HOCA_ODEME') == 1) { // odeme yapılmış
            $query->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is not null and egitim_hocalar.ucret) > 0");
            $filtre_egitim_odeme->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is not null and egitim_hocalar.ucret) > 0");
        } else if ( (int) session('PAST_FILTRE_HOCA_ODEME') == 2) { // odeme yapılmamış
            $query->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret) > 0");
            $filtre_egitim_odeme->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret) > 0");
        }
        if(intval(session('FILTRE_EGITIM_ODEME')) == 1) {
            $query->where('teklifler.flg_odendi', '1');
        } else if(intval(session('FILTRE_EGITIM_ODEME')) == 2) {
            $query->where('teklifler.flg_odendi', '0');
        }

        session(['PREFIX' => 'cc_now']);

        $data = [
            'filtre_yil' => session('FILTRE_YIL'),
            'filtre_yil_liste' => $filtre_yil_liste->get(),
            'filtre_ulke_id' => session('FILTRE_ULKE_ID'),
            'filtre_ulke_liste' => $filtre_ulke_liste->get(),
            'filtre_ref_sirket_id' => session('FILTRE_SIRKET_ID'),
            'filtre_ref_sirket_liste' => $filtre_ref_sirket_liste->get(),
            'filtre_hoca_odeme' => session('FILTRE_HOCA_ODEME'),
            'filtre_hoca_odeme_liste' => $filtre_hoca_odeme->first(),
            'filtre_egitim_odeme' => session('FILTRE_EGITIM_ODEME'),
            'filtre_egitim_odeme_liste' => $filtre_egitim_odeme->first(),
            'liste' => $query->get(),
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Confirmed Courses'
        ];
        return view('teklifler.view', $data);
    }

    public function searchPast(Request $request) {
        session(['PAST_FILTRE_YIL' => $request->filtre_yil]);
        session(['PAST_FILTRE_ULKE_ID' => $request->filtre_ulke_id]);
        session(['PAST_FILTRE_SIRKET_ID' => $request->filtre_sirket_id]);
        session(['PAST_FILTRE_HOCA_ODEME' => $request->filtre_hoca_odeme]);
        session(['PAST_FILTRE_EGITIM_ODEME' => $request->filtre_egitim_odeme]);

        return redirect('/'.session('PREFIX'));
    }

    public function cc_past() {
        if(!Auth::user()->isAllow('cca_ccp_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        if(session()->has('PAST_FILTRE_YIL')) {
        } else {
            session(['PAST_FILTRE_YIL' => date("Y")]);
        }

        $query = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'teklifler.id', '=', 'egitim_kayitlar.ref_teklif_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->where('teklifler.flg_silindi', 0)
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) <= curdate()')
            ->orderby('egitim_tarihleri.baslama_tarihi', 'desc')
            ->orderby('teklifler.id', 'desc')
            ->select('teklifler.*', 'egitim_tarihleri.baslama_tarihi');



        $filtre_yil_liste = Teklifler::where('teklifler.durum', 2)
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) < curdate()')
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->selectraw('year(egitim_tarihleri.baslama_tarihi) as yil')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('yil')
            ->orderby('yil', 'desc');

        $filtre_ulke_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) < curdate()')
            ->wherenotnull('ulkeler.id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc');

        $filtre_ref_sirket_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('referanslar', 'referanslar.id', '=', 'egitim_kayitlar.referans_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) < curdate()')
            ->wherenotnull('egitim_kayitlar.referans_id')
            ->select('referanslar.id', 'referanslar.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('referanslar.id')
            ->orderby('referanslar.adi', 'asc');

        $filtre_hoca_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('egitim_hocalar', 'egitim_hocalar.teklif_id', '=', 'teklifler.id')
            // ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) < curdate()')
            ->wherenotnull('egitim_hocalar.id')
            ->selectraw('count(if(egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret > 0, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(egitim_hocalar.odeme_yapilma_tarih is not null, teklifler.id, null)) as paid_sayi');

        $filtre_egitim_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) < curdate()')
            ->selectraw('count(if(teklifler.flg_odendi = 0, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(teklifler.flg_odendi = 1, teklifler.id, null)) as paid_sayi');

        if(intval(session('PAST_FILTRE_YIL')) > 0) {
            $query->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('PAST_FILTRE_YIL'));
            $filtre_ulke_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('PAST_FILTRE_YIL'));
            $filtre_ref_sirket_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('PAST_FILTRE_YIL'));
            $filtre_hoca_odeme->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('PAST_FILTRE_YIL'));
            $filtre_egitim_odeme->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('PAST_FILTRE_YIL'));
        }
        if(intval(session('PAST_FILTRE_ULKE_ID'))) {
            $query->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('PAST_FILTRE_ULKE_ID'));
            $filtre_ref_sirket_liste->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('PAST_FILTRE_ULKE_ID'));
            $filtre_hoca_odeme->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('PAST_FILTRE_ULKE_ID'));
            $filtre_egitim_odeme->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('PAST_FILTRE_ULKE_ID'));
        }
        if(intval(session('PAST_FILTRE_SIRKET_ID'))) {
            $query->where('egitim_kayitlar.referans_id', '=', session('PAST_FILTRE_SIRKET_ID'));
            $filtre_hoca_odeme->where('egitim_kayitlar.referans_id', '=', session('PAST_FILTRE_SIRKET_ID'));
            $filtre_egitim_odeme->where('egitim_kayitlar.referans_id', '=', session('PAST_FILTRE_SIRKET_ID'));
        }
        if((int) session('PAST_FILTRE_HOCA_ODEME') == 1) { // odeme yapılmış
            $query->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is not null and egitim_hocalar.ucret) > 0");
            $filtre_egitim_odeme->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is not null and egitim_hocalar.ucret) > 0");
        } else if ( (int) session('PAST_FILTRE_HOCA_ODEME') == 2) { // odeme yapılmamış
            $query->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret) > 0");
            $filtre_egitim_odeme->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret) > 0");
        }
        if(intval(session('PAST_FILTRE_EGITIM_ODEME')) == 1) {
            $query->where('teklifler.flg_odendi', '1');
        } else if(intval(session('PAST_FILTRE_EGITIM_ODEME')) == 2) {
            $query->where('teklifler.flg_odendi', '0');
        }
        //echo $filtre_hoca_odeme->toSql();
        //die('dddd');

        session(['PREFIX' => 'cc_past']);

        $data = [
            'filtre_yil' => session('PAST_FILTRE_YIL'),
            'filtre_yil_liste' => $filtre_yil_liste->get(),
            'filtre_ulke_id' => session('PAST_FILTRE_ULKE_ID'),
            'filtre_ulke_liste' => $filtre_ulke_liste->get(),
            'filtre_ref_sirket_id' => session('PAST_FILTRE_SIRKET_ID'),
            'filtre_ref_sirket_liste' => $filtre_ref_sirket_liste->get(),
            'filtre_hoca_odeme' => session('PAST_FILTRE_HOCA_ODEME'),
            'filtre_hoca_odeme_liste' => $filtre_hoca_odeme->first(),
            'filtre_egitim_odeme' => session('PAST_FILTRE_EGITIM_ODEME'),
            'filtre_egitim_odeme_liste' => $filtre_egitim_odeme->first(),
            'liste' => $query->get(),
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Confirmed Courses Past'
        ];
        return view('teklifler.view_past', $data);
    }

    public function filtreUlkeGetirJSON(Request $request) {
        $ulke_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->wherenotnull('ulkeler.id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc');
        if(!empty($request->filtre_yil)) {
            $ulke_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = ?', $request->filtre_yil);
        }

        return response()->json($ulke_liste->get());
    }

    public function filtreSirketGetirJSON(Request $request) {
        $filtre_ref_sirket_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('referanslar', 'referanslar.id', '=', 'egitim_kayitlar.referans_id')
            ->wherenotnull('egitim_kayitlar.referans_id')
            ->select('referanslar.id', 'referanslar.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('referanslar.id')
            ->orderby('referanslar.adi', 'asc');
        if(!empty($request->filtre_yil)) {
            $filtre_ref_sirket_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = ?', $request->filtre_yil);
        }
        if(!empty($request->filtre_ulke_id)) {
            $filtre_ref_sirket_liste->where('egitim_kayitlar.sirket_ulke_id', $request->filtre_ulke_id);
        }

        return response()->json($filtre_ref_sirket_liste->get());
    }

    public function filtreHocaOdemeGetirJSON(Request $request) {
        $filtre_hoca_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('egitim_hocalar', 'egitim_hocalar.teklif_id', '=', 'teklifler.id')
            ->wherenotnull('egitim_hocalar.id')
            ->selectraw('count(if(egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret > 0, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(egitim_hocalar.odeme_yapilma_tarih is not null, teklifler.id, null)) as paid_sayi');
        if(!empty($request->filtre_yil)) {
            $filtre_hoca_odeme->whereraw('year(egitim_tarihleri.baslama_tarihi) = ?', $request->filtre_yil);
        }
        if(!empty($request->filtre_ulke_id)) {
            $filtre_hoca_odeme->where('egitim_kayitlar.sirket_ulke_id', $request->filtre_ulke_id);
        }
        if(!empty($request->filtre_sirket_id) && $request->filtre_sirket_id > 0) {
            $filtre_hoca_odeme->where('egitim_kayitlar.referans_id', $request->filtre_sirket_id);
        }
        // echo $filtre_hoca_odeme->toSql();
        // die();
        return response()->json($filtre_hoca_odeme->first());
    }

    public function filtreKursOdemeGetirJSON(Request $request) {
        $filtre_egitim_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->selectraw('count(if(teklifler.flg_odendi = 0, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(teklifler.flg_odendi = 1, teklifler.id, null)) as paid_sayi');
        if(!empty($request->filtre_yil)) {
            $filtre_egitim_odeme->whereraw('year(egitim_tarihleri.baslama_tarihi) = ?', $request->filtre_yil);
        } else {
            $filtre_egitim_odeme->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) < curdate()');
        }
        if(!empty($request->filtre_ulke_id)) {
            $filtre_egitim_odeme->where('egitim_kayitlar.sirket_ulke_id', $request->filtre_ulke_id);
        }
        if(!empty($request->filtre_sirket_id) && $request->filtre_sirket_id > 0) {
            $filtre_egitim_odeme->where('egitim_kayitlar.referans_id', $request->filtre_sirket_id);
        }

        return response()->json($filtre_egitim_odeme->first());
    }

    public function cc_past_eski() {
        $liste = Teklifler::where('teklifler.durum', 2)
            ->wherenotnull('teklif_gon_tarih')
            ->where('teklifler.flg_silindi', 0)
            ->leftjoin('egitim_kayitlar', 'teklifler.id', '=', 'egitim_kayitlar.ref_teklif_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->whereHas('egitimKayit', function (Builder $query) {
                $query->wherenull('deleted_at');
            })
            ->whereHas('egitimKayit.egitimTarihi', function (Builder $query) {
                $query->whereRaw('baslama_tarihi < curdate()');
            })
            ->orderby('egitim_tarihleri.baslama_tarihi', 'desc')
            ->orderby('teklifler.created_at', 'desc')
            ->orderby('teklifler.id', 'desc')
            ->select('teklifler.*')
            ->paginate(10);
        $data = [
            'liste' => $liste,
            'alt_baslik' => 'Confirmed Courses Past'
        ];
        return view('teklifler.past_view', $data);
    }

    public function courseAssignMailView($prefix, $egitim_hoca_id) {
        $hoca = EgitimHocalar::find($egitim_hoca_id);
        //return $hoca->hocaBilgi;
        $teklif = Teklifler::find($hoca->teklif_id);
        $sablon = EmailSablon::find(12);

        $hoca_tarihleri = EgitimHocalar::where('teklif_id', $hoca->teklif_id)
            ->where('hoca_id', $hoca->hoca_id)
            ->orderby('ders_tarihi')
            ->orderby('ders_sira')
            ->orderby('saat_sira')
            ->select('ders_tarihi', 'ders_sira')
            ->get();
        $hoca_tarihleri_txt = "";
        foreach($hoca_tarihleri as $row){
            $hoca_tarihleri_txt .= ($hoca_tarihleri_txt != "" ? "<br>" : "")."Day ".($row->ders_sira + 1)." Topics --> ".date('d.m.Y', strtotime($row->ders_tarihi));
        }

        $data = [
            'hid_egitim_hoca_id' => $egitim_hoca_id,
            'data' => [
                'from_email' => $sablon->alan4,
                'to_email' => $hoca->hocaBilgi->sahsi_email,
                'konu' => str_replace([
                    '{COURSE_TITLE}',
                    '{COURSE_DATE}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{COURSE_CODE}',
                        '{COURSE_TITLE}',
                        '{EGITMEN_ADI}',
                        '{COURSE_LINK}',
                        '{COURSE_DATE}',
                        '{COURSE_MATERIAL}',
                        '{COURSE_DELIVERY}',
                        '{FEE}',
                        '{HOCA_TARIHLERI}',
                        '{COURSE_LOCATION}',
                        '{COURSE_LOCATION_LINK}',
                    ],
                    [
                        $teklif->egitimKayit->egitimler->kodu,
                        $teklif->egitimKayit->egitimler->adi,
                        $hoca->hocaBilgi->adi_soyadi,
                        '',
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi." -2 days")),
                        $teklif->egitimKayit->egitimTarihi->egitim_suresi." ".$teklif->egitimKayit->egitimTarihi->egitimPart->adi,
                        $hoca->ucret,
                        $hoca_tarihleri_txt,
                        $teklif->kursyeri->otelBilgi->adi,
                        "<a href=\"".$teklif->kursyeri->otelBilgi->web_adresi."\">".$teklif->kursyeri->otelBilgi->web_adresi."</a>",
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];

        return view('teklifler.kursatama_mail_view', $data);
    }

    public function courseAssignMailSend(Request $request, $prefix, $egitim_hoca_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'to_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'to_email.required' => 'To receive is required.',
                'to_email.email' => 'To receive is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            EgitimHocalar::find($egitim_hoca_id)->update([
                'dersatama_mail' => date('Y-m-d H:i:s')
            ]);

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);


            SendEmail::create([
                'oncelik' => 5,
                'konu' => $request->konu,
                'from_email' => $request->from_email,
                'to_email' => trim($request->to_email),
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'icerik' => str_replace('{ODENEN_MIKTAR}', $request->ucret." TL", $request->icerik),
                'ekler' => $ekler
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function coursePaymentMailView($prefix, $egitim_hoca_id) {
        $hoca = EgitimHocalar::find($egitim_hoca_id);
        //return $hoca->hocaBilgi;
        $teklif = Teklifler::find($hoca->teklif_id);
        $sablon = EmailSablon::find(11);

        $hoca_tarihleri = EgitimHocalar::where('teklif_id', $hoca->teklif_id)
            ->where('hoca_id', $hoca->hoca_id)
            ->orderby('ders_tarihi')
            ->orderby('ders_sira')
            ->orderby('saat_sira')
            ->select('ders_tarihi', 'ders_sira')
            ->get();
        $hoca_tarihleri_txt = "";
        foreach($hoca_tarihleri as $row){
            $hoca_tarihleri_txt .= ($hoca_tarihleri_txt != "" ? "<br>" : "")."Day ".($row->ders_sira + 1)." Topics --> ".date('d.m.Y', strtotime($row->ders_tarihi));
        }

        $data = [
            'hid_egitim_hoca_id' => $egitim_hoca_id,
            'data' => [
                'ucret' => $hoca->ucret,
                'from_email' => $sablon->alan4,
                'to_email' => $hoca->hocaBilgi->sahsi_email,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{EGITIM_ADI}',
                        '{EGITMEN_ADI}',
                        '{EGITIM_TARIHI}',
                    ],
                    [
                        $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                        $hoca->hocaBilgi->adi_soyadi,
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];

        return view('teklifler.hocaodeme_mail_view', $data);
    }

    public function coursePaymentMailSend(Request $request, $prefix, $egitim_hoca_id) {
        try {
            $rules = [
                'ucret' => 'required|numeric',
                'from_email' => 'required|email',
                'to_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'ucret.required' => 'Payment Made is required',
                'ucret.numeric' => 'Payment Made must be number',
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'to_email.required' => 'To receive is required.',
                'to_email.email' => 'To receive is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            EgitimHocalar::find($egitim_hoca_id)->update([
                'ucret' => $request->ucret
            ]);
            if(isset($request->mail_gonder)) {
                $ekler1 = $ekler2 = "";
                if($request->file("ekler1"))
                    $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
                if($request->file("ekler2"))
                    $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
                $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);


                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($request->to_email),
                    'cc' => $request->cc,
                    'bcc' => $request->bcc,
                    'icerik' => str_replace('{ODENEN_MIKTAR}', $request->ucret." TL", $request->icerik),
                    'ekler' => $ekler
                ]);

                EgitimHocalar::find($egitim_hoca_id)->update([
                    'odeme_yapilma_tarih' => date('Y-m-d H:i:s')
                ]);
            }
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }

    }

    public function instructorXSetup(Request $request, $prefix, $teklif_id) {

        $teklif = Teklifler::findorfail($teklif_id);
        $baslama_tarihi = date('Y-m-d', strtotime($teklif->egitimKayit->egitimTarihi['baslama_tarihi']));
        $egitim_suresi = $teklif->egitimKayit->egitimTarihi['egitim_suresi'];
        $bitis_tarihi = date('Y-m-d', strtotime($baslama_tarihi." +".$egitim_suresi." days"));
        $hocalar = User::where('flg_durum', 0)->leftjoin('kullanici_rolleri', 'kullanici_rolleri.kullanici_id', '=', 'kullanicilar.id')
            ->leftjoin('egitmenler', 'egitmenler.kullanici_id', '=', 'kullanicilar.id')
            ->leftjoin('unvanlar', 'unvanlar.id', '=', 'egitmenler.unvan_id')
            ->where('kullanici_rolleri.rol_id', 4)
            ->orderby('kullanicilar.adi_soyadi', 'asc')
            ->select('unvanlar.adi as unvan_adi', 'kullanicilar.adi_soyadi', 'kullanicilar.id')
            ->get();

        $hocalar = Egitmenler::where('egitmenler.durum', 3)
            ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmenler.kullanici_id')
            ->where('egitmenler.flg_silindi', 0)
            ->where('kullanicilar.flg_durum', 1)
            ->orderby('egitmenler.adi_soyadi')
            ->select('egitmenler.*')
            ->get();

        $atanan_liste = EgitimHocalar::where('teklif_id', $teklif_id)
            ->orderby('ders_tarihi', 'asc')
            ->orderby('baslama_saati', 'asc')
            ->get();
        $data = [
            'bilgi' => $teklif,
            'baslama_tarihi' => $baslama_tarihi,
            'bitis_tarihi' => $bitis_tarihi,
            'hocalar' => $hocalar,
            'atanan_liste' => $atanan_liste,
            'data' => new EgitimHocalar(),
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.hoca_atama', $data);
    }

    public function instructorXSetupEdit($prefix, $teklif_id, $egitim_hoca_id) {
        $teklif = Teklifler::findorfail($teklif_id);
        $baslama_tarihi = date('Y-m-d', strtotime($teklif->egitimKayit->egitimTarihi['baslama_tarihi']));
        $egitim_suresi = $teklif->egitimKayit->egitimTarihi['egitim_suresi'];
        $bitis_tarihi = date('Y-m-d', strtotime($baslama_tarihi." +".$egitim_suresi." days"));
        $hocalar = User::where('flg_durum', 0)->leftjoin('kullanici_rolleri', 'kullanici_rolleri.kullanici_id', '=', 'kullanicilar.id')
            ->leftjoin('egitmenler', 'egitmenler.kullanici_id', '=', 'kullanicilar.id')
            ->leftjoin('unvanlar', 'unvanlar.id', '=', 'egitmenler.unvan_id')
            ->where('kullanici_rolleri.rol_id', 4)
            ->orderby('kullanicilar.adi_soyadi', 'asc')
            ->select('unvanlar.adi as unvan_adi', 'kullanicilar.adi_soyadi', 'kullanicilar.id')
            ->get();

        $hocalar = Egitmenler::where('egitmenler.durum', 3)
            ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmenler.kullanici_id')
            ->where('egitmenler.flg_silindi', 0)
            ->where('kullanicilar.flg_durum', 1)
            ->orderby('egitmenler.adi_soyadi')
            ->select('egitmenler.*')
            ->get();

        $atanan_liste = EgitimHocalar::where('teklif_id', $teklif_id)
            ->orderby('ders_tarihi', 'asc')
            ->orderby('baslama_saati', 'asc')
            ->get();
        $data = [
            'bilgi' => $teklif,
            'baslama_tarihi' => $baslama_tarihi,
            'bitis_tarihi' => $bitis_tarihi,
            'hocalar' => $hocalar,
            'atanan_liste' => $atanan_liste,
            'data' => EgitimHocalar::find($egitim_hoca_id),
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.hoca_atama', $data);
    }

    public function kursuVerecekHocaTercihi() {
        $result = Teklifler::where('teklifler.durum', '=', 2)
            ->whereHas('egitimKayit.egitimTarihi', function ($query) {
                $query->whereRaw("baslama_tarihi >= curdate()");
            })
            ->leftJoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftJoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->orderBy('egitim_tarihleri.baslama_tarihi', 'asc')
            ->select('teklifler.*')
            // ->toSql();
            ->paginate(100);

        //return $result;

        session(['PREFIX' => 'iap_future']);

        $data = [
            'liste' => $result,
            'prefix' => 'iap_future',
            'alt_baslik' => 'Future Courses'
        ];

        return view('egitmen.kurstalip_view', $data);
    }

    public function kursuVerecekHocaTercihiGecmis() {
        $result = Teklifler::where('teklifler.durum', '=', 2)
            ->whereHas('egitimKayit.egitimTarihi', function ($query) {
                $query->whereRaw("baslama_tarihi < curdate()");
            })
            ->leftJoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftJoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->orderBy('egitim_tarihleri.baslama_tarihi', 'desc')
            ->select('teklifler.*')
            // ->toSql();
            ->paginate(100);

        //return $result;

        session(['PREFIX' => 'iap_past']);

        $data = [
            'liste' => $result,
            'prefix' => 'iap_past',
            'alt_baslik' => 'Past Courses'
        ];

        return view('egitmen.kurstalip_view', $data);
    }

    public function kursIptalMailGonderim($prefix, $teklif_id, $kayitlar_ids, Request $request) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(35);
        $to_receive_text = "";
        //return explode(',', $request->hid_kayit_ids);
        foreach(EgitmenKursTalip::wherein('egitmen_kurstalip.id', explode(',', $kayitlar_ids))
            ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmen_kurstalip.kullanici_id')
            ->select('kullanicilar.adi_soyadi', 'kullanicilar.email')
            ->get() as $row) {
            $to_receive_text .= "<div>$row->adi_soyadi < $row->email ></div>";
        }

        $data = [
            'hid_kayit_ids' => $kayitlar_ids,
            'teklif_id' => $teklif_id,
            'data' => [
                'from_email' => $sablon->alan4,
                'to_receive_text' => $to_receive_text,
                'konu' => str_replace([
                        '{COURSE_TITLE}',
                        '{COURSE_DATE}'
                    ], [
                        $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                    ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{COURSE_CODE}',
                        '{COURSE_TITLE}',
                        '{COURSE_LINK}',
                        '{COURSE_DATE}',
                    ],
                    [
                        $teklif->egitimKayit->egitimler->kodu,
                        $teklif->egitimKayit->egitimler->adi,
                        '',
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('egitmen.kurstalip_iptal_email', $data);
    }

    public function kursIptalMailGonderimSend($prefix, Request $request) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'hid_kayit_ids' => 'required',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'hid_kayit_ids.required' => 'To receive is required.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);

            $egitmen = EgitmenKursTalip::wherein('egitmen_kurstalip.id', explode(',', $request->hid_kayit_ids))
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmen_kurstalip.kullanici_id')
                ->select('kullanicilar.adi_soyadi', 'kullanicilar.email', 'egitmen_kurstalip.id')
                ->get();
            $gon_mail_add = array();
            foreach($egitmen as $row) {
                if(!in_array(trim($row->email), $gon_mail_add) ) {
                    SendEmail::create([
                        'oncelik' => 5,
                        'konu' => $request->konu,
                        'from_email' => $request->from_email,
                        'to_email' => trim($row->email),
                        'cc' => $request->cc,
                        'bcc' => $request->bcc,
                        'icerik' => str_replace('{EGITMEN_ADI}', $row->adi_soyadi, $request->icerik),
                        'ekler' => $ekler
                    ]);
                }

                EgitmenKursTalip::find($row->id)->update([
                    'iptal_mail_tarihi' => date('Y-m-d H:i:s'),
                    'iptal_mail_gonderen' => Auth::user()->id
                ]);


            }
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die('dddd');
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function kursMailGonderim($prefix, $kayit_id, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(40);

        $row = EgitmenKursTalip::where('egitmen_kurstalip.id', $kayit_id)
                    ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmen_kurstalip.kullanici_id')
                    ->select('kullanicilar.adi_soyadi', 'kullanicilar.email')
                    ->first();


        $data = [
            'hid_kayit_id' => $kayit_id,
            'teklif_id' => $teklif_id,
            'data' => [
                'from_email' => $sablon->alan4,
                'to_email' => $row->email,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{BASLAMA_TARIH}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{HOCA_ADI}',
                        '{EGITIM_ADI}',
                        '{BASLAMA_TARIH}',
                    ],
                    [
                        $row->adi_soyadi,
                        trim($teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi),
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('egitmen.kurstalip_mail', $data);
    }

    public function kursMailGonderimSend(Request $request) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'to_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'to_email.required' => 'To receive is required.',
                'to_email.email' => 'To receive is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            SendEmail::create([
                'oncelik' => 5,
                'konu' => $request->konu,
                'from_email' => $request->from_email,
                'to_email' => $request->to_email,
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'icerik' => $request->icerik,
                'ekler' => $ekler
            ]);

            EgitmenKursTalip::find($request->hid_kayit_id)->update([
                'onay_mail_tarihi' => date('Y-m-d H:i:s'),
                'onay_mail_gonderen' => Auth::user()->id
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function searchCCB(Request $request) {
        session(['CCB_FILTRE_YIL' => $request->filtre_yil]);
        session(['CCB_FILTRE_ULKE_ID' => $request->filtre_ulke_id]);
        session(['CCB_FILTRE_SIRKET_ID' => $request->filtre_sirket_id]);
        session(['CCB_FILTRE_HOCA_ODEME' => $request->filtre_hoca_odeme]);
        session(['CCB_FILTRE_EGITIM_ODEME' => $request->filtre_egitim_odeme]);

        return redirect('/ccb');
    }

    public function ccb_view() {
        if(!Auth::user()->isAllow('ccb_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $filtre_yil_liste = Teklifler::where('teklifler.durum', 2)
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->selectraw('year(egitim_tarihleri.baslama_tarihi) as yil')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('yil')
            ->orderby('yil', 'desc');

        $filtre_ulke_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->wherenotnull('ulkeler.id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc');

        $filtre_ref_sirket_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('referanslar', 'referanslar.id', '=', 'egitim_kayitlar.referans_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->wherenotnull('egitim_kayitlar.referans_id')
            ->select('referanslar.id', 'referanslar.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('referanslar.id')
            ->orderby('referanslar.adi', 'asc');

        $filtre_hoca_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('egitim_hocalar', 'egitim_hocalar.teklif_id', '=', 'teklifler.id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->wherenotnull('egitim_hocalar.id')
            ->selectraw('count(if(egitim_hocalar.odeme_yapilma_tarih is null, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(egitim_hocalar.odeme_yapilma_tarih is not null, teklifler.id, null)) as paid_sayi');

        $filtre_egitim_odeme = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()')
            ->selectraw('count(if(teklifler.flg_odendi = 0, teklifler.id, null)) as unpaid_sayi')
            ->selectraw('count(if(teklifler.flg_odendi = 1, teklifler.id, null)) as paid_sayi');

        $query = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'teklifler.id', '=', 'egitim_kayitlar.ref_teklif_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->orderby('egitim_tarihleri.baslama_tarihi', 'asc')
            ->select('teklifler.*')
            ;
        if((int) session('CCB_FILTRE_YIL') > 0) {
            $query->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('CCB_FILTRE_YIL'));
            $filtre_ulke_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('CCB_FILTRE_YIL'));
            $filtre_ref_sirket_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('CCB_FILTRE_YIL'));
        } else {
            $query->whereraw('date_add(egitim_tarihleri.baslama_tarihi, INTERVAL egitim_tarihleri.egitim_suresi day) >= curdate()');
        }
        if((int) session('CCB_FILTRE_ULKE_ID')) {
            $query->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('CCB_FILTRE_ULKE_ID'));
            $filtre_ref_sirket_liste->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('CCB_FILTRE_ULKE_ID'));
        }
        if(intval(session('CCB_FILTRE_SIRKET_ID'))) {
            $query->where('egitim_kayitlar.referans_id', '=', session('CCB_FILTRE_SIRKET_ID'));
        }
        if((int) session('CCB_FILTRE_HOCA_ODEME') == 1) { // odeme yapılmış
            $query->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is not null and egitim_hocalar.ucret) > 0");
            $filtre_egitim_odeme->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is not null and egitim_hocalar.ucret) > 0");
        } else if ( (int) session('CCB_FILTRE_HOCA_ODEME') == 2) { // odeme yapılmamış
            $query->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret) > 0");
            $filtre_egitim_odeme->whereraw("(select count(1) from egitim_hocalar where egitim_hocalar.teklif_id = teklifler.id and
                egitim_hocalar.odeme_yapilma_tarih is null and egitim_hocalar.ucret) > 0");
        }
        if(intval(session('CCB_FILTRE_EGITIM_ODEME')) == 1) {
            $query->where('teklifler.flg_odendi', '1');
        } else if(intval(session('CCB_FILTRE_EGITIM_ODEME')) == 2) {
            $query->where('teklifler.flg_odendi', '0');
        }
        //echo session('CCB_FILTRE_YIL')." -- ";die();
        //echo $query->toSql(); die();
        $data = [
            'liste' => $query->paginate(100),
            'prefix' => 'ccb',
            'alt_baslik' => 'List Record',
            'filtre_yil_liste' => $filtre_yil_liste->get(),
            'filtre_yil' => session('CCB_FILTRE_YIL') ?? null,
            'filtre_ulke_liste' => $filtre_ulke_liste->get(),
            'filtre_ulke_id' => session('CCB_FILTRE_ULKE_ID') ?? null,
            'filtre_ref_sirket_liste' => $filtre_ref_sirket_liste->get(),
            'filtre_ref_sirket_id' => session('CCB_FILTRE_SIRKET_ID') ?? null,
            'filtre_hoca_odeme_liste' => $filtre_hoca_odeme->first(),
            'filtre_hoca_odeme' => session('CCB_FILTRE_HOCA_ODEME') ?? null,
            'filtre_egitim_odeme_liste' => $filtre_egitim_odeme->first(),
            'filtre_egitim_odeme' => session('CCB_FILTRE_EGITIM_ODEME') ?? null
        ];

        return view('teklifler.ccb_view', $data);
    }

    public function assignTrainingLocationView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $hafta = date('W', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi));
        $yil = date('Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi));
        $liste = KursYeri::whereraw("week(date_add(kurs_yeri.baslama_tarihi, interval +1 day)) = '$hafta'")
            ->whereraw("year(kurs_yeri.baslama_tarihi) = $yil")
            ->leftjoin('otl_oteller', 'otl_oteller.id', '=', 'kurs_yeri.otel_id')
            ->leftjoin('teklifler', 'teklifler.id', '=', 'kurs_yeri.teklif_id')
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftjoin('egitimler', 'egitimler.id', '=', 'egitim_kayitlar.egitim_id')
            ->leftjoin('otl_sehir', 'otl_sehir.id', '=', 'otl_oteller.sehir_id')
            ->leftjoin('otl_bolge', 'otl_bolge.id', '=', 'otl_oteller.bolge_id')
            ->select('kurs_yeri.id', 'kurs_yeri.teklif_id', 'otl_oteller.adi as otel_adi',
                'kurs_yeri.oda_adi', 'egitimler.adi as egitim_adi', 'otl_sehir.adi as sehir_adi',
                'otl_bolge.adi as bolge_adi', 'kurs_yeri.kisi_sayisi', 'kurs_yeri.baslama_tarihi', 'kurs_yeri.kac_gun',
                'kurs_yeri.ucret', 'kurs_yeri.flg_odendi', 'kurs_yeri.otel_id')
            ->selectraw('week(kurs_yeri.baslama_tarihi, 5) as hafta')
            ->orderbyraw('if(kurs_yeri.teklif_id is null, 0, 1) asc')
            ->orderby('kurs_yeri.baslama_tarihi', 'asc');
        //return $liste->toSql();


        //return $liste;
        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
            'hafta' => $hafta,
            'liste' => $liste->get(),
            'teklif_id' => $teklif_id,
        ];
        return view('teklifler.kursyeri_atama_view', $data);
    }

    public function setAssignTrainingLocation($prefix, $kurs_yeri_id, $teklif_id) {
        try {
            KursYeri::where('teklif_id', $teklif_id)->update([
                'teklif_id' => null
            ]);
            KursYeri::find($kurs_yeri_id)->update([
                'teklif_id' => $teklif_id
            ]);
            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }
    }

    public function unsetAssignTrainingLocation($prefix, $kurs_yeri_id, $teklif_id) {
        try {
            KursYeri::where('teklif_id', $teklif_id)->update([
                'teklif_id' => null
            ]);
            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }
    }

    public function meetingRoomReservationView($prefix, $tarih, $kurs_yeri_id=null) {
        $hafta = date('W', strtotime($tarih));
        $yil = date('Y', strtotime($tarih));
        $liste = KursYeri::whereraw("week(date_add(kurs_yeri.baslama_tarihi, interval +1 day)) = '$hafta'")
            ->whereraw("year(kurs_yeri.baslama_tarihi) = $yil")
            ->leftjoin('otl_oteller', 'otl_oteller.id', '=', 'kurs_yeri.otel_id')
            ->leftjoin('teklifler', 'teklifler.id', '=', 'kurs_yeri.teklif_id')
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftjoin('egitimler', 'egitimler.id', '=', 'egitim_kayitlar.egitim_id')
            ->leftjoin('otl_sehir', 'otl_sehir.id', '=', 'otl_oteller.sehir_id')
            ->leftjoin('otl_bolge', 'otl_bolge.id', '=', 'otl_oteller.bolge_id')
            ->select('kurs_yeri.id', 'kurs_yeri.teklif_id', 'otl_oteller.adi as otel_adi',
                'kurs_yeri.oda_adi', 'egitimler.adi as egitim_adi', 'otl_sehir.adi as sehir_adi',
                'otl_bolge.adi as bolge_adi', 'kurs_yeri.kisi_sayisi', 'kurs_yeri.baslama_tarihi', 'kurs_yeri.kac_gun',
                'kurs_yeri.ucret', 'kurs_yeri.flg_odendi', 'kurs_yeri.otel_id')
            ->selectraw('week(kurs_yeri.baslama_tarihi, 5) as hafta')
            ->orderbyraw('if(kurs_yeri.teklif_id is null, 0, 1) asc')
            ->orderby('kurs_yeri.baslama_tarihi', 'asc');
        //return $liste->toSql();

        if($kurs_yeri_id > 0)
            $kurs_yeri = KursYeri::find($kurs_yeri_id);
        else
            $kurs_yeri = new KursYeri();

        $data = [
            'kurs_yeri_id' => $kurs_yeri_id ?? '',
            'prefix' => session('PREFIX'),
            'tarih' => $tarih,
            'alt_baslik' => "Week ".$hafta,
            'hafta' => $hafta,
            'yil' => $yil,
            'liste' => $liste->get(),
            'sehirler' => OtelSehirleri::orderby('adi')->get(),
            'data' => [
                'id' => $kurs_yeri->id,
                'sehir_id' => $kurs_yeri->otelBilgi->sehir_id,
                'bolge_id' => $kurs_yeri->otelBilgi->bolge_id,
                'otel_id' => $kurs_yeri->otel_id,
                'oda_adi' => $kurs_yeri->oda_adi,
                'kisi_sayisi' => $kurs_yeri->kisi_sayisi,
                'kac_gun' => $kurs_yeri->kac_gun,
                'baslama_tarihi' => $kurs_yeri->baslama_tarihi,
                'ucret' => $kurs_yeri->ucret,

            ],
            'tmp_tarih_bas' => date("d.m.Y", strtotime($yil."W".$hafta." +5 days")),
            'tmp_tarih_bit' => date("d.m.Y", strtotime($yil."W".$hafta." +11 days")),
        ];
        return view('teklifler.kursyeri_listesi_view', $data);
    }

    public function meetingRoomReservationBolgeGetirJson(Request $request) {
        $result = OtelBolgeleri::where('sehir_id', $request->sehir_id)
            ->orderby('sira', 'asc')
            ->orderby('adi', 'asc')
            ->select('id', 'adi')
            ->get();
        return response()->json($result);
    }

    public function meetingRoomReservationOtelGetirJson(Request $request) {
        $result = Oteller::where('bolge_id', $request->bolge_id)
            ->orderby('adi', 'asc')
            ->select('id', 'adi', 'flg_saharaofis')
            ->get();
        return response()->json($result);
    }

    public function meetingRoomReservationSave(Request $request, $tmp=null, $id=null) {

        try {
            $rules = [
                'otel_id' => 'required',
                'kisi_sayisi' => 'required|numeric',
                'kac_gun' => 'required|numeric',
                'baslama_tarihi' => 'required',
                'ucret' => 'required|numeric',
            ];
            $error_messages = [
                'otel_id.required' => 'Hotel is required.',
                'kisi_sayisi.required' => 'Number of person is required.',
                'kisi_sayisi.numeric' => 'Number of person is must be number.',
                'kac_gun.required' => 'Number of Day is required.',
                'kac_gun.numeric' => 'Number of Day must be number.',
                'baslama_tarihi.required' => 'Start date is required.',
                'ucret.required' => 'Fee is required.',
                'ucret.numeric' => 'Fee must be number.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }
            if((int) $id > 0) {
                KursYeri::find($id)
                    ->update([
                        'otel_id' => $request->otel_id,
                        'oda_adi' => $request->oda_adi,
                        'kisi_sayisi' => intval($request->kisi_sayisi),
                        'baslama_tarihi' => date("Y-m-d", strtotime($request->baslama_tarihi)),
                        'kac_gun' => intval($request->kac_gun),
                        'ucret' => floatval($request->ucret),
                    ]);
            } else {
                KursYeri::create([
                    'otel_id' => $request->otel_id,
                    'oda_adi' => $request->oda_adi,
                    'kisi_sayisi' => intval($request->kisi_sayisi),
                    'baslama_tarihi' => date("Y-m-d", strtotime($request->baslama_tarihi)),
                    'kac_gun' => intval($request->kac_gun),
                    'ucret' => floatval($request->ucret),
                ]);
            }

            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function meetingRoomReservationUpdate(Request $request, $tmp, $id) {
        echo $id;
    }

    public function meetingRoomReservationDelJson(Request $request) {
        KursYeri::destroy($request->id);

        return response()->json([
            'cvp' => 1,
            'msj' => ''
        ]);
    }

    public function tLocationInstructorMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(13);

        $hocalar_listesi = EgitimHocalar::where('egitim_hocalar.teklif_id', $teklif->id)
            ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitim_hocalar.hoca_id')
            ->where('kullanicilar.id', '>', '0')
            ->select('kullanicilar.adi_soyadi', 'kullanicilar.email')
            ->groupby('kullanicilar.email')
            ->orderby('kullanicilar.adi_soyadi')
            ->get();

        $data = [
            'hid_teklif_id' => $teklif_id,
            'hocalar_listesi' => $hocalar_listesi,
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{EGITIM_ADI}',
                        '{EGITIM_LINK}',
                        '{EGITIM_TARIHI}',
                        '{HOCA_TARIHLERI}',
                        '{EGITIM_YERI}',
                        '{EGITIM_YERI_LINK}',
                    ],
                    [
                        $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                        '',
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi." -2 days")),
                        $teklif->kursyeri->otelBilgi->adi,
                        "<a href=\"".$teklif->kursyeri->otelBilgi->web_adresi."\">".$teklif->kursyeri->otelBilgi->web_adresi."</a>",
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => 'to Instructor(s)',
            'prefix' => session('PREFIX')
        ];

        return view('teklifler.tlocation_instructor_mail', $data);
    }

    public function tLocationInstructorMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
                'secili_email' => 'required_without_all:secili_email',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);

            foreach($request->secili_email as $key => $to_email){
                $kisi = User::where('email', $to_email)->first();
                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($to_email),
                    'cc' => $request->cc,
                    'bcc' => $request->bcc,
                    'icerik' => str_replace('{KISI_ADI}', $kisi->adi_soyadi, $request->icerik),
                    'ekler' => $ekler
                ]);
            }

            KursYeri::where('teklif_id', $teklif_id)->update([
                'mail_egitmen' => date('Y-m-d H:i:s'),
                'mail_egitmen_gonderen' => Auth::user()->id
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function tLocationParticipantMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(24);

        $data = [
            'hid_teklif_id' => $teklif_id,
            'katilimci_listesi' => $teklif->egitimKayit->katilimcilar,
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{EGITIM_ADI}',
                        '{EGITIM_LINK}',
                        '{EGITIM_TARIHI}',
                        '{HOCA_TARIHLERI}',
                        '{EGITIM_YERI}',
                        '{EGITIM_YERI_LINK}',
                        '{PARTICIPANT_OTEL_FIYAT_BILGI}',
                    ],
                    [
                        $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                        '',
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi." -2 days")),
                        $teklif->kursyeri->otelBilgi->adi,
                        "<a href=\"".$teklif->kursyeri->otelBilgi->web_adresi."\">".$teklif->kursyeri->otelBilgi->web_adresi."</a>",
                        '',
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => ' to Participant(s)',
            'prefix' => session('PREFIX')
        ];

        return view('teklifler.tlocation_participant_mail', $data);
    }

    public function tLocationParticipantMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
                'secili_email' => 'required_without_all:secili_email',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);

            foreach($request->secili_email as $key => $to_email){
                $kisi = User::where('email', $to_email)->first();
                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($to_email),
                    'cc' => $request->cc,
                    'bcc' => $request->bcc,
                    'icerik' => $request->icerik,
                    'ekler' => $ekler
                ]);
            }

            KursYeri::where('teklif_id', $teklif_id)->update([
                'mail_katilimci' => date('Y-m-d H:i:s'),
                'mail_katilimci_gonderen' => Auth::user()->id
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function hrAdminMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(41);

        $data = [
            'hid_teklif_id' => $teklif_id,
            'hr_admin_txt' => trim($teklif->egitimKayit->kontakKisiUnvan['adi']." ".$teklif->egitimKayit['ct_adi_soyadi'])." < ".$teklif->egitimKayit->ct_sirket_email." >",
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => $sablon->alan1,
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{KISI_ISIM}',
                    ],
                    [
                        trim($teklif->egitimKayit->kontakKisiUnvan['adi']." ".$teklif->egitimKayit['ct_adi_soyadi'])
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];

        return view('teklifler.hradmin_mail_view', $data);
    }

    public function hrAdminMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);

            $teklif = Teklifler::findorfail($teklif_id);

            SendEmail::create([
                'oncelik' => 5,
                'konu' => $request->konu,
                'from_email' => $request->from_email,
                'to_email' => trim($teklif->egitimKayit->ct_sirket_email),
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'icerik' => $request->icerik,
                'ekler' => $ekler
            ]);


            $teklif->update([
                'hradmin_mail' => date('Y-m-d H:i:s'),
                'hradmin_mail_kisi' => Auth::user()->id,
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function paxCertificateView($prefix, $katilimci_id, $teklif_id) {
        $katilimci = Katilimcilar::find($katilimci_id);
        $teklif = Teklifler::find($teklif_id);
        $baslama_tarihi = $teklif->egitimKayit->egitimTarihi->baslama_tarihi;
        $egitim_sure = $teklif->egitimKayit->egitimTarihi->egitim_suresi;
        $bitis_tarihi = date('Y-m-d', strtotime($baslama_tarihi." +$egitim_sure days"));
        if(date('m', strtotime($baslama_tarihi)) == date('m', strtotime($bitis_tarihi))) {
            $baslama_tarihi = date('d', strtotime($baslama_tarihi));
        } else {
            $baslama_tarihi = date('d F', strtotime($baslama_tarihi));
        }
        $bitis_tarihi = date('d F Y', strtotime($bitis_tarihi));

        $data = [
            'katilimci_id' => $katilimci->id,
            'adi_soyadi' => $katilimci->adi_soyadi,
            'egitim_adi' => $teklif->egitimKayit->egitimler->adi,
            'baslama_tarihi' => $baslama_tarihi,
            'bitis_tarihi' => $bitis_tarihi,
            'egitim_yeri' => $teklif->egitimKayit->egitimTarihi->egitimYeri->adi,
        ];
        return view('teklifler.katilimci_sertifika', $data);
    }

    public function paxExperienceMailView($prefix, $katilimci_id, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(23);

        $katilimci = Katilimcilar::find($katilimci_id);

        $data = [
            'hid_katilimci_id' => $katilimci_id,
            'hid_teklif_id' => $teklif_id,
            'katilimci_adi_txt' => $katilimci->adi_soyadi." < $katilimci->email >",
            'data' => [
                'from_email' => $sablon->alan4,
                'to_email' => $katilimci->email,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{participant_name}',
                        '{COMPANY}',
                        '{COUNTRY}',
                        '{EGITIM_ADI}',
                        '{EGITIM_TARIHI}',
                        '{COURSE_DURATION}',
                    ],
                    [
                        $katilimci->adi_soyadi,
                        $katilimci->is_pozisyonu,
                        $katilimci->yasadigiUlke->adi,
                        trim($teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi),
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        $teklif->egitimKayit->egitimTarihi->egitim_suresi." ".$teklif->egitimKayit->egitimTarihi->egitimPart->adi
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.katilimci_deneyim_mail', $data);
    }

    public function paxExperienceMailSend(Request $request, $prefix, $katilimci_id, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'to_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'to_email.required' => 'To receive is required.',
                'to_email.email' => 'To receive is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            SendEmail::create([
                'oncelik' => 5,
                'konu' => $request->konu,
                'from_email' => $request->from_email,
                'to_email' => $request->to_email,
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'icerik' => $request->icerik,
                'ekler' => $ekler
            ]);

            $teklif = Teklifler::find($teklif_id);

            KatilimcilarEk::updateorcreate([
                'egitim_kayit_id' => $teklif->egitim_kayit_id,
                'teklif_id' => $teklif->id,
                'katilimci_id' => $katilimci_id
            ], [
                'deneyim_mail_tarih' => date('Y-m-d H:i:s'),
                'deneyim_mail_gonderen' => Auth::user()->id
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function allPaxExperienceMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(23);
        $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

        $katilimci_adi_txt = "";
        foreach($katilimcilar as $row) {
            if($row->email == "")
                continue;
            $katilimci_adi_txt .= "<div>$row->adi_soyadi < $row->email ></div>";
        }

        $data = [
            'hid_teklif_id' => $teklif_id,
            'katilimci_adi_txt' => $katilimci_adi_txt,
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{EGITIM_ADI}',
                        '{EGITIM_TARIHI}',
                        '{COURSE_DURATION}',
                    ],
                    [
                        trim($teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi),
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        $teklif->egitimKayit->egitimTarihi->egitim_suresi." ".$teklif->egitimKayit->egitimTarihi->egitimPart->adi
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.tumkatilimci_deneyim_mail', $data);
    }

    public function allPaxExperienceMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            $teklif = Teklifler::find($teklif_id);
            $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

            foreach($katilimcilar as $katilimci) {
                if(trim($katilimci->email) == "")
                    continue;
                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($katilimci->email),
                    'cc' => $request->cc,
                    'bcc' => $request->bcc,
                    'icerik' => str_replace(
                        [
                            '{participant_name}',
                            '{COMPANY}',
                            '{COUNTRY}',
                        ],
                        [
                            $katilimci->adi_soyadi,
                            $katilimci->is_pozisyonu,
                            $katilimci->yasadigiUlke->adi,
                        ],
                        $request->icerik
                    ),
                    'ekler' => $ekler
                ]);

                KatilimcilarEk::updateorcreate([
                    'egitim_kayit_id' => $teklif->egitim_kayit_id,
                    'teklif_id' => $teklif->id,
                    'katilimci_id' => $katilimci->id
                ], [
                    'deneyim_mail_tarih' => date('Y-m-d H:i:s'),
                    'deneyim_mail_gonderen' => Auth::user()->id
                ]);

            }

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function teklifOdemeDurumDegistirJson($prefix, $teklif_id, $flg_odendi) {
        Teklifler::find($teklif_id)->update(['flg_odendi' => $flg_odendi]);

        return response()->json([
            'cvp' => '1',
            'msj' => ''
        ]);
    }

    public function yorumYazModalView(Request $request) {
        $teklif = Teklifler::findorfail($request->teklif_id);
        $data = [
            'teklif_id' => $request->teklif_id,
            'prefix' => session('PREFIX'),
            'yorum' => $teklif->yorum,
            'yorum_tarih' => $teklif->yorum_tarih != "" ? date('d.m.Y', strtotime($teklif->yorum_tarih)) : '',
            'baslik' => $teklif->egitimKayit->egitimler->adi,
        ];


        return view('teklifler.yorum_modal_view', $data);
    }

    public function yorumYazModalSendJson(Request $request) {
        Teklifler::find($request->teklif_id)->update([
            'yorum' => $request->yorum,
            'yorum_tarih' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'cvp' => '1',
            'msj' => ''
        ]);
    }

    public function visaLetterReqMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(9);
        $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

        $katilimci_adi_txt = "";
        foreach($katilimcilar as $row) {
            if($row->email == "")
                continue;
            $katilimci_adi_txt .= "<div>$row->adi_soyadi < $row->email ></div>";
        }

        $data = [
            'hid_teklif_id' => $teklif_id,
            'katilimci_adi_txt' => $katilimci_adi_txt,
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'cc_hradmin' => $teklif->egitimKayit->ct_sirket_email,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{VIZE_BILGI_TOPLAMA_LINK}',
                    ],
                    [
                        "<a href='http://www.saharatraining.com/?visa-invitation,".md5($teklif->id)."'>http://www.saharatraining.com/?visa-invitation,".md5($teklif->id)."</a>",
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.visa_davet_mail_view', $data);
    }

    public function visaLetterReqMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            $teklif = Teklifler::find($teklif_id);
            $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

            $cc = "";
            if(isset($request->hradmin_gonder)) {
                $cc .= $request->cc_hradmin;
            }
            $cc .= ($cc != '' && $request->cc != '' ? ',' : '').$request->cc;

            foreach($katilimcilar as $katilimci) {
                if(trim($katilimci->email) =="")
                    continue;
                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($katilimci->email),
                    'cc' => $cc,
                    'bcc' => $request->bcc,
                    'icerik' => $request->icerik,
                    'ekler' => $ekler
                ]);
            }
            $teklif->update([
                'vdm_tarih' => date('Y-m-d H:i:s'),
                'vdm_gonderen' => Auth::user()->id,
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function visaFormFilledView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $data = [
            'prefix' => session('PREFIX'),
            'hid_teklif_id' => $teklif->id,
            'bilgi' => $teklif,
            'katilimcilar' => Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get(),
            'unvanlar' => Unvanlar::orderby('adi')->get(),
        ];

        return view('teklifler.visa_form_filled', $data);
    }

    public function visaFormFilledSave(Request $request, $prefix, $teklif_id) {
        try {
            $teklif = Teklifler::find($teklif_id);

            foreach($request->katilimci_id as $key => $katilimci_id) {
                if(intval($katilimci_id) > 0) {
                    Katilimcilar::find($katilimci_id)->update([
                        'unvan_id' => $request->unvan_id[$key],
                        'adi_soyadi' => $request->adi_soyadi[$key],
                        'vf_pasaport' => $request->vf_pasaport[$key],
                        'vf_dogum_tarihi' => $request->vf_dogum_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->vf_dogum_tarihi[$key])) : null,
                        'vf_duzenleyen_makam' => $request->vf_duzenleyen_makam[$key],
                        'vf_verilis_tarihi' => $request->vf_verilis_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->vf_verilis_tarihi[$key])) : null,
                        'vf_sonkullanma_tarihi' => $request->vf_sonkullanma_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->vf_sonkullanma_tarihi[$key])) : null,
                    ]);
                } else if(trim($request->adi_soyadi[$key]) != '') {
                    Katilimcilar::create([
                        'egitim_kayit_id' => $teklif->egitim_kayit_id,
                        'unvan_id' => $request->unvan_id[$key],
                        'adi_soyadi' => $request->adi_soyadi[$key],
                        'vf_pasaport' => $request->vf_pasaport[$key],
                        'vf_dogum_tarihi' => $request->vf_dogum_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->vf_dogum_tarihi[$key])) : null,
                        'vf_duzenleyen_makam' => $request->vf_duzenleyen_makam[$key],
                        'vf_verilis_tarihi' => $request->vf_verilis_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->vf_verilis_tarihi[$key])) : null,
                        'vf_sonkullanma_tarihi' => $request->vf_sonkullanma_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->vf_sonkullanma_tarihi[$key])) : null,
                    ]);
                }
            }
            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function visaLetterPDFView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(10);

        $visa_kisi_tablosu = "<table border='1' cellpadding='2' cellspacing='0'><tr>" .
            "<td><b>Ad/Soyad</b></td>" .
            "<td><b>Pasaport Veren Makam</b></td>" .
            "<td><b>Pasaport No</b></td>" .
            "<td><b>Doğum Tarihi</b></td>" .
            "<td><b>Veriliş Tarihi</b></td>" .
            "<td><b>Sona Erme Tarihi</b></td>" .
            "</tr>";
        $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();
        foreach($katilimcilar as $katilimci) {
            $visa_kisi_tablosu .= "<tr>" .
                "<td>$katilimci->adi_soyadi</td>" .
                "<td>$katilimci->vf_duzenleyen_makam</td>" .
                "<td>$katilimci->vf_pasaport</td>" .
                "<td style='text-align: center;'>".($katilimci->vf_dogum_tarihi != '' ? date('d.m.Y', strtotime($katilimci->vf_dogum_tarihi)) : '')."</td>" .
                "<td style='text-align: center;'>".($katilimci->vf_verilis_tarihi != '' ? date('d.m.Y', strtotime($katilimci->vf_verilis_tarihi)) : '')."</td>" .
                "<td style='text-align: center;'>".($katilimci->vf_sonkullanma_tarihi != '' ? date('d.m.Y', strtotime($katilimci->vf_sonkullanma_tarihi)) : '')."</td>" .
                "</tr>";
        }
        $visa_kisi_tablosu .= "</table>";

        $data = [
            'hid_teklif_id' => $teklif_id,
            'hr_admin_txt' => trim($teklif->egitimKayit->kontakKisiUnvan['adi']." ".$teklif->egitimKayit['ct_adi_soyadi'])." < ".$teklif->egitimKayit->ct_sirket_email." >",
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => $sablon->alan1,
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{SIRKET_ULKE}',
                        '{SIRKET_ISMI}',
                        '{EGITIM_TARIHI}',
                        '{EGITIM_ADI}',
                        '{VISA_KISI_TABLOSU}',
                    ],
                    [
                        $teklif->egitimKayit->sirketUlke->adi,
                        $teklif->egitimKayit->sirketReferans->adi != '' ? $teklif->egitimKayit->sirketReferans->adi : $teklif->egitimKayit->sirket_adi,
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        $teklif->egitimKayit->egitimler->adi,
                        $visa_kisi_tablosu,
                    ],
                    $sablon->alan2
                ),
                'alt_kisim' => $sablon->alan3,
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];

        return view('teklifler.visa_letter_pdf_view', $data);
    }

    public function visaLetterPDFCreate(Request $request, $prefix, $teklif_id) {
        $sablon = EmailSablon::find(10);
        $data = [
            'icerik' => $request->icerik,
            'alt_kisim' => $request->alt_kisim,
            'imza_resim' => Storage::url($sablon->alan1),
        ];
        $path = "public/visa_letter/";
        $filename = "visa_letter_".$teklif_id.".pdf";


        $pdf = PDF::loadView('teklifler/pdf/visa_letter_pdf', $data)
            ->setPaper('a4', 'portraid')
        ;

        $pdf->save(storage_path().'/app/'.$path.$filename);

        Teklifler::find($teklif_id)->update([
            'vpm_pdf_dosyasi' => $path.$filename
        ]);

        return $pdf->stream($filename);
    }

    public function visaLetterPDFMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(21);
        $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

        $katilimci_adi_txt = "";
        foreach($katilimcilar as $row) {
            if($row->email == "")
                continue;
            $katilimci_adi_txt .= "<div>$row->adi_soyadi < $row->email ></div>";
        }

        $data = [
            'hid_teklif_id' => $teklif_id,
            'katilimci_adi_txt' => $katilimci_adi_txt,
            'vpm_pdf_dosyasi' => $teklif->vpm_pdf_dosyasi,
            'vpm_pdf_dosyasi_adi' => explode("/", $teklif->vpm_pdf_dosyasi),
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{EGITIM_TARIHI}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'cc_hradmin' => $teklif->egitimKayit->ct_sirket_email,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{VIZE_BILGI_TOPLAMA_LINK}',
                    ],
                    [
                        "<a href='http://www.saharatraining.com/?visa-invitation,".md5($teklif->id)."'>http://www.saharatraining.com/?visa-invitation,".md5($teklif->id)."</a>",
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.visa_davet_pdf_mail_view', $data);
    }

    public function visaLetterPDFMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $teklif = Teklifler::find($teklif_id);
            $ekler = Storage::URL($teklif->vpm_pdf_dosyasi);
            $ekler .= ",".Storage::URL('SHR TrainingConsultancy Signature_of_Authorization.pdf');
            $ekler .= ",".Storage::URL('SAHARA Group-ChamberOfCommerce-RegistrationDocument-EnglishTranslation.pdf');
            $ekler .= ",".Storage::URL('SHR-ITO-ActivityCertificate-March2019.pdf');
            $ekler .= ",".Storage::URL('SAHARA Group-TaxRecordDocument-Translation-Year2017.pdf');

            /*
            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);
            */

            $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

            $cc = "";
            if(isset($request->hradmin_gonder)) {
                $cc .= $request->cc_hradmin;
            }
            $cc .= ($cc != '' && $request->cc != '' ? ',' : '').$request->cc;
            $tmp_mail_arr = [];
            foreach($katilimcilar as $katilimci) {
                if(trim($katilimci->email) == "")
                    continue;
                if(in_array(trim($katilimci->email), $tmp_mail_arr))
                    continue;
                array_push($tmp_mail_arr, trim($katilimci->email));

                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($katilimci->email),
                    'cc' => $cc,
                    'bcc' => $request->bcc,
                    'icerik' => $request->icerik,
                    'ekler' => $ekler
                ]);
            }
            $teklif->update([
                'vpm_tarih' => date('Y-m-d H:i:s'),
                'vpm_gonderen' => Auth::user()->id,
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function airportTransferMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(6);
        $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

        $katilimci_adi_txt = "";
        foreach($katilimcilar as $row) {
            if($row->email == "")
                continue;
            $katilimci_adi_txt .= "<div>$row->adi_soyadi < $row->email ></div>";
        }

        $link = "http://www.saharatraining.com/?airportform,".md5($teklif->id);
        $data = [
            'hid_teklif_id' => $teklif_id,
            'katilimci_adi_txt' => $katilimci_adi_txt,
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => str_replace([
                    '{course_title}',
                    '{course_start_date}'
                ], [
                    $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                    date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi))
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'cc_hradmin' => $teklif->egitimKayit->ct_sirket_email,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{COURSE_TITLE}',
                        '{COURSE_START_DATE}',
                        '{COURSE_DURATION}',
                        '{KARSILAMA_FORM_LINKI}',
                    ],
                    [
                        $teklif->egitimKayit->egitimler->kodu." ".$teklif->egitimKayit->egitimler->adi,
                        date('d.m.Y', strtotime($teklif->egitimKayit->egitimTarihi->baslama_tarihi)),
                        $teklif->egitimKayit->egitimTarihi->egitim_suresi." ".$teklif->egitimKayit->egitimTarihi->egitimPart->adi,
                        "<a href='$link'>$link</a>",
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.airport_transfer_mail_view', $data);
    }

    public function airportTransferMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            $teklif = Teklifler::find($teklif_id);
            $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

            $cc = "";
            if(isset($request->hradmin_gonder)) {
                $cc .= $request->cc_hradmin;
            }
            $cc .= ($cc != '' && $request->cc != '' ? ',' : '').$request->cc;

            foreach($katilimcilar as $katilimci) {
                if(trim($katilimci->email) =="")
                    continue;
                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($katilimci->email),
                    'cc' => $cc,
                    'bcc' => $request->bcc,
                    'icerik' => $request->icerik,
                    'ekler' => $ekler
                ]);
            }
            $teklif->update([
                'apt_tarih' => date('Y-m-d H:i:s'),
                'apt_gonderen' => Auth::user()->id,
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function hotelReservationFormMailView($prefix, $teklif_id) {
        $teklif = Teklifler::find($teklif_id);
        $sablon = EmailSablon::find(30);
        $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

        $katilimci_adi_txt = "";
        foreach($katilimcilar as $row) {
            if($row->email == "")
                continue;
            $katilimci_adi_txt .= "<div>$row->adi_soyadi < $row->email ></div>";
        }

        $link = "http://www.saharatraining.com/?hotel-reservation,".md5($teklif->id);
        $data = [
            'hid_teklif_id' => $teklif_id,
            'katilimci_adi_txt' => $katilimci_adi_txt,
            'data' => [
                'from_email' => $sablon->alan4,
                'konu' => $sablon->alan1,
                'cc' => $sablon->alan6,
                'cc_hradmin' => $teklif->egitimKayit->ct_sirket_email,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{OTEL_REZERVASYON_LINK}',
                    ],
                    [
                        "<a href='$link'>$link</a>",
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX')
        ];
        return view('teklifler.hotel_reservation_mail_view', $data);
    }

    public function hotelReservationFormMailSend(Request $request, $prefix, $teklif_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            $teklif = Teklifler::find($teklif_id);
            $katilimcilar = Katilimcilar::where('egitim_kayit_id', $teklif->egitim_kayit_id)->get();

            $cc = "";
            if(isset($request->hradmin_gonder)) {
                $cc .= $request->cc_hradmin;
            }
            $cc .= ($cc != '' && $request->cc != '' ? ',' : '').$request->cc;

            foreach($katilimcilar as $katilimci) {
                if(trim($katilimci->email) =="")
                    continue;
                SendEmail::create([
                    'oncelik' => 5,
                    'konu' => $request->konu,
                    'from_email' => $request->from_email,
                    'to_email' => trim($katilimci->email),
                    'cc' => $cc,
                    'bcc' => $request->bcc,
                    'icerik' => $request->icerik,
                    'ekler' => $ekler
                ]);
            }
            $teklif->update([
                'orm_tarih' => date('Y-m-d H:i:s'),
                'orm_gonderen' => Auth::user()->id,
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function ccs_view(Request $request) {
        if(!Auth::user()->isAllow('ccs_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        //return $request->filtre_yil;

        $request->session()->forget([
            'CCS_FILTRE_YIL',
            'CCS_FILTRE_ULKE_ID',
            'CCS_FILTRE_SIRKET_ID'
        ]);

        if((int) $request->filtre_yil > 0) {
            session(['CCS_FILTRE_YIL' => $request->filtre_yil]);
        }
        if( (int) $request->filtre_ulke_id > 0) {
            session(['CCS_FILTRE_ULKE_ID' => $request->filtre_ulke_id]);
        }
        if((int) $request->filtre_sirket_id > 0) {
            $request->session()->put('CCS_FILTRE_SIRKET_ID', $request->filtre_sirket_id);
        }

        $filtre_yil_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->selectraw('year(egitim_tarihleri.baslama_tarihi) as yil')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('yil')
            ->havingraw('yil is not null')
            ->orderby('yil', 'desc');

        $filtre_ulke_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->wherenotnull('ulkeler.id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc');

        $filtre_ref_sirket_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('referanslar', 'referanslar.id', '=', 'egitim_kayitlar.referans_id')
            ->wherenotnull('egitim_kayitlar.referans_id')
            ->select('referanslar.id', 'referanslar.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('referanslar.id')
            ->orderby('referanslar.adi', 'asc');

        $query = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'teklifler.id', '=', 'egitim_kayitlar.ref_teklif_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->orderby('ulkeler.adi', 'asc')
            ->select('ulkeler.adi')
            ->selectraw('count(teklifler.id) as teklif_sayi')
            ->selectraw('sum((select count(katilimcilar.id) from katilimcilar where katilimcilar.egitim_kayit_id = teklifler.egitim_kayit_id and katilimcilar.deleted_at is null)) as katilimci_sayisi')
            ->groupby('ulkeler.id');

        if((int) session('CCS_FILTRE_YIL') > 0) {
            $query->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('CCS_FILTRE_YIL'));
            $filtre_ulke_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('CCS_FILTRE_YIL'));
            $filtre_ref_sirket_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('CCS_FILTRE_YIL'));
        }

        if((int) session('CCS_FILTRE_ULKE_ID')) {
            $query->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('CCS_FILTRE_ULKE_ID'));
            $filtre_ref_sirket_liste->whereraw('egitim_kayitlar.sirket_ulke_id = '.session('CCS_FILTRE_ULKE_ID'));
        }

        if((int) session('CCS_FILTRE_SIRKET_ID')) {
            $query->where('egitim_kayitlar.referans_id', '=', session('CCS_FILTRE_SIRKET_ID'));
        }
        //echo $query->toSql(); return;
        $data = [
            'prefix' => 'ccs',
            'alt_baslik' => '',
            'liste' => $query->get(),
            'filtre_yil_liste' => $filtre_yil_liste->get(),
            'filtre_yil' => session('CCS_FILTRE_YIL') ?? null,
            'filtre_ulke_liste' => $filtre_ulke_liste->get(),
            'filtre_ulke_id' => session('CCS_FILTRE_ULKE_ID') ?? null,
            'filtre_ref_sirket_liste' => $filtre_ref_sirket_liste->get(),
            'filtre_ref_sirket_id' => session('CCS_FILTRE_SIRKET_ID') ?? null,
        ];
        return view('teklifler.cc_istatistik', $data);
    }

    public function ccsFiltreUlkeGetirJSON(Request $request) {
        $ulke_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitim_kayitlar.sirket_ulke_id')
            ->wherenotnull('ulkeler.id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc');

        if(!empty($request->filtre_yil)) {
            $ulke_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = ?', $request->filtre_yil);
        }

        return response()->json($ulke_liste->get());
    }

    public function ccsFiltreSirketGetirJSON(Request $request) {
        $filtre_ref_sirket_liste = Teklifler::where('teklifler.durum', 2)
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.ref_teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->leftjoin('referanslar', 'referanslar.id', '=', 'egitim_kayitlar.referans_id')
            ->wherenotnull('egitim_kayitlar.referans_id')
            ->select('referanslar.id', 'referanslar.adi')
            ->selectraw('count(teklifler.id) as sayi')
            ->groupby('referanslar.id')
            ->orderby('referanslar.adi', 'asc');

        if(!empty($request->filtre_yil)) {
            $filtre_ref_sirket_liste->whereraw('year(egitim_tarihleri.baslama_tarihi) = ?', $request->filtre_yil);
        }
        if(!empty($request->filtre_ulke_id)) {
            $filtre_ref_sirket_liste->where('egitim_kayitlar.sirket_ulke_id', $request->filtre_ulke_id);
        }

        return response()->json($filtre_ref_sirket_liste->get());
    }

}
