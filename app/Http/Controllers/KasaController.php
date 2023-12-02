<?php

namespace App\Http\Controllers;

use App\Http\Models\GiderKalemleri;
use App\Http\Models\Kasa;
use App\Http\Models\KullaniciRolleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

class KasaController extends HomeController
{
    private $prefix = "aca_spent";
    private $error_messages = array(
        'tarih.required' => 'Date is required.',
        'ilgili_kisi.required' => 'Related person is required.',
        'tarih.date_format' => 'Date format must be day.month.Year.',
        'gider_kalem_id.required' => 'Expense type is required.',
        'gider_tl.required' => "Amount is required",
        'gider_tl.numeric' => "Amount must be number",
    );
    private $rules = array(
        'tarih' => 'required|date_format:d.m.Y',
        'ilgili_kisi' => 'required',
        'gider_kalem_id' => 'required',
        'gider_tl' => 'required|numeric',
    );
    private $s_filtre_yil;
    private $s_ilgili_kisi;
    private $s_gider_kalem_id;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('ama_spent_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Kasa::wherenull('deleted_at')
            ->wherenotnull('gider_kalem_id')
            ->orderby('tarih', 'desc')
            ->orderby('id', 'desc');
        $yillar_liste = clone $liste;
        $yillar_liste->selectraw('year(hes_kasa.tarih) as yil')
            ->groupby(DB::raw('year(hes_kasa.tarih)'));

        $liste->whereraw('year(hes_kasa.tarih) = '.($this->s_filtre_yil ?? date('Y')));
        if($this->s_gider_kalem_id != "")
            $liste->where('gider_kalem_id', $this->s_gider_kalem_id);
        if($this->s_ilgili_kisi != "")
            $liste->where('ilgili_kisi', $this->s_ilgili_kisi);

//        $total_spent = $liste->whereraw('year(hes_kasa.tarih) = '.date('Y'))->sum('per_gider_tl');
//        $total_given = $liste->whereraw('year(hes_kasa.tarih) = '.date('Y'))->sum('gider_tl');
        $total_spent = $liste->sum('per_gider_tl');
        $total_given = $liste->sum('gider_tl');

        $data = [
            'liste' => $liste->paginate(20),
            'prefix' => $this->prefix,
            'alt_baslik' => "Spent",
            'totals' => [
                'spent' => $total_spent,
                'given' => $total_given,
                'fark' => floatval($total_given) - floatval($total_spent)
            ],
            's_filtre_yil' => $this->s_filtre_yil,
            's_ilgili_kisi' => $this->s_ilgili_kisi,
            's_gider_kalem_id' => $this->s_gider_kalem_id,
            'yillar_liste' => $yillar_liste->get(),
            'gider_turleri' => GiderKalemleri::orderby('sira')->get(),
            'personel_listesi' => Kasa::leftjoin('kullanicilar', 'kullanicilar.id', '=', 'hes_kasa.ilgili_kisi')
                ->wherenull('hes_kasa.deleted_at')
                ->wherenotnull('hes_kasa.ilgili_kisi')
                ->orderby('kullanicilar.adi_soyadi')
                ->groupby('kullanicilar.id')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi')
                ->get()
        ];
        return view('admin_operation.hesap_modul.kasagider_view', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('ama_spent_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Spent",
            'data' => new Kasa(),
            'gider_turleri' => GiderKalemleri::orderby('sira')->get(),
            'personel_listesi' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->where('kullanicilar.flg_durum', '1')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->get()
        ];
        return view('admin_operation.hesap_modul.kasagider_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('ama_spent_add')) {
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

            Kasa::create([
                'tarih' => date("Y-m-d", strtotime($request->input('tarih'))),
                'islem_yapan' => session('KULLANICI_ID'),
                'ilgili_kisi' => $request->ilgili_kisi,
                'gider_kalem_id' => $request->gider_kalem_id,
                'gider_tl' => (float)$request->gider_tl ?? 0,
                'per_gider_tl' => (float)$request->per_gider_tl ?? 0,
                'kayit_ip' => $request->ip(),
                'aciklama' => $request->aciklama
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
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function show(Kasa $kasa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function edit(Kasa $kasa, $id)
    {
        if(!Auth::user()->isAllow('ama_spent_edit')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Spent",
            'data' => $kasa->findorfail($id),
            'gider_turleri' => GiderKalemleri::orderby('sira')->get(),
            'personel_listesi' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->where('kullanicilar.flg_durum', '1')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->get()
        ];
        return view('admin_operation.hesap_modul.kasagider_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kasa $kasa, $id)
    {
        if(!Auth::user()->isAllow('ama_spent_edit')) {
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

            $kasa->findorfail($id)->update([
                'tarih' => date("Y-m-d", strtotime($request->input('tarih'))),
                'islem_yapan' => session('KULLANICI_ID'),
                'ilgili_kisi' => $request->ilgili_kisi,
                'gider_kalem_id' => $request->gider_kalem_id,
                'gider_tl' => (float)$request->gider_tl ?? 0,
                'per_gider_tl' => (float)$request->per_gider_tl ?? 0,
                'kayit_ip' => $request->ip(),
                'aciklama' => $request->aciklama
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
     * @param  \App\Http\Models\Kasa  $kasa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kasa $kasa, $id)
    {
        if(!Auth::user()->isAllow('ama_spent_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $kasa->destroy($id);
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

    public function search(Request $request) {
        $this->s_filtre_yil = $request->s_filtre_yil;
        $this->s_ilgili_kisi = $request->s_ilgili_kisi;
        $this->s_gider_kalem_id = $request->s_gider_kalem_id;

        return $this->index();
    }


    public function personelGiderListe() {
        $liste = Kasa::wherenull('deleted_at')
            ->where('ilgili_kisi', auth()->user()->id)
            ->wherenotnull('gider_kalem_id')
            ->orderby('tarih', 'desc')
            ->orderby('id', 'desc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => "amp_view",
            'alt_baslik' => "Expense Types"
        ];
        return view('admin_operation.hesap_modul.personel_kasagider_view', $data);
    }
}
