<?php

namespace App\Http\Controllers;

use App\Http\Models\EbultenGruplar;
use App\Http\Models\EbultenKayitlar;
use App\Http\Models\Referanslar;
use App\Http\Models\Ulkeler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EbultenKayitlarController extends HomeController
{
    private $prefix = "em_grouplist";
    private $error_messages = array(
        'grup_id.required' => 'Email Group is required.',
        'adi_soyadi.required' => 'Name is required.',
        'ulke_id.required' => 'Country is required.',
        'referans_sirket_id.required' => 'Company is required.',
        'sirket_adi.required_if' => 'Company name is required.',
        'email.required' => 'Private Email is required.',
        'email.email' => 'Email must be valid.',
        'email2.email' => 'Corparate email must be valid.',
    );
    private $rules = array(
        'grup_id' => 'required',
        'adi_soyadi' => 'required',
        'ulke_id' => 'required',
        'referans_sirket_id' => 'required',
        'sirket_adi' => 'required_if:referans_sirket_id,-1',
        'email' => 'required|email',
        'email2' => 'sometimes|nullable|email',
    );
    private $liste;
    private $grup_id;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('em_gl_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $grup_listesi = EbultenGruplar::wherenull('deleted_at')
            ->orderbyraw('if(dinamik_grup_id is null, 0, 1)', 'asc')
            ->orderby('sira', 'asc')
            ->get();

        if($this->grup_id > 0) {
            $this->liste = EbultenKayitlar::wherenull('deleted_at')
                ->where('grup_id', $this->grup_id)
                ->orderby('adi_soyadi', 'asc')
                ->paginate(100);
        } else {
            $this->liste = EbultenKayitlar::wherenull('deleted_at')
                ->whereHas('grup', function ($query) {
                    $query->orderby('adi', 'asc');
                })
                ->orderby('adi_soyadi', 'asc')
                ->paginate(100);
        }

        $data = [
            'grup_id' => $this->grup_id,
            'grup_listesi' => $grup_listesi,
            'liste' => $this->liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Group Lists'
        ];
        return view('ebulten.bulten_kayitlar_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('em_gl_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Grup Email Record",
            'data' => new EbultenKayitlar(),
            'gruplar' => EbultenGruplar::wherenull('dinamik_grup_id')->orderby('sira', 'asc')->get(),
            'ulkeler' => Ulkeler::where('flg_aktif', '1')->orderby('adi', 'asc')->get()
        ];
        return view('ebulten.bulten_kayitlar_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('em_gl_add')) {
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

            EbultenKayitlar::create([
                'grup_id' => $request->input('grup_id'),
                'adi_soyadi' => $request->input('adi_soyadi'),
                'departman' => $request->departman,
                'sirket_adi' => intval($request->referans_sirket_id) < 0 ? $request->sirket_adi : null,
                'referans_sirket_id' => intval($request->referans_sirket_id) > 0 ? $request->referans_sirket_id : null,
                'sehir' => $request->sehir,
                'ulke_id' => intval($request->ulke_id) > 0 ? $request->ulke_id : null,
                'telefon' => $request->telefon,
                'cep' => $request->cep,
                'email' => $request->email,
                'email2' => $request->email2,
                'notlar' => $request->notlar,
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
     * @param  \App\Http\Models\EbultenKayitlar  $ebultenKayitlar
     * @return \Illuminate\Http\Response
     */
    public function show(EbultenKayitlar $ebultenKayitlar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EbultenKayitlar  $ebultenKayitlar
     * @return \Illuminate\Http\Response
     */
    public function edit(EbultenKayitlar $ebultenKayitlar, $id)
    {
        if(!Auth::user()->isAllow('em_gl_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Email Record",
            'data' => $ebultenKayitlar->findorfail($id),
            'gruplar' => EbultenGruplar::wherenull('dinamik_grup_id')->orderby('sira', 'asc')->get(),
            'ulkeler' => Ulkeler::where('flg_aktif', '1')->orderby('adi', 'asc')->get()
        ];
        return view('ebulten.bulten_kayitlar_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EbultenKayitlar  $ebultenKayitlar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbultenKayitlar $ebultenKayitlar, $id)
    {
        if(!Auth::user()->isAllow('em_gl_edit')) {
            return redirect()
                ->back()
                ->withInput()
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

            $ebultenKayitlar->find($id)
                ->update([
                    'grup_id' => $request->input('grup_id'),
                    'adi_soyadi' => $request->input('adi_soyadi'),
                    'departman' => $request->departman,
                    'sirket_adi' => intval($request->referans_sirket_id) < 0 ? $request->sirket_adi : null,
                    'referans_sirket_id' => intval($request->referans_sirket_id) > 0 ? $request->referans_sirket_id : null,
                    'sehir' => $request->sehir,
                    'ulke_id' => intval($request->ulke_id) > 0 ? $request->ulke_id : null,
                    'telefon' => $request->telefon,
                    'cep' => $request->cep,
                    'email' => $request->email,
                    'email2' => $request->email2,
                    'notlar' => $request->notlar,
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
     * @param  \App\Http\Models\EbultenKayitlar  $ebultenKayitlar
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbultenKayitlar $ebultenKayitlar, $id)
    {
        if(!Auth::user()->isAllow('em_gl_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $ebultenKayitlar->destroy($id);
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

    public function search($grup_id=null) {

        $this->grup_id = $grup_id;

        return $this->index();
    }

    public function refSirketListeJson(Request $request) {
        $liste = Referanslar::wherenull('deleted_at')
            ->where('ulke_id', $request->ulke_id)
            ->orderby('flg_notinlist', 'asc')
            ->orderby('adi', 'asc')
            ->select('id', 'adi', 'flg_notinlist')
            ->get();

        return response()->json($liste);
    }
}
