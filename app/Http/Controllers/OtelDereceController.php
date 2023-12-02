<?php

namespace App\Http\Controllers;

use App\Http\Models\OtelDerece;
use Illuminate\Http\Request;
use Validator;
use Auth;

class OtelDereceController extends HomeController
{
    private $prefix = "hrm_star";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('hrm_sr_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = OtelDerece::orderby('sira', 'asc')
            ->get();
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Define hotel star rating"
        ];
        return view("hotel_registration.derece_view", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('hrm_sr_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Define hotel star rating",
            'data' => new OtelDerece()
        ];

        return view('hotel_registration.derece_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('hrm_sr_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Star Rating name is required.',
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
                    ->withErrors($validator);
            }

            OtelDerece::create([
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
     * @param  \App\Http\Models\OtelDerece  $otelDerece
     * @return \Illuminate\Http\Response
     */
    public function show(OtelDerece $otelDerece)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\OtelDerece  $otelDerece
     * @return \Illuminate\Http\Response
     */
    public function edit(OtelDerece $otelDerece, $id)
    {
        if(!Auth::user()->isAllow('hrm_sr_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Hotel Star",
            'data' => $otelDerece->findorfail($id)
        ];
        return view('hotel_registration.derece_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\OtelDerece  $otelDerece
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtelDerece $otelDerece, $idsi)
    {
        if(!Auth::user()->isAllow('hrm_sr_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Star Rating name is required.',
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

            $otelDerece->find($idsi)->update([
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
     * @param  \App\Http\Models\OtelDerece  $otelDerece
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtelDerece $otelDerece, $id)
    {
        if(!Auth::user()->isAllow('hrm_sr_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $otelDerece->destroy($id);
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
