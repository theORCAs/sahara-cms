<?php

namespace App\Http\Controllers;

use App\Http\Models\Referanslar;
use App\Http\Models\Sektorler;
use App\Http\Models\Ulkeler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ReferanslarController extends HomeController
{
    private $liste;
    private $ulke_id;
    private $prefix;
    private $alt_baslik;
    private $flg_notinlist;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ulkeler_liste = Referanslar::wherenull('referanslar.deleted_at')
            ->where('flg_notinlist', $this->flg_notinlist)
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'referanslar.ulke_id')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->selectRaw('count(ulkeler.id) ulke_sayisi')
            ->groupby('ulkeler.id')
            ->groupby('ulkeler.adi')
            ->orderby('ulkeler.adi')
            ->get();

        $data = [
            'ulke_id' => $this->ulke_id,
            'ulkeler_liste' => $ulkeler_liste,
            'liste' => $this->liste,
            'prefix' => $this->prefix,
            'alt_baslik' => $this->alt_baslik
        ];
        return view('website.referanslar.view', $data);
    }

    public function listede($ulke_id=null) {
        if($ulke_id > 0) {
            $this->liste = Referanslar::wherenull('referanslar.deleted_at')
                ->where('ulke_id', $ulke_id)
                ->where('flg_notinlist', 0)
                ->leftjoin('sektorler', 'sektorler.id', '=', 'referanslar.sektor_id')
                ->orderby('sektorler.adi', 'asc')
                ->orderby('referanslar.adi', 'asc')
                ->select('referanslar.*')
                ->paginate(20);
        } else {
            $this->liste = Referanslar::wherenull('referanslar.deleted_at')
                ->where('flg_notinlist', 0)
                ->leftjoin('ulkeler', 'ulkeler.id', '=', 'referanslar.ulke_id')
                ->leftjoin('sektorler', 'sektorler.id', '=', 'referanslar.sektor_id')
                ->orderby('ulkeler.adi', 'asc')
                ->orderby('sektorler.adi', 'asc')
                ->orderby('referanslar.adi', 'asc')
                ->select('referanslar.*')
                ->paginate(20);
        }
        $this->ulke_id = $ulke_id;
        $this->prefix = "rcm_referancelist";
        session(['CONT_PREFIX' => "rcm_referancelist"]);
        $this->alt_baslik = "Reference List (existing)";
        $this->flg_notinlist = 0;

        return $this->index();
    }

    public function listedeDegil($ulke_id=null) {

        if(!Auth::user()->isAllow('wmo_cilfc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        if($ulke_id > 0) {
            $this->liste = Referanslar::wherenull('referanslar.deleted_at')
                ->where('ulke_id', $ulke_id)
                ->where('flg_notinlist', 1)
                ->leftjoin('sektorler', 'sektorler.id', '=', 'referanslar.sektor_id')
                ->orderby('sektorler.adi', 'asc')
                ->orderby('referanslar.adi', 'asc')
                ->select('referanslar.*')
                ->paginate(20);
        } else {
            $this->liste = Referanslar::wherenull('referanslar.deleted_at')
                ->where('flg_notinlist', 1)
                ->leftjoin('ulkeler', 'ulkeler.id', '=', 'referanslar.ulke_id')
                ->leftjoin('sektorler', 'sektorler.id', '=', 'referanslar.sektor_id')
                ->orderby('ulkeler.adi', 'asc')
                ->orderby('sektorler.adi', 'asc')
                ->orderby('referanslar.adi', 'asc')
                ->select('referanslar.*')
                ->paginate(20);
        }
        $this->ulke_id = $ulke_id;
        $this->prefix = "rcm_contactlist";
        session(['CONT_PREFIX' => "rcm_contactlist"]);
        $this->alt_baslik = "Company/Institutions List from Contacts";
        $this->flg_notinlist = 1;

        return $this->index();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wmo_cilfc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => session('CONT_PREFIX'),
            'alt_baslik' => "Add New Company/Institutions List from Contacts",
            'data' => new Referanslar(),
            'ulke_listesi' => Ulkeler::wherenull('deleted_at')->where('flg_aktif', '1')->orderby('adi', 'asc')->get(),
            'sektor_listesi' => Sektorler::wherenull('deleted_at')->orderby('adi', 'asc')->get()
        ];
        return view('website.referanslar.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('wmo_faq_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Company name is required.',
                'ulke_id.required' => 'Country is required.',
                'sektor_id.required' => 'Sector is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'ulke_id' => 'required',
                'sektor_id' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Referanslar::create([
                'adi' => $request->input('adi'),
                'ulke_id' => $request->input('ulke_id'),
                'sektor_id' => $request->input('sektor_id'),
                'web_sayfasi' => $request->web_sayfasi,
                'sira' => $request->sira,
                'flg_aktif' => intval($request->flg_aktif),
                'flg_notinlist' => intval($request->flg_notinlist)
            ]);
            return redirect('/'.session('CONT_PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Referanslar  $referanslar
     * @return \Illuminate\Http\Response
     */
    public function show(Referanslar $referanslar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Referanslar  $referanslar
     * @return \Illuminate\Http\Response
     */
    public function edit(Referanslar $referanslar, $id)
    {
        if(!Auth::user()->isAllow('wmo_faq_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => session('CONT_PREFIX'),
            'alt_baslik' => "Edit",
            'data' => $referanslar->findorfail($id),
            'ulke_listesi' => Ulkeler::wherenull('deleted_at')->where('flg_aktif', '1')->orderby('adi', 'asc')->get(),
            'sektor_listesi' => Sektorler::wherenull('deleted_at')->orderby('adi', 'asc')->get()
        ];
        return view('website.referanslar.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Referanslar  $referanslar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Referanslar $referanslar, $id)
    {
        if(!Auth::user()->isAllow('wmo_cilfc_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Company name is required.',
                'ulke_id.required' => 'Country is required.',
                'sektor_id.required' => 'Sector is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'ulke_id' => 'required',
                'sektor_id' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $referanslar->find($id)
                ->update([
                    'adi' => $request->input('adi'),
                    'ulke_id' => $request->input('ulke_id'),
                    'sektor_id' => $request->input('sektor_id'),
                    'web_sayfasi' => $request->web_sayfasi,
                    'sira' => $request->sira,
                    'flg_aktif' => intval($request->flg_aktif),
                    'flg_notinlist' => intval($request->flg_notinlist)
                ]);
            return redirect('/'.session('CONT_PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Referanslar  $referanslar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Referanslar $referanslar, $id)
    {
        if(!Auth::user()->isAllow('wmo_cilfc_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $referanslar->destroy($id);

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
