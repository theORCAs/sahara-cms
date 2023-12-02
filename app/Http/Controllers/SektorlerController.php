<?php

namespace App\Http\Controllers;

use App\Http\Models\Sektorler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class SektorlerController extends HomeController
{
    private $prefix = "rcm_sectors";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('wmo_rcm_s_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = Sektorler::wherenull('deleted_at')
            ->orderby('adi', 'asc')
            ->paginate(20);
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Sectors"
        ];
        return view('website.referans_sirket.sektorler_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wmo_rcm_s_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Sector",
            'data' => new Sektorler()
        ];
        return view('website.referans_sirket.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('wmo_rcm_s_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Sector name is required.',
            );
            $rules = array(
                'adi' => 'required'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Sektorler::create([
                'adi' => $request->input('adi')
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
     * @param  \App\Http\Models\Sektorler  $sektorler
     * @return \Illuminate\Http\Response
     */
    public function show(Sektorler $sektorler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Sektorler  $sektorler
     * @return \Illuminate\Http\Response
     */
    public function edit(Sektorler $sektorler, $id)
    {
        if(!Auth::user()->isAllow('wmo_rcm_s_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Sector",
            'data' => $sektorler->findorfail($id)
        ];
        return view('website.referans_sirket.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Sektorler  $sektorler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sektorler $sektorler, $id)
    {
        if(!Auth::user()->isAllow('wmo_rcm_s_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Sector name is required.'
            );
            $rules = array(
                'adi' => 'required'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $sektorler->find($id)
                ->update([
                    'adi' => $request->input('adi')
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
     * @param  \App\Http\Models\Sektorler  $sektorler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sektorler $sektorler, $id)
    {
        if(!Auth::user()->isAllow('wmo_rcm_s_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $sektorler->destroy($id);
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
