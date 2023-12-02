<?php

namespace App\Http\Controllers;

use App\Http\Models\GelirKalemleri;
use App\Http\Models\Kasa;
use Illuminate\Http\Request;
use Validator;
use Auth;

class KasaReceiveController extends HomeController
{
    private $prefix = "aca_received";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('ama_receive_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Kasa::wherenull('deleted_at')
            ->wherenotnull('gelir_kalem_id')
            ->orderby('tarih', 'desc')
            ->orderby('id', 'desc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Received"
        ];
        return view('admin_operation.hesap_modul.kasagelir_view', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('ama_receive_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Received",
            'data' => new Kasa(),
            'gelir_turleri' => GelirKalemleri::orderby('sira')->get()
        ];
        return view('admin_operation.hesap_modul.kasagelir_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('ama_receive_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'tarih.required' => 'Date is required.',
                'tarih.date_format' => 'Date format must be day.month.Year.',
                'gelir_kalem_id.required' => 'Received type is required.',
                'gelir_tl.required' => "Amount is required",
                'gelir_tl.numeric' => "Amount must be number",
            );
            $rules = array(
                'tarih' => 'required|date_format:d.m.Y',
                'gelir_kalem_id' => 'required',
                'gelir_tl' => 'required|numeric',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Kasa::create([
                'tarih' => date("Y-m-d", strtotime($request->input('tarih'))),
                'islem_yapan' => session('KULLANICI_ID'),
                'gelir_kalem_id' => $request->gelir_kalem_id,
                'gelir_tl' => $request->gelir_tl,
                'kayit_ip' => $request->ip(),
                'aciklama' => $request->aciklama
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
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function show(Kasa $kasa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function edit(Kasa $kasa, $id)
    {
        if(!Auth::user()->isAllow('ama_receive_edit')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Received",
            'data' => $kasa->findorfail($id),
            'gelir_turleri' => GelirKalemleri::orderby('sira')->get()
        ];
        return view('admin_operation.hesap_modul.kasagelir_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kasa $kasa, $id)
    {
        if(!Auth::user()->isAllow('ama_receive_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'tarih.required' => 'Date is required.',
                'tarih.date_format' => 'Date format must be day.month.Year.',
                'gelir_kalem_id.required' => 'Received type is required.',
                'gelir_tl.required' => "Amount is required",
                'gelir_tl.numeric' => "Amount must be number",
            );
            $rules = array(
                'tarih' => 'required|date_format:d.m.Y',
                'gelir_kalem_id' => 'required',
                'gelir_tl' => 'required|numeric',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $kasa->findorfail($id)->update([
                'tarih' => date("Y-m-d", strtotime($request->input('tarih'))),
                'islem_yapan' => session('KULLANICI_ID'),
                'gelir_kalem_id' => $request->gelir_kalem_id,
                'gelir_tl' => $request->gelir_tl,
                'kayit_ip' => $request->ip(),
                'aciklama' => $request->aciklama
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
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kasa $kasa, $id)
    {
        if(!Auth::user()->isAllow('ama_receive_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $kasa->destroy($id);
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
