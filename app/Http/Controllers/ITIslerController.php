<?php

namespace App\Http\Controllers;

use App\Http\Models\ITIsler;
use App\Http\Models\ITIsTurleri;
use App\Http\Models\ITKategoriler;
use App\Http\Models\ITOncelikTurleri;
use App\Http\Models\KullaniciRolleri;
use App\Http\Models\Referanslar;
use App\Http\Models\Ulkeler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ITIslerController extends HomeController
{
    private $liste;
    private $prefix;
    private $alt_baslik;
    private $error_messages = array(
        'isturu_id.required' => 'Job Type is required.',
        'adi_soyadi.required' => 'Requester/Inquirer name is required.',
        'email.required' => 'Requester/Inquirer email is required.',
        'email.email' => 'Requester/Inquirer email must be valid.',
        'ulke_id.required' => 'Requester/Inquirer Country is required.',
        'ref_sirket_id.required' => 'Requester/Inquirer Company is required.',
        'sirket_adi.required_if' => 'Requester/Inquirer Company name is required.',
        'istek_yapan.required' => 'Reported/Registered by name is required.',
        'iy_email.required' => 'Reported/Registered by email is required.',
        'iy_email.email' => 'Reported/Registered by email must be valid.',
        'is_tarihi.required' => 'Job date is required.',
        'is_tarihi.date_format' => 'Job date must be valid day.month.year format.',
        'is_tanimi.required' => 'Additional Notes is required.',
        'ilgili_kisi.required' => 'Action by is required.',
    );
    private $rules = array(
        'isturu_id' => 'required',
        'adi_soyadi' => 'required',
        'email' => 'required|email',
        'ulke_id' => 'required',
        'ref_sirket_id' => 'required',
        'sirket_adi' => 'required_if:ref_sirket_id,-1',
        'istek_yapan' => 'required',
        'iy_email' => 'required|email',
        'is_tarihi' => 'required|date_format:d.m.Y',
        'is_tanimi' => 'required',
        'ilgili_kisi' => 'required',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $onem_listesi = ITOncelikTurleri::wherenull('deleted_at')
            ->orderby('adi', 'asc')
            ->get();

        $data = [
            'onem_listesi' => $onem_listesi,
            'liste' => $this->liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => $this->alt_baslik
        ];
        return view('office_management.istakip.isler_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('jfm_wait_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Add New Jobs",
            'data' => new ITIsler(),
            'kategori_listesi' => ITKategoriler::orderby('sira', 'asc')->orderby('adi', 'asc')->select('id', 'adi')->get(),
            'ulke_listesi' => Ulkeler::where('flg_aktif', '1')->orderby('adi', 'asc')->get(),
            'istek_yapan_liste' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 6, 8, 12, 13])
                    ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                    ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                    ->where('kullanicilar.flg_durum', '1')
                    ->groupby('kullanicilar.id')
                    ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                    ->orderby('roller.adi', 'asc')
                    ->orderby('kullanicilar.adi_soyadi', 'asc')
                    ->get()
        ];
        return view('office_management.istakip.isler_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('jfm_wait_add')) {
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

            ITIsler::create([
                'isturu_id' => $request->input('isturu_id'),
                'adi_soyadi' => $request->adi_soyadi,
                'email' => $request->email,
                'telefon' => $request->telefon,
                'ulke_id' => $request->ulke_id,
                'ref_sirket_id' => intval($request->ref_sirket_id) > 0 ? $request->ref_sirket_id : null,
                'sirket_adi' => $request->sirket_adi,
                'istek_yapan' => $request->istek_yapan,
                'iy_email' => $request->iy_email,
                'iy_telefon' => $request->iy_telefon,
                'is_tarihi' => date('Y-m-d', strtotime($request->is_tarihi)),
                'is_tanimi' => $request->is_tanimi,
                'ataanan_kisi' => $request->ataanan_kisi,
                'durum' => $request->durum,
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
     * @param  \App\Http\Models\ITIsler  $iTIsler
     * @return \Illuminate\Http\Response
     */
    public function show(ITIsler $iTIsler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\ITIsler  $iTIsler
     * @return \Illuminate\Http\Response
     */
    public function edit(ITIsler $iTIsler, $id)
    {
        if(!Auth::user()->isAllow('jfm_wait_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Update Job",
            'data' => $iTIsler->where('it_isler.id', $id)
                ->leftjoin('it_isturleri', 'it_isturleri.id', '=', 'it_isler.isturu_id')
                ->select('it_isler.*', 'it_isturleri.kategori_id')
                ->first()
            ,
            'kategori_listesi' => ITKategoriler::orderby('sira', 'asc')->orderby('adi', 'asc')->select('id', 'adi')->get(),
            'ulke_listesi' => Ulkeler::where('flg_aktif', '1')->orderby('adi', 'asc')->get(),
            'istek_yapan_liste' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->where('kullanicilar.flg_durum', '1')
                ->groupby('kullanicilar.id')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->get(),
        ];
        return view('office_management.istakip.isler_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\ITIsler  $iTIsler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ITIsler $iTIsler, $id)
    {
        if(!Auth::user()->isAllow('jfm_wait_edit')) {
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

            $iTIsler::findorfail($id)->update([
                'isturu_id' => $request->input('isturu_id'),
                'adi_soyadi' => $request->adi_soyadi,
                'email' => $request->email,
                'telefon' => $request->telefon,
                'ulke_id' => $request->ulke_id,
                'ref_sirket_id' => intval($request->ref_sirket_id) > 0 ? $request->ref_sirket_id : null,
                'sirket_adi' => $request->sirket_adi,
                'istek_yapan' => $request->istek_yapan,
                'iy_email' => $request->iy_email,
                'iy_telefon' => $request->iy_telefon,
                'is_tarihi' => date('Y-m-d', strtotime($request->is_tarihi)),
                'is_tanimi' => $request->is_tanimi,
                'ataanan_kisi' => $request->ataanan_kisi,
                'durum' => $request->durum,
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
     * @param  \App\Http\Models\ITIsler  $iTIsler
     * @return \Illuminate\Http\Response
     */
    public function destroy(ITIsler $iTIsler, $id)
    {
        if(!Auth::user()->isAllow('sm_se_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $iTIsler->destroy($id);
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

    public function bekleyen() {
        $liste = ITIsler::wherenull('deleted_at')
            ->where('durum', 0)
            ->orderby('is_tarihi', 'asc')
            ->paginate(10);
        $this->liste = $liste;
        session(['PREFIX' => 'jfu_waiting']);

        $this->alt_baslik = "Jobs Waiting";
        return $this->index();
    }
    public function tamamlanmis() {
        $liste = ITIsler::wherenull('deleted_at')
            ->where('durum', 1)
            ->orderby('is_tarihi', 'desc')
            ->paginate(10);
        $this->liste = $liste;
        session(['PREFIX' => 'jfu_completed']);

        $this->alt_baslik = "Jobs Completed";
        return $this->index();
    }

    public function isTurleriGetirJson(Request $request) {
        $result = ITIsTurleri::where('it_isturleri.kategori_id', $request->kategori_id)
            ->leftjoin('it_tekrar_turleri', 'it_tekrar_turleri.id', '=', 'it_isturleri.tekrar_id')
            ->orderby('it_isturleri.adi', 'asc')
            ->select('it_isturleri.id', 'it_isturleri.adi', 'it_tekrar_turleri.adi as isturu_adi')
            ->get();

        return response()->json($result);
    }

    public function sirketListeGetirJson(Request $request) {
        $result = Referanslar::where('flg_aktif', '1')
            ->where('ulke_id', $request->ulke_id)
            ->where('flg_notinlist', '0')
            ->orderby('adi', 'asc')
            ->select('id', 'adi')
            ->get();

            return response()->json($result);
    }

}
