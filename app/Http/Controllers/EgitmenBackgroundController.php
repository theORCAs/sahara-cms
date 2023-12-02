<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimKategori;
use App\Http\Models\Egitimler;
use App\Http\Models\EgitmenBackground;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EgitmenBackgroundController extends HomeController
{
    private $prefix = "bsnc_view";
    private $error_messages = array(

    );
    private $rules = array(

    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('im_bs_ncl_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $kategori_liste = EgitimKategori::wherenull('deleted_at')->get();
        $backround_liste = EgitmenBackground::where('egitmen_background.egitmen_id', Auth::user()->egitmenID())
            ->leftjoin('egitimler', 'egitimler.id', '=', 'egitmen_background.egitim_id')
            ->leftjoin('egitim_kategori', 'egitim_kategori.id', '=', 'egitimler.kategori_id')
            ->orderbyraw('if(egitmen_background.rate > 0, 1, 0) asc')
            ->orderby('egitim_kategori.adi', 'asc')
            ->orderby('egitimler.adi', 'asc')
            ->select('egitmen_background.*')
            ->get();

        $data = [
            'kategori_liste' => $kategori_liste,
            'background_liste' => $backround_liste,
            'prefix' => $this->prefix,
            'alt_baslik' => '--'
        ];

        return view('egitmen.background_selection', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('im_bs_ncl_view')) { // view bilerek eklendi. ekstradan add yetkisine gerek yok
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
            foreach($request->my_multi_select1 as $egitim_id) {
                EgitmenBackground::create([
                    'egitmen_id' => Auth::user()->egitmenID(),
                    'egitim_id' => $egitim_id,
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
     * Display the specified resource.
     *
     * @param  \App\Http\Models\EgitmenBackground  $egitmenBackground
     * @return \Illuminate\Http\Response
     */
    public function show(EgitmenBackground $egitmenBackground)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitmenBackground  $egitmenBackground
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitmenBackground $egitmenBackground)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitmenBackground  $egitmenBackground
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitmenBackground $egitmenBackground)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EgitmenBackground  $egitmenBackground
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitmenBackground $egitmenBackground)
    {
        //
    }

    public function egitimKategoriGetirJson(Request $request) {
        $result = Egitimler::where('kategori_id', '=', $request->kategori_id)
            ->where('flg_aktif', '=', 1)
            ->select('id', 'kodu', 'adi')
            ->orderby('sira', 'asc')
            ->get();
        return $result->toJson();

        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA'
        ]);
    }

    public function egitimOylamaYap(Request $request) {
        if(!Auth::user()->isAllow('im_bs_ncl_view')) { // view bilerek eklendi. ekstradan add yetkisine gerek yok
            return response()->json([
                'cvp' => 0,
                'mesaj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            EgitmenBackground::find($request->id)->update([
                'rate' => $request->rate
            ]);

            return response()->json([
                'cvp' => 1,
                'mesaj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => 0,
                'mesaj' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }

    }

    public function egitmenBackgroundKursSecim(Request $request) {
        if(!Auth::user()->isAllow('im_ibcs_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $kategori_liste = EgitimKategori::wherenull('deleted_at')->orderby('adi', 'asc')->get();
        $backround_liste = EgitmenBackground::wherenotnull('egitmen_background.rate')
            ->leftjoin('egitmenler', 'egitmenler.id', '=', 'egitmen_background.egitmen_id')
            ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmenler.kullanici_id')
            ->where('kullanicilar.flg_durum', '1')
            ->orderby('egitmenler.adi_soyadi', 'asc')
            ->select('egitmenler.id', 'egitmenler.adi_soyadi')
            ->selectRaw('count(egitmen_background.id) as egitim_sayisi')
            ->selectRaw('max(egitmen_background.created_at) son_guncelleme')
            ->groupby('egitmenler.id');
        if($request->kategori_id > 0)
            $backround_liste->leftjoin('egitimler', 'egitimler.id', '=', 'egitmen_background.egitim_id')
                ->where('egitimler.kategori_id', $request->kategori_id);

        $data = [
            'kategori_liste' => $kategori_liste,
            'background_liste' => $backround_liste->get(),
            'prefix' => $this->prefix,
            'alt_baslik' => '--',
            'secili_kategori_id' => $request->kategori_id
        ];

        return view('egitmen.admin_background_selection', $data);
    }

    public function egitmenBackgroundListeGetir(Request $request) {
        if(!Auth::user()->isAllow('im_ibcs_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $backround_liste = EgitmenBackground::where('egitmen_background.egitmen_id', $request->egitmen_id)
            ->leftjoin('egitimler', 'egitimler.id', '=', 'egitmen_background.egitim_id')
            ->leftjoin('egitim_kategori', 'egitim_kategori.id', '=', 'egitimler.kategori_id')
            ->select('egitim_kategori.adi as kategori_adi', 'egitimler.kodu', 'egitimler.adi', 'egitmen_background.rate')
            ->orderby('egitim_kategori.adi', 'asc')
            ->orderby('egitimler.adi', 'asc')
            ->orderby('egitmen_background.rate', 'desc')
            ->get();

        $data = [
            'liste' => $backround_liste
        ];

        return view('egitmen.admin_background_selection_list', $data);
    }
}
