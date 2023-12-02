<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimKategori;
use App\Http\Models\EgitmenKursOneri;
use App\Http\Models\EgitmenKursOneriIcerik;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EgitmenKursOneriController extends HomeController
{
    private $error_messages = array(
        'kategori_id.required' => 'Suggested Course Category is required.',
        'adi.required' => 'Suggested Course Title is required.',
        'icerik.required' => 'Course Description is required.',
        'objective.required' => 'Course Objective is required.'
    );
    private $rules = array(
        'kategori_id' => 'required',
        'adi' => 'required',
        'icerik' => 'required',
        'objective' => 'required',
    );
    private $liste;
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
            'alt_baslik' => 'List Record'
        ];

        return view('kurs_oneri.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('im_pnc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'kategori_liste' => EgitimKategori::wherenull('deleted_at')->where('flg_aktif', 1)->orderby('sira', 'asc')->get(),
            'data' => new EgitmenKursOneri(),
            'icerik_data' => new EgitmenKursOneriIcerik(),
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Edit Record'
        ];
        return view('kurs_oneri.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('im_pnc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {

            foreach($request->kurs_icerik as $key => $icerik) {
                $gun = $key+1;
                if($gun > $request->kac_gun)
                    continue;

                $this->rules['kurs_icerik.'.$key] = 'required';
                $this->error_messages['kurs_icerik.'.$key.'.required'] = $gun.'. day topics is required.';
            }

            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $oneri_obj = EgitmenKursOneri::create([
                'kullanici_id' => Auth::user()->id,
                'kategori_id' => $request->kategori_id,
                'adi' => $request->input('adi'),
                'deneyim_aciklama' => $request->input('deneyim_aciklama'),
                'icerik' => $request->icerik,
                'objective' => $request->objective,
                'aciklama' => $request->aciklama,
                'attend' => $request->attend,
                'kac_gun' => $request->kac_gun,
                'durum' => 0
            ]);
            foreach($request->kurs_icerik as $key => $icerik) {
                $gun = $key+1;
                if($gun > $request->kac_gun)
                    continue;
                EgitmenKursOneriIcerik::create([
                    'oneri_egitim_id' => $oneri_obj->id,
                    'gun' => $gun,
                    'icerik' => $icerik
                ]);
            }
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\EgitmenKursOneri  $egitmenKursOneri
     * @return \Illuminate\Http\Response
     */
    public function show(EgitmenKursOneri $egitmenKursOneri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitmenKursOneri  $egitmenKursOneri
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitmenKursOneri $egitmenKursOneri, $id)
    {
        if(!Auth::user()->isAllow('im_pnc_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'kategori_liste' => EgitimKategori::wherenull('deleted_at')->where('flg_aktif', 1)->orderby('sira', 'asc')->get(),
            'data' => EgitmenKursOneri::find($id),
            'icerik_data' => EgitmenKursOneriIcerik::where('oneri_egitim_id', $id)->orderby('gun', 'asc')->get(),
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Edit Record'
        ];
        if(session('YETKILI') == '1')
            return view('kurs_oneri.admin.edit', $data);
        return view('kurs_oneri.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitmenKursOneri  $egitmenKursOneri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitmenKursOneri $egitmenKursOneri, $id)
    {
        if(!Auth::user()->isAllow('im_pnc_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            foreach($request->kurs_icerik as $key => $icerik) {
                $gun = $key+1;
                if($gun > $request->kac_gun)
                    continue;

                $this->rules['kurs_icerik.'.$key] = 'required';
                $this->error_messages['kurs_icerik.'.$key.'.required'] = $gun.'. day topics is required.';
            }

            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $eko = EgitmenKursOneri::find($id);
            $eko->update([
                'kategori_id' => $request->kategori_id,
                'adi' => $request->input('adi'),
                'deneyim_aciklama' => $request->input('deneyim_aciklama'),
                'icerik' => $request->icerik,
                'objective' => $request->objective,
                'aciklama' => $request->aciklama,
                'attend' => $request->attend,
                'kac_gun' => $request->kac_gun,
                'durum' => 0
            ]);
            if(session('YETKILI') == '1' && isset($request->durum)) {
                $eko->update(['durum' => intval($request->durum)]);
            }
            foreach($request->kurs_icerik as $key => $icerik) {
                $gun = $key+1;
                if($gun > $request->kac_gun)
                    continue;

                EgitmenKursOneriIcerik::updateorcreate(
                        [
                            'oneri_egitim_id' => $id,
                            'gun' => $gun
                        ],
                        [
                        'oneri_egitim_id' => $id,
                        'gun' => $gun,
                        'icerik' => $icerik
                    ]);

            }
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
     * @param  \App\Http\Models\EgitmenKursOneri  $egitmenKursOneri
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitmenKursOneri $egitmenKursOneri, $id)
    {
        if(!Auth::user()->isAllow('im_pnc_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $egitmenKursOneri->destroy($id);
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

    public function egitmenYeniKurs() {
        if(!Auth::user()->isAllow('im_pnc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $this->liste = EgitmenKursOneri::wherenull('deleted_at')
            ->where('kullanici_id', Auth::user()->id)
            ->where('durum', '!=', '4')
            ->paginate(10);

        session(['PREFIX' => 'wtpnc_view']);
        session(['YETKILI' => 0]);

        return $this->index();
    }

    public function yoneticiYeniKurs() {
        if(!Auth::user()->isAllow('im_os_np_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EgitmenKursOneri::wherenull('deleted_at')
            ->where('durum', '=', '0')
            ->orderby('id', 'asc')
            ->paginate(100);

        session(['PREFIX' => 'osnp_view']);
        session(['YETKILI' => 1]);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'New Proposal'
        ];

        return view('kurs_oneri.admin.view', $data);
    }

    public function yoneticiEditedKurs() {
        if(!Auth::user()->isAllow('im_os_em_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EgitmenKursOneri::wherenull('deleted_at')
            ->where('durum', '=', '2')
            ->orderby('updated_at', 'desc')
            ->paginate(100);

        session(['PREFIX' => 'osem_view']);
        session(['YETKILI' => 1]);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Edited/Modified'
        ];

        return view('kurs_oneri.admin.view', $data);
    }

    public function yoneticiKabulKurs() {
        if(!Auth::user()->isAllow('im_os_ap_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EgitmenKursOneri::wherenull('deleted_at')
            ->where('durum', '=', '1')
            ->orderby('updated_at', 'desc')
            ->paginate(100);

        session(['PREFIX' => 'osap_view']);
        session(['YETKILI' => 1]);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Accepted/Published'
        ];

        return view('kurs_oneri.admin.view', $data);
    }

    public function yoneticiPasifKurs() {
        if(!Auth::user()->isAllow('im_os_pu_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EgitmenKursOneri::wherenull('deleted_at')
            ->where('durum', '=', '4')
            ->orderby('id', 'desc')
            ->paginate(100);

        session(['PREFIX' => 'ospu_view']);
        session(['YETKILI' => 1]);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Passive/Unpublished'
        ];

        return view('kurs_oneri.admin.view', $data);
    }
}
