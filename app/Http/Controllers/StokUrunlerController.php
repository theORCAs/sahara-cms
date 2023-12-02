<?php

namespace App\Http\Controllers;

use App\Http\Models\Stoklar;
use App\Http\Models\StokUrunler;
use App\Http\Models\Teklifler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Validator;
use Auth;

class StokUrunlerController extends HomeController
{
    private $prefix = "sm_view";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
        'uyari_limiti.required' => 'Warning Limit is required.',
        'uyari_limiti.numeric' => 'Warning Limit must be number',
    );
    private $rules = array(
        'adi' => 'required',
        'uyari_limiti' => 'required|numeric',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('sm_se_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = StokUrunler::wherenull('deleted_at')
            ->orderby('flg_otodusme', 'desc')
            ->orderby('flg_aktif', 'desc')
            ->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Stock Entry'
        ];
        return view('office_management.stok_modul.stok_view', $data);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('sm_se_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Stock",
            'data' => new StokUrunler()
        ];
        return view('office_management.stok_modul.stok_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('sm_se_add')) {
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

            StokUrunler::create([
                'adi' => $request->input('adi'),
                'uyari_limit' => $request->uyari_limiti,
                'flg_otodusme' => intval($request->flg_otodusme),
                'flg_aktif' => intval($request->flg_aktif),
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
     * @param  \App\Http\Models\StokUrunler  $stokUrunler
     * @return \Illuminate\Http\Response
     */
    public function show(StokUrunler $stokUrunler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\StokUrunler  $stokUrunler
     * @return \Illuminate\Http\Response
     */
    public function edit(StokUrunler $stokUrunler, $id)
    {
        if(!Auth::user()->isAllow('sm_se_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Stock",
            'data' => $stokUrunler->findorfail($id)
        ];
        return view('office_management.stok_modul.stok_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\StokUrunler  $stokUrunler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StokUrunler $stokUrunler, $id)
    {
        if(!Auth::user()->isAllow('sm_se_edit')) {
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

            $stokUrunler::findorfail($id)->update([
                'adi' => $request->input('adi'),
                'uyari_limit' => $request->uyari_limiti,
                'flg_otodusme' => intval($request->flg_otodusme),
                'flg_aktif' => intval($request->flg_aktif),
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
     * @param  \App\Http\Models\StokUrunler  $stokUrunler
     * @return \Illuminate\Http\Response
     */
    public function destroy(StokUrunler $stokUrunler, $id)
    {
        if(!Auth::user()->isAllow('sm_se_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $stokUrunler->destroy($id);
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

    public function stokGiris($id) {
        if(!Auth::user()->isAllow('sm_se_stokgiris')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $bilgi = StokUrunler::find($id);
        $data = [
            'alt_baslik' => $bilgi->adi." Stock Entry",
            'stok_urun_id' => $id,
            'prefix' => $this->prefix,
            'data' => new Stoklar()
        ];
        return view('office_management.stok_modul.stok_giris', $data);
    }

    public function stokGirisKaydet(Request $request) {
        if(!Auth::user()->isAllow('sm_se_stokgiris')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'stok_urun_id.required' => 'Stock product is required.',
                'stok_urun_id.numeric' => 'Stock product must be number',
                'giris.numeric' => 'Stock entry must be number',
                'cikis.numeric' => 'Stock output must be number',
                'aciklama.required' => 'Explanation is required.',
            );
            $rules = array(
                'stok_urun_id' => 'required|numeric',
                'giris' => 'sometimes|nullable|numeric',
                'cikis' => 'sometimes|nullable|numeric',
                'aciklama' => 'required',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Stoklar::create([
                'stok_urun_id' => $request->input('stok_urun_id'),
                'giris' => intval($request->giris),
                'cikis' => intval($request->cikis),
                'aciklama' => $request->aciklama,
                'kayit_ip' => $request->ip(),
                'created_by' => Auth::user()->id
            ]);
            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function stokListesi($id) {
        if(!Auth::user()->isAllow('sm_se_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Stoklar::where('stok_urun_id', $id)
            ->wherenull('deleted_at')
            ->orderby('created_at', 'desc')
            ->paginate(20);

        $stok_bilgi = StokUrunler::find($id);
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => $stok_bilgi->adi.' Stock Spending/Deduction List',
            'stok_bilgi' => $stok_bilgi
        ];
        return view('office_management.stok_modul.stok_urun_listesi', $data);
    }

    public function stokListesiDelete($id) {
        if(!Auth::user()->isAllow('sm_se_stokliste_del')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        try {
            Stoklar::destroy($id);
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

    public function egitimListe() {
        $liste = Teklifler::wherenull('teklifler.deleted_at')
            ->where('teklifler.durum', '2')
            ->whereHas('egitimKayit.egitimTarihi', function (Builder $query) {
                $query->whereRaw('date_add(baslama_tarihi, interval ifnull(egitim_suresi, 5) day) >= curdate()');
            })
            /*
            ->with(['egitimKayitlar' => function ($query) {
                $query->orderby('id', 'desc');
            }])
            */
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->orderby('egitim_tarihleri.baslama_tarihi', 'asc')
            ->select("teklifler.*")
            ->paginate(20)
        ;

        //return $liste->toSql();

        $data = [
            'liste' => $liste,
            'prefix' => 'sm_egitimliste',
            'alt_baslik' => 'Training List'
        ];
        return view('office_management.stok_modul.egitimler_view', $data);
    }
}
