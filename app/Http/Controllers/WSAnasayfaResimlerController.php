<?php

namespace App\Http\Controllers;

use App\Http\Models\WSAnasayfaResimler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Auth;

class WSAnasayfaResimlerController extends HomeController
{
    private $prefix = "ws_hppictures";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
        'sira.required' => 'Order number is required.',
        'sira.numeric' => 'The order number must be a number.',
        'resim.required' => 'Image must be selected',
        'resim.mimes' => 'Image must be jpeg, bmp or png'
    );
    private $rules = array(
        'adi' => 'required',
        'sira' => 'required|numeric',
        'resim' => 'required|mimes:jpeg,bmp,png'
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('wmo_hp_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = WSAnasayfaResimler::wherenull('deleted_at')
            ->orderby('flg_aktif', 'desc')
            ->orderby('sira', 'asc')
            ->paginate(20);
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Homepage Pictures"
        ];
        return view('website.anasayfa_resim.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wmo_hp_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Homepage Pictures",
            'data' => new WSAnasayfaResimler()
        ];
        return view('website.anasayfa_resim.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('wmo_hp_add')) {
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

            WSAnasayfaResimler::create([
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
                'flg_aktif' => intval($request->flg_aktif),
                'resim' => $request->file("resim")->store("public/anasayfa_resim")
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
     * @param  \App\Http\Models\WSAnasayfaResimler  $wSAnasayfaResimler
     * @return \Illuminate\Http\Response
     */
    public function show(WSAnasayfaResimler $wSAnasayfaResimler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\WSAnasayfaResimler  $wSAnasayfaResimler
     * @return \Illuminate\Http\Response
     */
    public function edit(WSAnasayfaResimler $wSAnasayfaResimler, $id)
    {
        if(!Auth::user()->isAllow('wmo_hp_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Homapage Pictures",
            'data' => $wSAnasayfaResimler->findorfail($id)
        ];
        return view('website.anasayfa_resim.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\WSAnasayfaResimler  $wSAnasayfaResimler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WSAnasayfaResimler $wSAnasayfaResimler, $id)
    {
        if(!Auth::user()->isAllow('wmo_hp_edit')) {
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

            $wSAnasayfaResimler->find($id)
                ->update([
                    'adi' => $request->input('adi'),
                    'sira' => $request->sira,
                    'flg_aktif' => intval($request->flg_aktif)
                ]);
            if($request->file('resim') != "") {
                $result = $wSAnasayfaResimler->find($id);
                if($result->resim != "") {
                    Storage::delete($result->resim);
                }
                $wSAnasayfaResimler->find($id)
                    ->update([
                        'resim' => $request->file("resim")->store("public/anasayfa_resim")
                    ]);
            }
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
     * @param  \App\Http\Models\WSAnasayfaResimler  $wSAnasayfaResimler
     * @return \Illuminate\Http\Response
     */
    public function destroy(WSAnasayfaResimler $wSAnasayfaResimler, $id)
    {
        if(!Auth::user()->isAllow('wmo_hp_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $wSAnasayfaResimler->destroy($id);
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
