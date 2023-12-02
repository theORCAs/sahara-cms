<?php

namespace App\Http\Controllers;

use App\Http\Models\EbultenGruplar;
use App\Http\Models\EbultenKayitlar;
use App\Http\Models\KullaniciRolleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EbultenGruplarController extends HomeController
{
    private $prefix = "em_emailgroup";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
        'sira.required' => 'Order number is required.',
        'sira.numeric' => 'The order number must be a number.',
    );
    private $rules = array(
        'adi' => 'required',
        'sira' => 'required|numeric',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('em_eg_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EbultenGruplar::wherenull('deleted_at')
            ->orderby('dinamik_grup_id', 'asc')
            ->orderby('sira', 'asc')
            ->paginate(100);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Email Groups"
        ];
        return view('ebulten.grup_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('em_eg_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Email Groups",
            'data' => new EbultenGruplar(),
            'dinamik_grup_liste' => EbultenGruplar::wherenotnull('dinamik_grup_id')->orderby('sira', 'asc')->select('dinamik_grup_id as id', 'adi')->get(),
            'yetkili_liste' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->where('kullanicilar.flg_durum', '1')
                ->get()
        ];
        return view('ebulten.grup_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('em_eg_add')) {
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

            EbultenGruplar::create([
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
                'dinamik_grup_id' => intval($request->dinamik_grup_id) > 0 ? $request->dinamik_grup_id : null,
                'yetkili_1' => intval($request->yetkili_1) > 0 ? $request->yetkili_1 : null,
                'yetkili_2' => intval($request->yetkili_2) > 0 ? $request->yetkili_2 : null,
                'yetkili_3' => intval($request->yetkili_3) > 0 ? $request->yetkili_3 : null,
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
     * @param  \App\Http\Models\EbultenGruplar  $ebultenGruplar
     * @return \Illuminate\Http\Response
     */
    public function show(EbultenGruplar $ebultenGruplar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EbultenGruplar  $ebultenGruplar
     * @return \Illuminate\Http\Response
     */
    public function edit(EbultenGruplar $ebultenGruplar, $id)
    {
        if(!Auth::user()->isAllow('em_eg_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Email Groups",
            'data' => $ebultenGruplar->findorfail($id),
            'dinamik_grup_liste' => EbultenGruplar::wherenotnull('dinamik_grup_id')->orderby('sira', 'asc')->select('dinamik_grup_id as id', 'adi')->get(),
            'yetkili_liste' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->where('kullanicilar.flg_durum', '1')
                ->get()
        ];
        return view('ebulten.grup_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EbultenGruplar  $ebultenGruplar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbultenGruplar $ebultenGruplar, $id)
    {
        if(!Auth::user()->isAllow('em_eg_edit')) {
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

            $ebultenGruplar->find($id)
                ->update([
                    'adi' => $request->input('adi'),
                    'sira' => $request->sira,
                    'dinamik_grup_id' => intval($request->dinamik_grup_id) > 0 ? $request->dinamik_grup_id : null,
                    'yetkili_1' => intval($request->yetkili_1) > 0 ? $request->yetkili_1 : null,
                    'yetkili_2' => intval($request->yetkili_2) > 0 ? $request->yetkili_2 : null,
                    'yetkili_3' => intval($request->yetkili_3) > 0 ? $request->yetkili_3 : null,
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
     * @param  \App\Http\Models\EbultenGruplar  $ebultenGruplar
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbultenGruplar $ebultenGruplar)
    {
        //
    }
}
