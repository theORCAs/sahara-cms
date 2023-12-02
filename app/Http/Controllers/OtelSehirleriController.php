<?php

namespace App\Http\Controllers;

use App\Http\Models\OtelSehirleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class OtelSehirleriController extends HomeController
{
    private $prefix = "hrm_hotelcity";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('hrm_city_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = OtelSehirleri::wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->get();

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "List of Hotel Cities"
        ];
        return view("hotel_registration.sehir_view", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('hrm_city_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Hotel Cities",
            'data' => new OtelSehirleri()
        ];

        return view('hotel_registration.sehir_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('hrm_city_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'City Name is required.',
                'plaka.required' => 'City Code is required.',
                'plaka.numeric' => 'The city code must be a number.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'plaka' => 'required|numeric',
                'sira' => 'required|numeric'
                //'email'      => 'required|email',
                //'nerd_level' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator)
                    ;
            }

            OtelSehirleri::create([
                'adi' => $request->input('adi'),
                'plaka' => $request->input('plakas'),
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
     * @param  \App\Http\Models\OtelSehirleri  $otelSehirleri
     * @return \Illuminate\Http\Response
     */
    public function show(OtelSehirleri $otelSehirleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\OtelSehirleri  $otelSehirleri
     * @return \Illuminate\Http\Response
     */
    public function edit(OtelSehirleri $otelSehirleri, $id)
    {
        if(!Auth::user()->isAllow('hrm_city_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Sector",
            'data' => $otelSehirleri->findorfail($id)
        ];
        return view('hotel_registration.sehir_edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\OtelSehirleri  $otelSehirleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtelSehirleri $otelSehirleri, $idsi)
    {
        if(!Auth::user()->isAllow('hrm_city_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'City Name is required.',
                'plaka.required' => 'City Code is required.',
                'plaka.numeric' => 'The city code must be a number.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'plaka' => 'required|numeric',
                'sira' => 'required|numeric'
                //'email'      => 'required|email',
                //'nerd_level' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $otelSehirleri->find($idsi)->update([
                'adi' => $request->input('adi'),
                'plaka' => $request->input('plaka'),
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
     * @param  \App\Http\Models\OtelSehirleri  $otelSehirleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtelSehirleri $otelSehirleri, $id)
    {
        if(!Auth::user()->isAllow('hrm_city_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $otelSehirleri->destroy($id);
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
