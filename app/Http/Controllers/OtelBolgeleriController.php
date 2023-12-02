<?php

namespace App\Http\Controllers;

use App\Http\Models\OtelSehirleri;
use App\Http\Models\OtelBolgeleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class OtelBolgeleriController extends HomeController
{
    private $prefix = "hrm_region";
    private $error_messages = array(
        'sehir_id' => 'Region city is required.',
        'adi.required' => 'Region name is required.',
        'sira.required' => 'Order number is required.',
        'sira.numeric' => 'The order number must be a number.'
    );
    private $rules = array(
        'sehir_id' => 'required',
        'adi' => 'required',
        'sira' => 'required|numeric'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('hrm_rs_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = OtelBolgeleri::leftjoin('otl_sehir', 'otl_bolge.sehir_id', 'otl_sehir.id')
            ->select("otl_bolge.*")
            ->wherenull('otl_bolge.deleted_at')
            ->orderBy('otl_sehir.adi', 'asc')
            ->orderBy('otl_bolge.adi', 'asc')
            ->get();

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Define hotel region"
        ];


        return view("hotel_registration.bolge_view", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('hrm_rs_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Region",
            'data' => new OtelBolgeleri(),
            'sehirler' => OtelSehirleri::wherenull('deleted_at')->orderBy('adi', 'asc')->get()
        ];

        return view('hotel_registration.bolge_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('hrm_rs_add')) {
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

            OtelBolgeleri::create([
                'sehir_id' => $request->input('sehir_id'),
                'adi' => $request->input('adi'),
                'sira' => $request->input('sira')
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
     * @param  \App\Http\Models\OtelBolgeleri  $otelBolgeleri
     * @return \Illuminate\Http\Response
     */
    public function show(OtelBolgeleri $otelBolgeleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\OtelBolgeleri  $otelBolgeleri
     * @return \Illuminate\Http\Response
     */
    public function edit(OtelBolgeleri $otelBolgeleri, $id)
    {
        if(!Auth::user()->isAllow('hrm_rs_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Hotel Region",
            'data' => $otelBolgeleri->findorfail($id),
            'sehirler' => OtelSehirleri::wherenull('deleted_at')->orderBy('adi', 'asc')->get()
        ];

        return view('hotel_registration.bolge_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\OtelBolgeleri  $otelBolgeleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtelBolgeleri $otelBolgeleri, $id)
    {
        if(!Auth::user()->isAllow('hrm_rs_edit')) {
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

            $otelBolgeleri->find($id)
                ->update([
                    'sehir_id' => $request->input('sehir_id'),
                    'adi' => $request->input('adi'),
                    'sira' => $request->input('sira')
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
     * @param  \App\Http\Models\OtelBolgeleri  $otelBolgeleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtelBolgeleri $otelBolgeleri, $id)
    {
        if(!Auth::user()->isAllow('hrm_rs_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $otelBolgeleri->destroy($id);
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
