<?php

namespace App\Http\Controllers;

use App\Http\Models\OtelManzaralari;
use Illuminate\Http\Request;
use Validator;
use Auth;

class OtelManzaralariController extends HomeController
{
    private $prefix = "hrm_viewoption";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('hrm_vo_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = OtelManzaralari::orderby('sira', 'asc')
            ->get();
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Define hotel view options"
        ];
        return view("hotel_registration.manzara_view", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('hrm_vo_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Hotel View Option",
            'data' => new OtelManzaralari()
        ];

        return view('hotel_registration.manzara_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('hrm_vo_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'View option name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
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

            OtelManzaralari::create([
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
     * @param  \App\Http\Models\OtelManzaralari  $otelManzaralari
     * @return \Illuminate\Http\Response
     */
    public function show(OtelManzaralari $otelManzaralari)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\OtelManzaralari  $otelManzaralari
     * @return \Illuminate\Http\Response
     */
    public function edit(OtelManzaralari $otelManzaralari, $id)
    {
        if(!Auth::user()->isAllow('hrm_vo_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit View Options",
            'data' => $otelManzaralari->findorfail($id)
        ];

        return view('hotel_registration.manzara_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\OtelManzaralari  $otelManzaralari
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtelManzaralari $otelManzaralari, $idsi)
    {
        if(!Auth::user()->isAllow('hrm_vo_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'View option name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator)
                    ;
            }

            $otelManzaralari->find($idsi)->update([
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
     * @param  \App\Http\Models\OtelManzaralari  $otelManzaralari
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtelManzaralari $otelManzaralari, $id)
    {
        if(!Auth::user()->isAllow('hrm_vo_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $otelManzaralari->destroy($id);
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
