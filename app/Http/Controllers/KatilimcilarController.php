<?php

namespace App\Http\Controllers;

use App\Http\Models\Katilimcilar;
use App\Http\Models\Unvanlar;
use Illuminate\Http\Request;
use Auth;
use Validator;

class KatilimcilarController extends HomeController
{
    private $prefix = "participant";

    private $rules = [];

    private $error_messages = [];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($crf_id=null)
    {
        $data = [
            'crf_id' => $crf_id,
            'alt_baslik' => 'Add Remove ',
            'prefix' => $this->prefix,
            'unvanlar' => Unvanlar::wherenull("deleted_at")->orderby("sira")->get(),
            'liste' => Katilimcilar::where('egitim_kayit_id', $crf_id)->get(),
        ];
        return view('egitim_kayitlar.proposal_module.katilimcilar', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $crf_id)
    {
        if(!Auth::user()->isAllow('upd_participant')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            Katilimcilar::create([
                'egitim_kayit_id' => $crf_id,
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Katilimcilar  $katilimcilar
     * @return \Illuminate\Http\Response
     */
    public function show(Katilimcilar $katilimcilar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Katilimcilar  $katilimcilar
     * @return \Illuminate\Http\Response
     */
    public function edit(Katilimcilar $katilimcilar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Katilimcilar  $katilimcilar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Katilimcilar $katilimcilar, $crf_id)
    {
        if(!Auth::user()->isAllow('upd_participant')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            foreach($request->hid_katilimci_id as $key => $katilimci_id) {
                $this->rules = array_merge($this->rules, [
                    'unvan_id.'.$key => 'required',
                    'adi_soyadi.'.$key => 'required',
                    'email.'.$key => 'required|email',
                ]);

                $this->error_messages = array_merge($this->error_messages, [
                    "unvan_id.$key.required" => ($key + 1)." participant salutation is required",
                    "adi_soyadi.$key.required" => ($key + 1)." participant name is required",
                    "email.$key.required" => ($key + 1)." participant email is required",
                    "email.$key.email" => ($key + 1)." participant email is not valid",
                ]);
            }

            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            foreach($request->hid_katilimci_id as $key => $katilimci_id) {
                if(!empty($katilimci_id)) {
                    $katilimcilar->find($katilimci_id)->update([
                        "unvan_id" => $request->unvan_id[$key],
                        "adi_soyadi" => $request->adi_soyadi[$key],
                        //"yasadigi_ulke_id" => $request->k_yasadigi_ulke_id[$key],
                        "is_pozisyonu" => $request->is_pozisyonu[$key] ?? null,
                        "email" => $request->email[$key],
                        "email2" => $request->email2[$key] ?? null,
                        "cep_tel_kodu" => $request->cep_tel_kodu[$key] ?? null,
                        "cep_tel" => $request->cep_tel[$key] ?? null,
                        "cep_tel2_kodu" => $request->cep_tel2_kodu[$key] ?? null,
                        "cep_tel2" => $request->cep_tel2[$key] ?? null,
                    ]);
                } else {
                    $katilimcilar->create([
                        "egitim_kayit_id" => $crf_id,
                        "unvan_id" => $request->unvan_id[$key],
                        "adi_soyadi" => $request->adi_soyadi[$key],
                        //"yasadigi_ulke_id" => $request->k_yasadigi_ulke_id[$key],
                        "is_pozisyonu" => $request->is_pozisyonu[$key] ?? null,
                        "email" => $request->email[$key],
                        "email2" => $request->email2[$key] ?? null,
                        "cep_tel_kodu" => $request->cep_tel_kodu[$key] ?? null,
                        "cep_tel" => $request->cep_tel[$key] ?? null,
                        "cep_tel2_kodu" => $request->cep_tel2_kodu[$key] ?? null,
                        "cep_tel2" => $request->cep_tel2[$key] ?? null,
                    ]);
                }
            }

            return redirect()
                ->back()
                ->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Katilimcilar  $katilimcilar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Katilimcilar $katilimcilar, $id)
    {
        if(!Auth::user()->isAllow('clm_list_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $katilimcilar->destroy($id);
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
}
