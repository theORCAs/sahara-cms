<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\ITIsler;
use Validator;
use Auth;

class ITIslerAtanmisController extends HomeController
{
    private $liste;
    private $alt_baslik;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'liste' => $this->liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => $this->alt_baslik
        ];
        return view('office_management.istakip.atananisler_view', $data);
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
        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Update My Job",

            'data' => ITIsler::where('it_isler.id', $id)
                ->leftjoin('it_isturleri', 'it_isturleri.id', '=', 'it_isler.isturu_id')
                ->leftjoin('it_kategori', 'it_kategori.id', '=', 'it_isturleri.kategori_id')
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'it_isler.istek_yapan')
                ->select('it_kategori.adi as kategori_adi', 'it_isturleri.adi as is_turu_adi', 'it_isler.*', 'kullanicilar.adi_soyadi as istek_yapan_adi')
                // ->select('it_isler.*')
                ->first()
        ];

        if(!Auth::user()->isAllow('jatm_jc_edit')) {
            if($data['data']->atanan_kisi != Auth::user()->id) {
                if($data['data']->ilgili_kisi != Auth::user()->id) {
                    return redirect()
                        ->back()
                        ->with('err_msj', config('messages.yetkiniz_yok'));
                }
            }
        }

        return view('office_management.istakip.atananisler_edit', $data);
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
        $result = $iTIsler::findorfail($id);
        if(!Auth::user()->isAllow('jatm_jc_edit')) {
            if($result->atanan_kisi != Auth::user()->id) {
                if($result->ilgili_kisi != Auth::user()->id) {
                    return redirect()
                        ->back()
                        ->with('err_msj', config('messages.yetkiniz_yok'));
                }
            }
        }
        try {
            $error_messages = array(
                'is_tanimi.required' => 'Additional Notes is required.',
            );
            $rules = array(
                'is_tanimi' => 'required',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $result->update([
                'is_tanimi' => $request->input('is_tanimi'),
                'durum' => $request->durum,
            ]);
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
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
    public function destroy(ITIsler $iTIsler)
    {
        //
    }

    public function bekleyenAtanmis() {
        $this->liste = ITIsler::wherenull('deleted_at')
            ->where('durum', 0)
            ->where('atanan_kisi', auth()->user()->id)
            //->where('atanan_kisi', 529)
            ->orderby('is_tarihi', 'asc')
            ->paginate(10);
        session(['PREFIX' => 'jatm_waiting']);
        $this->alt_baslik = "Job Waiting";

        return $this->index();
    }
    public function tamamlanmisAtanmis() {
        if(!Auth::user()->isAllow('jatm_jc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $this->liste = ITIsler::wherenull('deleted_at')
            ->where('durum', 1)
            ->where('atanan_kisi', auth()->user()->id)
            //->where('atanan_kisi', 529)
            ->orderby('is_tarihi', 'desc')
            ->paginate(10);
        session(['PREFIX' => 'jatm_completed']);
        $this->alt_baslik = "Job Completed";

        return $this->index();
    }
}
