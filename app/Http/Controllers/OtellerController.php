<?php

namespace App\Http\Controllers;

use App\Http\Models\OtelBolgeleri;
use App\Http\Models\OtelDerece;
use App\Http\Models\Oteller;
use App\Http\Models\OtellerinOdaTipleri;
use App\Http\Models\OtelOdaTipleri;
use App\Http\Models\OtelSehirleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

class OtellerController extends HomeController
{
    private $prefix = "hrm_list";
    private $s_sehir_id;
    private $s_bolge_id;
    private $s_derece_id;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('hrm_hl_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $sql = Oteller::wherenull('otl_oteller.deleted_at')
            ->leftJoin('otl_sehir', 'otl_sehir.id', 'otl_oteller.sehir_id')
            ->orderBy('otl_sehir.sira', 'asc')
            ->select('otl_oteller.*')
            ;
        if($this->s_sehir_id > 0) $sql = $sql->where('otl_oteller.sehir_id', $this->s_sehir_id);
        if($this->s_bolge_id > 0) $sql = $sql->where('otl_oteller.bolge_id', $this->s_bolge_id);
        if($this->s_derece_id > 0) $sql = $sql->where('otl_oteller.derece_id', $this->s_derece_id);

        $liste = $sql->get();

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Hotel Lists",
            'sehirler' => OtelSehirleri::orderby('sira', 'asc')->orderby('adi', 'asc')->get(),
            's_sehir_id' => $this->s_sehir_id,
            's_bolge_id' => $this->s_bolge_id,
            's_derece_id' => $this->s_derece_id
        ];

        return view("hotel_registration.otel_view", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('hrm_hl_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Hotel",
            'data' => new Oteller(),
            'sehirler' => OtelSehirleri::orderby('sira', 'asc')->orderby('adi', 'asc')->get(),
            'dereceler' => OtelDerece::orderby('sira', 'asc')->select('id', 'adi')->get(),
            'odatipleri' => OtelOdaTipleri::orderby('sira', 'asc')->select('id', 'adi')->get()
        ];
        return view('hotel_registration.otel_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('hrm_hl_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Hotel name is required.',
                'sehir_id.required' => 'City is required.',
                'bolge_id.required' => 'Region-Semt is required.',
                'derece_id.required' => 'Star Rating is required.',
                'email.email' => 'Email must be valid.',
                'koordinat_x.numeric' => 'Coordinates must be number.',
                'koordinat_y.numeric' => 'Coordinates must be number.',
                'ofise_uzaklik.numeric' => 'Distance to SAHARA HQ (in Km) must be number.',
                'oda_sayisi.numeric' => 'Number of rooms must be number.',
                'toplanti_oda_sayisi.numeric' => 'Number of rooms must be number.',
                'yarim_ucret.numeric' => 'Meeting Room Half-Day Rate must be number.',
                'tam_ucret.numeric' => 'Meeting Room Full-Day Rate must be number.',
                'min_katilimci.numeric' => 'Meeting Room Min Pax must be number.',
            );
            $rules = array(
                'adi' => 'required',
                'sehir_id' => 'required',
                'bolge_id' => 'required',
                'derece_id' => 'required',
                'email' => 'sometimes|nullable|email',
                'koordinat_x' => 'sometimes|nullable|numeric',
                'koordinat_y' => 'sometimes|nullable|numeric',
                'ofise_uzaklik' => 'sometimes|nullable|numeric',
                'oda_sayisi' => 'sometimes|nullable|numeric',
                'toplanti_oda_sayisi' => 'sometimes|nullable|numeric',
                'yarim_ucret' => 'sometimes|nullable|numeric',
                'tam_ucret' => 'sometimes|nullable|numeric',
                'min_katilimci' => 'sometimes|nullable|numeric',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Oteller::create([
                'adi' => $request->input('adi'),
                'sehir_id' => $request->sehir_id,
                'bolge_id' => $request->bolge_id,
                'derece_id' => $request->derece_id,
                'web_adresi' => $request->web_adresi,
                'telefon' => $request->telefon,
                'email' => $request->email,
                'adres' => $request->adres,
                'koordinat_x' => $request->koordinat_x,
                'koordinat_y' => $request->koordinat_y,
                'ofise_uzaklik' => $request->ofise_uzaklik,
                'oda_sayisi' => $request->oda_sayisi,
                'toplanti_oda_sayisi' => $request->toplanti_oda_sayisi,
                'yarim_ucret' => $request->yarim_ucret,
                'tam_ucret' => $request->tam_ucret,
                'min_katilimci' => $request->min_katilimci,
                'toplanti_oda_aciklama' => $request->toplanti_oda_aciklama,
                'flg_fiyatsor' => $request->flg_fiyatsor,
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
     * @param  \App\Http\Models\Oteller  $oteller
     * @return \Illuminate\Http\Response
     */
    public function show(Oteller $oteller)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Oteller  $oteller
     * @return \Illuminate\Http\Response
     */
    public function edit(Oteller $oteller, $id)
    {
        if(!Auth::user()->isAllow('hrm_hl_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Hotel",
            'data' => $oteller->findorfail($id),
            'sehirler' => OtelSehirleri::orderby('sira', 'asc')->orderby('adi', 'asc')->get(),
            'dereceler' => OtelDerece::orderby('sira', 'asc')->select('id', 'adi')->get(),
            'odatipleri' => DB::select("select otl_oda_tipleri.id, otl_oda_tipleri.adi, otl_oteller_odatip.ucret_alis, otl_oteller_odatip.ucret_satis, otl_oteller_odatip.flg_na, 
                    if(otl_oteller_odatip.id is not null, 1, 0) secilmis
                from otl_oda_tipleri
                    left join otl_oteller_odatip on otl_oteller_odatip.oda_tip_id = otl_oda_tipleri.id and otl_oteller_odatip.otel_id = ".$id."
                order by otl_oda_tipleri.sira")
        ];
        return view('hotel_registration.otel_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Oteller  $oteller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Oteller $oteller, $id)
    {


        if(!Auth::user()->isAllow('hrm_hl_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Hotel name is required.',
                'sehir_id.required' => 'City is required.',
                'bolge_id.required' => 'Region-Semt is required.',
                'derece_id.required' => 'Star Rating is required.',
                'email.email' => 'Email must be valid.',
                'koordinat_x.numeric' => 'Coordinates must be number.',
                'koordinat_y.numeric' => 'Coordinates must be number.',
                'ofise_uzaklik.numeric' => 'Distance to SAHARA HQ (in Km) must be number.',
                'oda_sayisi.numeric' => 'Number of rooms must be number.',
                'toplanti_oda_sayisi.numeric' => 'Number of rooms must be number.',
                'yarim_ucret.numeric' => 'Meeting Room Half-Day Rate must be number.',
                'tam_ucret.numeric' => 'Meeting Room Full-Day Rate must be number.',
                'min_katilimci.numeric' => 'Meeting Room Min Pax must be number.',
            );
            $rules = array(
                'adi' => 'required',
                'sehir_id' => 'required',
                'bolge_id' => 'required',
                'derece_id' => 'required',
                'email' => 'sometimes|nullable|email',
                'koordinat_x' => 'sometimes|nullable|numeric',
                'koordinat_y' => 'sometimes|nullable|numeric',
                'ofise_uzaklik' => 'sometimes|nullable|numeric',
                'oda_sayisi' => 'sometimes|nullable|numeric',
                'toplanti_oda_sayisi' => 'sometimes|nullable|numeric',
                'yarim_ucret' => 'sometimes|nullable|numeric',
                'tam_ucret' => 'sometimes|nullable|numeric',
                'min_katilimci' => 'sometimes|nullable|numeric',
            );

            foreach($request->oda_tip_id as $key => $oda_tip_id) {
                $rules['ucret_alis_'.$oda_tip_id] = "required|numeric";
                $error_messages['ucret_alis_'.$oda_tip_id.".required"] = 'Room type buy price is required';
                $error_messages['ucret_alis_'.$oda_tip_id.".numeric"] = 'Room type buy price must be number';
                $rules['ucret_satis_'.$oda_tip_id] = "required|numeric";
                $error_messages['ucret_satis_'.$oda_tip_id.".required"] = 'Room type sell price is required';
                $error_messages['ucret_satis_'.$oda_tip_id.".numeric"] = 'Room type sell price must be number';
            }

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Oteller::findorfail($id)->update([
                'adi' => $request->input('adi'),
                'sehir_id' => $request->sehir_id,
                'bolge_id' => $request->bolge_id,
                'derece_id' => $request->derece_id,
                'web_adresi' => $request->web_adresi,
                'telefon' => $request->telefon,
                'email' => $request->email,
                'adres' => $request->adres,
                'koordinat_x' => $request->koordinat_x,
                'koordinat_y' => $request->koordinat_y,
                'ofise_uzaklik' => $request->ofise_uzaklik,
                'oda_sayisi' => $request->oda_sayisi,
                'toplanti_oda_sayisi' => $request->toplanti_oda_sayisi,
                'yarim_ucret' => $request->yarim_ucret,
                'tam_ucret' => $request->tam_ucret,
                'min_katilimci' => $request->min_katilimci,
                'toplanti_oda_aciklama' => $request->toplanti_oda_aciklama,
                'flg_fiyatsor' => $request->flg_fiyatsor,
            ]);
            foreach($request->oda_tip_id as $key => $oda_tip_id) {
                OtellerinOdaTipleri::updateorcreate([
                    'otel_id' => $id,
                    'oda_tip_id' => $oda_tip_id
                ], [
                    'otel_id' => $id,
                    'oda_tip_id' => $oda_tip_id,
                    'ucret_alis' => $request->input('ucret_alis_'.$oda_tip_id),
                    'ucret_satis' => $request->input('ucret_satis_'.$oda_tip_id),
                    'flg_na' => intval($request->input('flg_na_'.$oda_tip_id))
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
     * @param  \App\Http\Models\Oteller  $oteller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Oteller $oteller, $id)
    {
        if(!Auth::user()->isAllow('hrm_hl_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $oteller->destroy($id);
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

    public function bolgeleriGetirJson(Request $request) {
        $result = OtelBolgeleri::where('sehir_id', $request->sehir_id)
            ->wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->select('id', 'adi')
            ->get();
        return response()->json($result);
    }
    public function dereceGetirJson(Request $request) {
        $sql = Oteller::where('otl_oteller.sehir_id', $request->sehir_id)
            ->wherenull('otl_oteller.deleted_at')
            ->leftjoin('otl_derece', 'otl_derece.id', '=', 'otl_oteller.derece_id')
            ->orderby('otl_derece.sira', 'asc')
            ->groupby('otl_derece.id')
            ->select('otl_derece.id', 'otl_derece.adi');
        if($request->bolge_id > 0)
            $sql = $sql->where('otl_oteller.bolge_id', $request->bolge_id);
        $result = $sql->get();
        return response()->json($result);
    }

    public function search(Request $request) {
        $this->s_sehir_id = $request->sehir_id;
        $this->s_bolge_id = $request->bolge_id;
        $this->s_derece_id = $request->derece_id;
        return $this->index();
    }
}
