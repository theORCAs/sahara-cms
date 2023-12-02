<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitmenKursTalip;
use App\Http\Models\Teklifler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EgitmenKursTalipController extends HomeController
{
    private $prefix = "cds_view";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('im_cds_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Teklifler::where('teklifler.durum', '=', 2)
            ->whereHas('egitimKayit.egitimTarihi', function ($query) {
                $query->whereRaw("baslama_tarihi >= curdate()");
            })
            ->leftJoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftJoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->orderBy('egitim_tarihleri.baslama_tarihi', 'asc')
            ->select('teklifler.*')
            // ->toSql();
            ->paginate(30);
        foreach($liste as $key => $row) {
            $tmp_result = EgitmenKursTalip::where('teklif_id', $row->id)->where('kullanici_id', Auth::user()->id)
                ->select('id', 'secili_gun')
                ->first();

            $liste[$key]['hoca_secim'] = $tmp_result;
            $liste[$key]['hoca_secim_arr'] = isset($tmp_result->secili_gun) ? explode('|', $tmp_result->secili_gun) : '';
        }

        //return "aaa";
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "--"
        ];
        return view('egitmen.egitmen_kurssecim_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\EgitmenKursTalip  $egitmenKursTalip
     * @return \Illuminate\Http\Response
     */
    public function show(EgitmenKursTalip $egitmenKursTalip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitmenKursTalip  $egitmenKursTalip
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitmenKursTalip $egitmenKursTalip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitmenKursTalip  $egitmenKursTalip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitmenKursTalip $egitmenKursTalip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EgitmenKursTalip  $egitmenKursTalip
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitmenKursTalip $egitmenKursTalip)
    {
        //
    }

    public function secimYap(Request $request) {
        if(!Auth::user()->isAllow('im_cds_view')) {
            return response()->json([
                'cvp' => "0",
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {

            $result = EgitmenKursTalip::where('teklif_id', $request->teklif_id)
                ->where('kullanici_id', Auth::user()->id)
                ->first();

            $secim_arr = [];
            if(!empty($result) && isset($result->secili_gun)) {
                $secim_arr = explode("|", $result->secili_gun);
            }

            $gun = intval($request->gun) + 1;
            if($request->islem == "1") {
                if(!in_array($gun, $secim_arr)) {
                    array_push($secim_arr, $gun);
                }
            } else {
                $secim_arr = array_diff($secim_arr, [$gun]);
            }
            $secim_arr = array_diff($secim_arr, ['']);
            $secili_gun = implode('|', $secim_arr);
            EgitmenKursTalip::updateOrCreate([
                'id' => $result->id ?? null
            ], [
                'kullanici_id' => Auth::user()->id,
                'teklif_id' => $request->teklif_id,
                'secili_gun' => $secili_gun ?? null,
            ]);
            /*
            if(isset($result->id)) {
                $result->update([
                    'secili_gun' => $secili_gun
                ]);
            } else {
                // ekleme
                EgitmenKursTalip::create([
                    'kullanici_id' => Auth::user()->id,
                    'teklif_id' => $request->teklif_id,
                    'secili_gun' => $secili_gun,
                ]);
            }
            */
            return response()->json([
                'cvp' => "1",
                'msj' => ""
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => "0",
                'msj' => $e->getMessage()
            ]);
        }

    }
}
