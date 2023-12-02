<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimYerleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EgitimYerleriController extends HomeController
{
    private $prefix = "to_location";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
    );
    private $rules = array(
        'adi' => 'required',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('to_location_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimYerleri::wherenull('deleted_at')
            ->where('flg_silindi', '0')
            ->orderby('flg_default', 'desc')
            ->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Training Locations'
        ];
        return view('egitimler.egitimyerileri_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('to_location_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Training Location",
            'data' => new EgitimYerleri()
        ];
        return view('egitimler.egitimyerileri_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('to_location_add')) {
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

            EgitimYerleri::create([
                'adi' => $request->input('adi'),
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
     * @param  \App\Http\Models\EgitimYerleri  $egitimYerleri
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimYerleri $egitimYerleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitimYerleri  $egitimYerleri
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitimYerleri $egitimYerleri, $id)
    {
        if(!Auth::user()->isAllow('to_location_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Training Location",
            'data' => $egitimYerleri->findorfail($id)
        ];
        return view('egitimler.egitimyerileri_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitimYerleri  $egitimYerleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitimYerleri $egitimYerleri, $id)
    {
        if(!Auth::user()->isAllow('to_location_edit')) {
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

            $egitimYerleri->find($id)
                ->update([
                    'adi' => $request->input('adi'),
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
     * @param  \App\Http\Models\EgitimYerleri  $egitimYerleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitimYerleri $egitimYerleri, $id)
    {
        if(!Auth::user()->isAllow('to_location_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $egitimYerleri->destroy($id);
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
