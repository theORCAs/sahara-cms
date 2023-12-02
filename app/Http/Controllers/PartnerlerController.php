<?php

namespace App\Http\Controllers;

use App\Http\Models\PartnerKategorileri;
use App\Http\Models\Partnerler;
use App\Http\Models\Ulkeler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class PartnerlerController extends HomeController
{
    private $prefix = "spm_customerdetail";
    private $kategori_id;
    private $error_messages = array(
        'kategori_id.required' => 'Payment Category is required.',
        'adi.required' => 'Company/Person Name is required.',
        'servis.required' => 'Service Provided is required.',
        'ulke_id.required' => 'Country is required.',
        'ilgili_kisi.required' => 'Contact Person Name is required.',
        'ilgili_email.required' => 'Contact Person email is required.',
        'ilgili_email.email' => 'Contact Person email must be valid.',
    );
    private $rules = array(
        'kategori_id' => 'required',
        'adi' => 'required',
        'servis' => 'required',
        'ulke_id' => 'required',
        'ilgili_kisi' => 'required',
        'ilgili_email' => 'required|email',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('spm_cpd_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $kategori_listesi = PartnerKategorileri::orderby('adi', 'asc')
            ->get();

        if($this->kategori_id > 0) {
            $liste = Partnerler::where('kategori_id', $this->kategori_id)
                ->orderby('adi', 'asc')
                ->paginate(20);
        } else {
            $liste = Partnerler::wherenull('deleted_at')
                ->whereHas('kategori', function ($query) {
                    return $query->orderby('adi', 'asc');
                })
                ->orderby('adi', 'asc')
                ->paginate(20);
        }
        $data = [
            'kategori_id' => $this->kategori_id,
            'kategori_listesi' => $kategori_listesi,
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Customer/Person Details'
        ];
        return view('office_management.payment_module.partnerler_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('spm_cpd_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Customer/Person",
            'data' => new Partnerler(),
            'kategori_listesi' => PartnerKategorileri::orderby('sira', 'asc', 'adi', 'asc')->get(),
            'ulke_listesi' => Ulkeler::where('flg_aktif', '1')->orderby('adi', 'asc')->get()
        ];
        return view('office_management.payment_module.partnerler_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('spm_cpd_add')) {
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

            Partnerler::create([
                'kategori_id' => $request->kategori_id,
                'adi' => $request->adi,
                'servis' => $request->servis,
                'website' => $request->website,
                'sehir_adi' => $request->sehir_adi,
                'ulke_id' => $request->ulke_id,
                'ilgili_kisi' => $request->ilgili_kisi,
                'ilgili_email' => $request->ilgili_email,
                'ilgili_email1' => $request->ilgili_email1,
                'ilgili_cep' => $request->ilgili_cep,
                'ilgili_cep1' => $request->ilgili_cep1,
                'banka_adi' => $request->banka_adi,
                'banka_sube' => $request->banka_sube,
                'hesap_sahibi' => $request->hesap_sahibi,
                'hesap_no' => $request->hesap_no,
                'iban' => $request->iban,
            ]);
            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Partnerler  $partnerler
     * @return \Illuminate\Http\Response
     */
    public function show(Partnerler $partnerler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Partnerler  $partnerler
     * @return \Illuminate\Http\Response
     */
    public function edit(Partnerler $partnerler, $id)
    {
        if(!Auth::user()->isAllow('spm_cpd_edit')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Spent",
            'data' => $partnerler->findorfail($id),
            'kategori_listesi' => PartnerKategorileri::orderby('sira', 'asc', 'adi', 'asc')->get(),
            'ulke_listesi' => Ulkeler::where('flg_aktif', '1')->orderby('adi', 'asc')->get()
        ];
        return view('office_management.payment_module.partnerler_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Partnerler  $partnerler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Partnerler $partnerler, $id)
    {
        if(!Auth::user()->isAllow('spm_cpd_edit')) {
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

            $partnerler::find($id)->update([
                'kategori_id' => $request->kategori_id,
                'adi' => $request->adi,
                'servis' => $request->servis,
                'website' => $request->website,
                'sehir_adi' => $request->sehir_adi,
                'ulke_id' => $request->ulke_id,
                'ilgili_kisi' => $request->ilgili_kisi,
                'ilgili_email' => $request->ilgili_email,
                'ilgili_email1' => $request->ilgili_email1,
                'ilgili_cep' => $request->ilgili_cep,
                'ilgili_cep1' => $request->ilgili_cep1,
                'banka_adi' => $request->banka_adi,
                'banka_sube' => $request->banka_sube,
                'hesap_sahibi' => $request->hesap_sahibi,
                'hesap_no' => $request->hesap_no,
                'iban' => $request->iban,
            ]);
            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Partnerler  $partnerler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Partnerler $partnerler, $id)
    {
        if(!Auth::user()->isAllow('spm_cpd_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $partnerler->destroy($id);
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

    public function search(Request $request) {
        $this->kategori_id = $request->kategori_id;

        return $this->index();
    }
}
