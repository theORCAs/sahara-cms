<?php

namespace App\Http\Controllers;

use App\Http\Models\OdemeBeklemeTurleri;
use App\Http\Models\Odemeler;
use App\Http\Models\PartnerKategorileri;
use App\Http\Models\Partnerler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class OdemelerController extends HomeController
{
    private $liste;
    private $prefix;
    private $alt_baslik;
    private $error_messages = array(
        'kategori_id.required' => 'Payment Category is required.',
        'partner_id.required' => 'Company/Person Name is required.',
        'dekont_tarihi.required' => 'Document Date is required.',
        'dekont_tarihi.date_format' => 'Document Date format must be day.month.year.',
        'tutar.required' => 'Amount is required.',
        'tutar.numeric' => 'Amount must be a number.',
        'banka.required' => 'Bank name is required.',
        'iban.required' => 'IBAN is required.',
        'odeme_tarihi.required_if' => 'Payment date is required.',
        'odeme_tarihi.date_format' => 'Payment date format must be day.month.year.',
    );
    private $rules = array(
        'kategori_id' => 'required',
        'partner_id' => 'required',
        'dekont_tarihi' => 'required|date_format:d.m.Y',
        'tutar' => 'required|numeric',
        'banka' => 'required',
        'iban' => 'required',
        'odeme_tarihi' => 'required_if:durum,1|date_format:d.m.Y',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'liste' => $this->liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => $this->alt_baslik
        ];
        return view('office_management.payment_module.odemeler_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('spm_wait_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Add New Payment",
            'data' => new Odemeler(),
            'odeme_kategorileri' => PartnerKategorileri::orderby('sira', 'asc', 'adi', 'asc')->get(),
            'odeme_bekleme_turleri' => OdemeBeklemeTurleri::orderby('adi', 'asc')->get()
        ];
        return view('office_management.payment_module.odemeler_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('spm_wait_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Odemeler::create([
                'kategori_id' => $request->input('kategori_id'),
                'partner_id' => $request->partner_id,
                'dekont_tarihi' => $request->dekont_tarihi != "" ? date('Y-m-d', strtotime($request->dekont_tarihi)) : null,
                'tutar' => $request->tutar,
                'banka' => $request->banka,
                'iban' => $request->iban,
                'aciklama' => $request->aciklama,
                'durum' => $request->durum,
                'odeme_tarihi' => $request->odeme_tarihi != "" ? date('Y-m-d', strtotime($request->odeme_tarihi)) : null,
                'dekont_durum_id' => $request->dekont_durum_id,
                'dekont' => $request->dekont,
            ]);
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\Odemeler  $odemeler
     * @return \Illuminate\Http\Response
     */
    public function show(Odemeler $odemeler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Odemeler  $odemeler
     * @return \Illuminate\Http\Response
     */
    public function edit(Odemeler $odemeler, $id)
    {
        if(!Auth::user()->isAllow('spm_wait_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Update Payment",
            'data' => $odemeler->findorfail($id),
            'odeme_kategorileri' => PartnerKategorileri::orderby('sira', 'asc', 'adi', 'asc')->get(),
            'odeme_bekleme_turleri' => OdemeBeklemeTurleri::orderby('adi', 'asc')->get()
        ];
        return view('office_management.payment_module.odemeler_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Odemeler  $odemeler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Odemeler $odemeler, $id)
    {
        if(!Auth::user()->isAllow('spm_wait_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $odemeler::findorfail($id)->update([
                'kategori_id' => $request->input('kategori_id'),
                'partner_id' => $request->partner_id,
                'dekont_tarihi' => $request->dekont_tarihi != "" ? date('Y-m-d', strtotime($request->dekont_tarihi)) : null,
                'tutar' => $request->tutar,
                'banka' => $request->banka,
                'iban' => $request->iban,
                'aciklama' => $request->aciklama,
                'durum' => $request->durum,
                'odeme_tarihi' => $request->odeme_tarihi != "" ? date('Y-m-d', strtotime($request->odeme_tarihi)) : null,
                'dekont_durum_id' => $request->dekont_durum_id,
                'dekont' => $request->dekont,
            ]);
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Odemeler  $odemeler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Odemeler $odemeler, $id)
    {
        if(!Auth::user()->isAllow('spm_wait_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $odemeler->destroy($id);
            return redirect()
                ->back()
                ->with('msj', config('messages.islem_basarili'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function bekleyen() {
        if(!Auth::user()->isAllow('spm_wait_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Odemeler::wherenull('deleted_at')
            ->where('durum', '0')
            ->orderby('created_at', 'asc')
            ->paginate(10);

        $this->liste = $liste;
        session(['PREFIX' => 'spm_watingpayment']);
        $this->alt_baslik = "Waiting Payments";

        return $this->index();
    }

    public function odenmisler() {
        if(!Auth::user()->isAllow('spm_completed_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Odemeler::wherenull('deleted_at')
            ->where('durum', '1')
            ->orderby('odeme_tarihi', 'desc')
            ->paginate(10);

        $this->liste = $liste;
        session(['PREFIX' => 'spm_completedpayment']);
        $this->alt_baslik = "Completed Payments";

        return $this->index();
    }

    public function partnerGetirJson(Request $request) {
        $result = Partnerler::where('kategori_id', $request->kategori_id)->orderby('adi', 'asc')->get();

        return response()->json($result);
    }
}
