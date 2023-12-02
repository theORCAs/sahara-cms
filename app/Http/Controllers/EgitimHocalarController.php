<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimHocalar;
use Illuminate\Http\Request;
use Auth;
use Validator;

class EgitimHocalarController extends HomeController
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
        try {
            $rules = [
                'ders_tarihi' => 'required',
                'hoca_id' => 'required',
                'ucret' => 'required|numeric',
            ];
            $error_messages = [
                'ders_tarihi.required' => 'Date Delivery is required.',
                'hoca_id.required' => 'Instructor is required.',
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

            EgitimHocalar::create([
                'teklif_id' => $request->teklif_id,
                'egitim_kayit_id' => $request->egitim_kayit_id,
                'hoca_id' => $request->hoca_id,
                'hoca_kisa_adi' => $request->hoca_kisa_adi,
                'ders_tarihi' => date('Y-m-d', strtotime($request->ders_tarihi)),
                'baslama_saati' => $request->baslama_saati,
                'bitis_saati' => $request->bitis_saati,
                'kisa_tanim' => $request->kisa_tanim,
                'ucret' => floatval($request->ucret)
            ]);

            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\EgitimHocalar  $egitimHocalar
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimHocalar $egitimHocalar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitimHocalar  $egitimHocalar
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitimHocalar $egitimHocalar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitimHocalar  $egitimHocalar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'ders_tarihi' => 'required',
                'hoca_id' => 'required',
                'ucret' => 'required|numeric',
            ];
            $error_messages = [
                'ders_tarihi.required' => 'Date Delivery is required.',
                'hoca_id.required' => 'Instructor is required.',
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

            EgitimHocalar::find($id)
                ->update([
                    'hoca_id' => $request->hoca_id,
                    'hoca_kisa_adi' => $request->hoca_kisa_adi,
                    'ders_tarihi' => date('Y-m-d', strtotime($request->ders_tarihi)),
                    'baslama_saati' => $request->baslama_saati,
                    'bitis_saati' => $request->bitis_saati,
                    'kisa_tanim' => $request->kisa_tanim,
                    'ucret' => floatval($request->ucret)
                ]);

            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\EgitimHocalar  $egitimHocalar
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if(!Auth::user()->isAllow('cca_egitimhoca_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            EgitimHocalar::destroy($id);
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


    public function egitmen_odemeler_listesi() {
        if(!Auth::user()->isAllow('iap_payment_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $result = EgitimHocalar::wherenull('deleted_at')
            ->where('ucret', '>', 0)
            ->orderby('id', 'desc')
            ->paginate(100);

        $data = [
            'liste' => $result,
            'prefix' => 'ia_payment'
        ];
        return view('egitmen.odemeler_view', $data);
    }

    public function egitmenOdemeYap($id) {
        if(!Auth::user()->isAllow('iap_payment_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        try {
            EgitimHocalar::find($id)->update([
                'odeme_yapilma_tarih' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }
    public function egitmenOdemeSil($id) {
        if(!Auth::user()->isAllow('iap_payment_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        try {
            EgitimHocalar::find($id)->update([
                'odeme_yapilma_tarih' => null
            ]);

            return redirect()->back()->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }
}
