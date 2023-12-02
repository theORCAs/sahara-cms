<?php

namespace App\Http\Controllers;

use App\Http\Models\HavayoluSirket;
use Illuminate\Http\Request;
use Validator;
use Auth;

class HavayoluSirketController extends HomeController
{
    private $prefix = "at_airlineentry";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('at_ae_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = HavayoluSirket::wherenull('deleted_at')
            ->orderBy('sira', 'asc')
            ->orderBy('adi', 'asc')
            ->paginate(50);

        $data = [
            'alt_baslik' => 'Airlines List',
            'prefix' => $this->prefix,
            'liste' => $liste
        ];

        return view("airport_transfer.airline_view", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('at_ae_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Airline",
            'data' => new HavayoluSirket()
        ];

        return view('airport_transfer.airline_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('at_ae_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Airline name is required.',
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
                    ->withErrors($validator);
            }

            HavayoluSirket::create([
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
     * @param  \App\Http\Models\HavayoluSirket  $havayoluSirket
     * @return \Illuminate\Http\Response
     */
    public function show(HavayoluSirket $havayoluSirket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\HavayoluSirket  $havayoluSirket
     * @return \Illuminate\Http\Response
     */
    public function edit(HavayoluSirket $havayoluSirket, $id)
    {
        if(!Auth::user()->isAllow('at_ae_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Airline",
            'data' => $havayoluSirket->findorfail($id)
        ];

        return view('airport_transfer.airline_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\HavayoluSirket  $havayoluSirket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HavayoluSirket $havayoluSirket, $idsi)
    {
        if(!Auth::user()->isAllow('at_ae_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Airline name is required.',
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
                    ->withErrors($validator);
            }

            $havayoluSirket::find($idsi)->update([
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
     * @param  \App\Http\Models\HavayoluSirket  $havayoluSirket
     * @return \Illuminate\Http\Response
     */
    public function destroy(HavayoluSirket $havayoluSirket, $id)
    {
        if(!Auth::user()->isAllow('at_ae_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $havayoluSirket::destroy($id);
            return redirect('/'.$this->prefix)
                ->with(['msj' => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }
    }
}
