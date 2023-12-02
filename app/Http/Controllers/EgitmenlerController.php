<?php

namespace App\Http\Controllers;

use App\Http\Models\Diller;
use App\Http\Models\EgitimDegerlendirme;
use App\Http\Models\EgitimHocalar;
use App\Http\Models\EgitimKategori;
use App\Http\Models\Egitimler;
use App\Http\Models\EgitmenAldigiKurslar;
use App\Http\Models\EgitmenBackground;
use App\Http\Models\EgitmenDegerlendirme;
use App\Http\Models\EgitmenDiller;
use App\Http\Models\EgitmenEgitimKategori;
use App\Http\Models\EgitmenEkSecim;
use App\Http\Models\EgitmenIsler;
use App\Http\Models\Egitmenler;
use App\Http\Models\EgitmenlerBilgi;
use App\Http\Models\EgitmenOkullar;
use App\Http\Models\SendEmail;
use App\Http\Models\Ulkeler;
use App\Http\Models\Unvanlar;
use App\User;
use Illuminate\Http\Request;
use mysql_xdevapi\Session;
use Validator;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\EmailSablon;
use Illuminate\Support\Facades\DB;

class EgitmenlerController extends HomeController
{
    private $prefix = "cv_view";
    private $error_messages = array(
        'sifre_tekrar.required_with' => 'Password (repeat) is required.',
        'sifre_tekrar.same' => 'Password and Password (repeat) is not same. Please check.',
        'unvan_id.required' => 'Title is required.',
        'adi_soyadi.required' => 'Full Name is required.',
        'resim.mimes' => 'Image must be jpeg, bmp or png',
        'resim.max' => 'Image max 2mb',
        'yasadigi_ulke.required' => 'Country of Residence is required',
        'dogum_ulke.required' => 'Country of Origin is required',
        'sahsi_email.required' => 'Personal Email is required',
        'sahsi_email.email' => 'Personal Email must be valid.',
        'cep_tel_kod.required_with' => 'Mobile (GSM) code is required',
        'tel_kod.required_with' => 'Additional Telephone code is required',
        'cv_dosya.mimes' => 'CV1 (FreeFormat) must be doc docx or pdf',
        'cv_dosya.max' => 'CV1 (FreeFormat) file size max 2mb',
        'cv_dosya2.mimes' => 'CV2 (TemplateFormat) must be doc docx or pdf',
        'cv_dosya2.max' => 'CV2 (TemplateFormat) file size max 2mb',
        'pasaport_resim.mimes' => 'Passport Copy must be jpeg, bmp or png',
        'pasaport_resim.max' => 'Passport Copy file size max 2mb',
        'ikame_resim.mimes' => 'Residence (Ikame) copy must be jpeg, bmp or png',
        'ikame_resim.max' => 'Residence (Ikame) copy file size max 2mb',
    );
    private $rules = array(
        'sifre_tekrar' => 'required_with:sifre|same:sifre',
        'unvan_id' => 'required',
        'adi_soyadi' => 'required',
        'resim' => 'sometimes|nullable|mimes:jpeg,bmp,png|max:2048',
        'yasadigi_ulke' => 'required',
        'dogum_ulke' => 'required',
        'sahsi_email' => 'required|email',
        'cep_tel_kod' => 'required_with:cep_tel',
        'tel_kod' => 'required_with:tel',
        'cv_dosya' => 'sometimes|nullable|mimes:doc,docx,pdf|max:2048',
        'cv_dosya2' => 'sometimes|nullable|mimes:doc,docx,pdf|max:2048',
        'pasaport_resim' => 'sometimes|nullable|mimes:jpeg,bmp,png|max:2048',
        'ikame_resim' => 'sometimes|nullable|mimes:jpeg,bmp,png|max:2048',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('im_cv_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $data = Egitmenler::where('kullanici_id', Auth::user()->id)
            ->first();

        $sectigi_kategori_arr = array();
        foreach($data->sectigiKategoriler as $row) {
            array_push($sectigi_kategori_arr, $row->id);
        }
        $sectigi_egitimler_arr = array();
        foreach($data->sectigiEgitimler as $row) {
            array_push($sectigi_egitimler_arr, $row->id);
        }
        session(['PREFIX' => $this->prefix]);
        $data = [
            'data' => $data,
            'prefix' => $this->prefix,
            'baslik' => 'CV and Personal Info (please UPDATE!)',
            'alt_baslik' => "-",
            'unvanlar' => Unvanlar::orderby('adi')->get(),
            'ulkeler' => Ulkeler::where('flg_aktif', 1)->orderby('adi', 'asc')->select('id', 'adi')->get(),
            'diller' => Diller::select('id', 'adi')->orderby('adi', 'asc')->get(),
            'egitmen_diller' => EgitmenDiller::where('egitmen_id', $data->id)->select('id', 'dil_id', 'derece')->orderby('id', 'asc')->get(),
            'egitim_kategorileri' => EgitimKategori::orderby('adi')->get(),
            'sectigi_kategoriler' => $sectigi_kategori_arr,
            'egitimler_listesi' => Egitimler::leftjoin('egitim_kategori', 'egitim_kategori.id', '=', 'egitimler.kategori_id')
                ->select('egitim_kategori.id as kategori_id', 'egitim_kategori.adi as kategori_adi', 'egitimler.id', 'egitimler.adi', 'egitimler.kodu')
                ->orderby('egitim_kategori.sira', 'asc')
                ->orderby('egitimler.adi', 'asc')
                ->get(),
            'sectigi_egitimler' => $sectigi_egitimler_arr,
        ];
        return view('egitmen.cv_edit', $data);
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

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\Egitmenler  $egitmenler
     * @return \Illuminate\Http\Response
     */
    public function show(Egitmenler $egitmenler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Egitmenler  $egitmenler
     * @return \Illuminate\Http\Response
     */
    public function edit(Egitmenler $egitmenler, $id)
    {
        $data = Egitmenler::find($id);

        $sectigi_kategori_arr = array();
        foreach($data->sectigiKategoriler as $row) {
            array_push($sectigi_kategori_arr, $row->id);
        }
        $sectigi_egitimler_arr = array();
        foreach($data->sectigiEgitimler as $row) {
            array_push($sectigi_egitimler_arr, $row->id);
        }
        $data = [
            'data' => $data,
            'prefix' => session('PREFIX'),
            'baslik' => 'Instructor Update',
            'alt_baslik' => "-",
            'unvanlar' => Unvanlar::orderby('adi')->get(),
            'ulkeler' => Ulkeler::where('flg_aktif', 1)->orderby('adi', 'asc')->select('id', 'adi')->get(),
            'diller' => Diller::select('id', 'adi')->orderby('adi', 'asc')->get(),
            'egitmen_diller' => EgitmenDiller::where('egitmen_id', $data->id)->select('id', 'dil_id', 'derece')->orderby('id', 'asc')->get(),
            'egitim_kategorileri' => EgitimKategori::orderby('adi')->get(),
            'sectigi_kategoriler' => $sectigi_kategori_arr,
            'egitimler_listesi' => Egitimler::leftjoin('egitim_kategori', 'egitim_kategori.id', '=', 'egitimler.kategori_id')
                ->select('egitim_kategori.id as kategori_id', 'egitim_kategori.adi as kategori_adi', 'egitimler.id', 'egitimler.adi', 'egitimler.kodu')
                ->orderby('egitim_kategori.sira', 'asc')
                ->orderby('egitimler.adi', 'asc')
                ->get(),
            'sectigi_egitimler' => $sectigi_egitimler_arr,
            'egitmen_formu' => "1"
        ];
        return view('egitmen.cv_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Egitmenler  $egitmenler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Egitmenler $egitmenler, $id)
    {
        if(!Auth::user()->isAllow('im_cv_view')) {
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

            $kayit = $egitmenler->findorfail($id);
            if(isset($request->sifre) && $kayit->kullanici_id > 0) {
                User::findorfail($kayit->kullanici_id)->update(['sifre' => md5($request->sifre)]);
            }
            if(isset($request->resim)) {
                $kayit->update([
                    'resim' => $request->file("resim")->store("public/egitmen_resim")
                ]);
            }
            if(isset($request->cv_dosya)) {
                $kayit->update([
                    'cv_dosya' => $request->file("cv_dosya")->store("public/egitmen_cv"),
                    'cv_dosya_tarih' => date('Y-m-d H:i:s')
                ]);
            }
            if(isset($request->cv_dosya2)) {
                $kayit->update([
                    'cv_dosya2' => $request->file("cv_dosya2")->store("public/egitmen_cv"),
                    'cv_dosya2_tarih' => date('Y-m-d H:i:s')
                ]);
            }
            if(isset($request->pasaport_resim)) {
                $kayit->update([
                    'pasaport_resim' => $request->file("pasaport_resim")->store("public/egitmen_pasaport")
                ]);
            }
            if(isset($request->ikame_resim)) {
                $kayit->update([
                    'ikame_resim' => $request->file("ikame_resim")->store("public/egitmen_pasaport")
                ]);
            }
            $kayit->update([
                'unvan_id' => $request->input('unvan_id'),
                'adi_soyadi' => $request->adi_soyadi,
                'cinsiyet' => intval($request->cinsiyet),
                'medeni_durum' => intval($request->medeni_durum),
                'dogum_tarihi' => ($request->dogum_tarihi != '' ? date('Y-m-d', strtotime($request->dogum_tarihi)) : null),
                'sirket_adi' => $request->sirket_adi,
                'yasadigi_ulke' => intval($request->yasadigi_ulke) > 0 ? $request->yasadigi_ulke : null,
                'dogum_ulke' => intval($request->dogum_ulke) > 0 ? $request->dogum_ulke : null,
                'yasadigi_sehir' => $request->yasadigi_sehir,
                'sahsi_email' => $request->sahsi_email,
                'sirket_email' => $request->sirket_email,
                'cep_tel_kod' => $request->cep_tel_kod,
                'cep_tel' => $request->cep_tel,
                'tel' => $request->tel,
                'tc_kimlik' => $request->tc_kimlik,
                'pasaport_no' => $request->pasaport_no,
                'banka_hesap_adi' => $request->banka_hesap_adi,
                'banka_adi' => $request->banka_adi,
                'banka_sube' => $request->banka_sube,
                'banka_hesap_no' => $request->banka_hesap_no,
                'banka_iban' => $request->banka_iban,
                'course_additional' => $request->course_additional,
                'papers' => $request->papers,
                'software' => $request->software,
                'referanslar' => $request->referanslar,
                'other_info' => $request->other_info,
            ]);

            if(!empty($request->diller)) {
                EgitmenDiller::where('egitmen_id', $kayit->id)->wherenotin('dil_id', [implode(',', $request->diller)])->select('id')->delete();
                for($i = 0; $i < 4; $i++) {
                    if($request->diller[$i] != '') {
                        EgitmenDiller::updateOrCreate([
                            'egitmen_id' => $kayit->id,
                            'dil_id' => $request->diller[$i],
                            'derece' => $request->derece[$i]
                        ]);
                    }
                }
            }

            foreach($request->hid_eo_id as $key => $eo_id) {
                if($eo_id > 0) {
                    EgitmenOkullar::find($eo_id)->update([
                        'okul' => $request->eo_okul[$key],
                        'uzmanlik' => $request->eo_uzmanlik[$key],
                        'sehir' => $request->eo_sehir[$key],
                        'ulke_id' => $request->eo_ulke_id[$key] > 0 ? $request->eo_ulke_id[$key] : null,
                        'mezun_tarih' => $request->eo_mezun_tarih[$key]."-01-01",
                        'derece' => $request->eo_derece[$key]
                    ]);
                } else {
                    if(trim($request->eo_uzmanlik[$key]) != "") {
                        EgitmenOkullar::create([
                            'egitmen_id' => $kayit->id,
                            'okul' => $request->eo_okul[$key],
                            'uzmanlik' => $request->eo_uzmanlik[$key],
                            'sehir' => $request->eo_sehir[$key],
                            'ulke_id' => $request->eo_ulke_id[$key] > 0 ? $request->eo_ulke_id[$key] : null,
                            'mezun_tarih' => $request->eo_mezun_tarih[$key] . "-01-01",
                            'derece' => $request->eo_derece[$key]
                        ]);
                    }
                }
            }

            foreach($request->hid_ei_id as $key => $ei_id) {
                if($ei_id > 0) {
                    EgitmenIsler::find($ei_id)->update([
                        'baslama_tarihi' => $request->ei_baslama_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ei_baslama_tarihi[$key])) : null,
                        'bitis_tarihi' => $request->ei_bitis_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ei_bitis_tarihi[$key])) : null,
                        'sirket_adi' => $request->ei_sirket_adi[$key],
                        'departman' => $request->ei_departman[$key],
                        'pozisyon' => $request->ei_pozisyon[$key],
                        'sehir' => $request->ei_sehir[$key],
                        'ulke_id' => $request->ei_ulke_id[$key] > 0 ? $request->ei_ulke_id[$key] : null,
                    ]);
                } else {
                    if(trim($request->ei_departman[$key]) != "") {
                        EgitmenIsler::create([
                            'egitmen_id' => $kayit->id,
                            'baslama_tarihi' => $request->ei_baslama_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ei_baslama_tarihi[$key])) : null,
                            'bitis_tarihi' => $request->ei_bitis_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ei_bitis_tarihi[$key])) : null,
                            'sirket_adi' => $request->ei_sirket_adi[$key],
                            'departman' => $request->ei_departman[$key],
                            'pozisyon' => $request->ei_pozisyon[$key],
                            'sehir' => $request->ei_sehir[$key],
                            'ulke_id' => $request->ei_ulke_id[$key] > 0 ? $request->ei_ulke_id[$key] : null,
                        ]);
                    }
                }

                if(isset($request->my_multi_select1)) {
                    $olan_ek_id = array();
                    foreach($request->my_multi_select1 as $key => $ek_id) {
                        if(!in_array($ek_id, $olan_ek_id))
                            array_push($olan_ek_id, $ek_id);
                        EgitmenEgitimKategori::updateorcreate([
                            'egitmen_id' => $kayit->id,
                            'egitim_kategori_id' => $ek_id
                        ]);
                    }
                    EgitmenEgitimKategori::where('egitmen_id', $kayit->id)->wherenotin('egitim_kategori_id', $olan_ek_id)->delete();
                } else {
                    EgitmenEgitimKategori::where('egitmen_id', $kayit->id)->delete();
                }

                if(isset($request->my_multi_select2)) {
                    $olan_egitim_id = array();
                    foreach($request->my_multi_select2 as $key => $egitim_id) {
                        if(!in_array($egitim_id, $olan_egitim_id))
                            array_push($olan_egitim_id, $egitim_id);

                        EgitmenEkSecim::updateorcreate([
                            'egitmen_id' => $kayit->id,
                            'egitim_id' => $egitim_id
                        ]);
                    }
                    EgitmenEkSecim::where('egitmen_id', $kayit->id)->wherenotin('egitim_id', $olan_egitim_id)->delete();
                } else {
                    EgitmenEkSecim::where('egitmen_id', $kayit->id)->delete();
                }

            }
            foreach($request->hid_ek_id as $key => $ek_id) {
                if($ek_id > 0) {
                    EgitmenAldigiKurslar::find($ek_id)->update([
                        'baslama_tarihi' => $request->ek_baslama_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ek_baslama_tarihi[$key])) : null,
                        'bitis_tarihi' => $request->ek_bitis_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ek_bitis_tarihi[$key])) : null,
                        'kurs_adi' => $request->ek_kurs_adi[$key],
                        'kurum' => $request->ek_kurum[$key],
                        'sehir' => $request->ek_sehir[$key],
                        'ulke_id' => $request->ek_ulke_id[$key] > 0 ? $request->ek_ulke_id[$key] : null,
                    ]);
                } else {
                    if(trim($request->ek_kurs_adi[$key]) != "") {
                        EgitmenAldigiKurslar::create([
                            'egitmen_id' => $kayit->id,
                            'baslama_tarihi' => $request->ek_baslama_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ek_baslama_tarihi[$key])) : null,
                            'bitis_tarihi' => $request->ek_bitis_tarihi[$key] != '' ? date('Y-m-d', strtotime($request->ek_bitis_tarihi[$key])) : null,
                            'kurs_adi' => $request->ek_kurs_adi[$key],
                            'kurum' => $request->ek_kurum[$key],
                            'sehir' => $request->ek_sehir[$key],
                            'ulke_id' => $request->ek_ulke_id[$key] > 0 ? $request->ek_ulke_id[$key] : null,
                        ]);
                    }
                }
            }

            if(isset($request->durum)) {
                $kayit->update([
                    'durum' => $request->durum
                ]);
                if($request->durum == 3 && $kayit->kullanici_id == '') {
                    if($request->sifre != '')
                        $sifre = md5($request->sifre);
                    else
                        $sifre = md5(rand(100000, 999999));

                    $kullanici = User::create([
                        'adi_soyadi' => $request->adi_soyadi,
                        'email' => $request->sahsi_email,
                        'flg_durum' => 1,
                        'sifre' => $sifre
                    ]);

                    $kayit->update([
                        'kullanici_id' => $kullanici->id
                    ]);
                }
                if(intval($kayit->kullanici_id) > 0) {
                    if(isset($request->flg_durum)) {

                        User::find($kayit->kullanici_id)->update([
                            'flg_durum' => $request->flg_durum
                        ]);

                        if($request->sifre != '') {
                            User::find($kayit->kullanici_id)->update([
                                'sifre' => md5($request->sifre)
                            ]);
                        }

                    }
                }
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
     * @param  \App\Http\Models\Egitmenler  $egitmenler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Egitmenler $egitmenler)
    {
        //
    }

    public function cvDosyaSil(Request $request) {
        if(!Auth::user()->isAllow('im_cv_view')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            $alan_adi = $request->alan;
            $data = Egitmenler::find($request->id);
            Storage::delete($data->$alan_adi);
            $data->update([
                $alan_adi => null,
                $alan_adi."_tarih" => null
            ]);
            return response()->json([
                'cvp' => 1,
                'msj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()
            ]);
        }
    }

    public function hocaEgitimSil(Request $request) {
        if(!Auth::user()->isAllow('im_cv_view')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            if($request->id > 0) {
                EgitmenOkullar::destroy($request->id);
            }
            return response()->json([
                'cvp' => 1,
                'msj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()
            ]);
        }
    }

    public function calistigiYerSil(Request $request) {
        if(!Auth::user()->isAllow('im_cv_view')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            if($request->id > 0) {
                EgitmenIsler::destroy($request->id);
            }
            return response()->json([
                'cvp' => 1,
                'msj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()
            ]);
        }
    }

    public function aldigiKursSil(Request $request) {
        if(!Auth::user()->isAllow('im_cv_view')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            if($request->id > 0) {
                EgitmenAldigiKurslar::destroy($request->id);
            }
            return response()->json([
                'cvp' => 1,
                'msj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()
            ]);
        }
    }

    public function cosp_onerdigiKurslar() {

        if(!Auth::user()->isAllow('im_cos_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        if(session('ROL_ID') == 3) {
            $liste = Egitimler::wherenotnull('egitimler.teklif_eden_kisi')
                ->leftjoin('egitim_kategori', 'egitim_kategori.id', '=', 'egitimler.kategori_id')
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitimler.teklif_eden_kisi')
                ->orderby('egitim_kategori.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->orderby('egitimler.created_at', 'desc')
                ->paginate(100);
        } else {
            $liste = Egitimler::wherenull('deleted_at')
                ->where('teklif_eden_kisi', Auth::user()->id)
                ->paginate(100);
        }

        $data = [
            'liste' => $liste,
            'prefix' => 'cosp_view',
            'alt_baslik' => '--'
        ];

        return view('egitmen.onerdigi_kurslar', $data);
    }

    public function aktif() {
        if(!Auth::user()->isAllow('iap_active_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session([
            'PREFIX' => 'ia_active',
            'EGITMEN_DURUM' => 3
        ]);

        $result = Egitmenler::where('egitmenler.durum', '3')
            ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitmenler.kullanici_id')
            ->where('kullanicilar.flg_durum', 1)
            ->orderBy('egitmenler.id', 'desc')
            ->select('egitmenler.*');

        $filtre_yil = $this->filtreOlustur($result);

        $liste = $result->paginate(100);

        $data = [
            'filtre_yil' => $filtre_yil,
            'liste' => $liste,
            'prefix' => \session('PREFIX'),
            'alt_baslik' => 'Active',
            'uti_filtre_yil' => session('UTI_FILTRE_YIL'),
            'filtre_ulke_id' => session('UTI_FILTRE_ULKE_ID'),
            'filtre_dil_id' => session('UTI_FILTRE_DIL_ID'),
            'filtre_kategori_id' => session('UTI_FILTRE_KATEGORI_ID'),
            'filtre_egitim_id' => session('UTI_FILTRE_EGITIM_ID'),
            'filtre_egitmen_id' => session('UTI_FILTRE_EGITMEN_ID'),
        ];
        return view('egitmen.list', $data);
    }

    public function bekleyen() {
        if(!Auth::user()->isAllow('iap_request_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session([
            'PREFIX' => 'ia_request',
            'EGITMEN_DURUM' => 1
        ]);

        $result = Egitmenler::where('durum', 1)
            ->orderBy('id', 'desc');

        $filtre_yil = $this->filtreOlustur($result);

        $liste = $result->paginate(100);

        $data = [
            'filtre_yil' => $filtre_yil,
            'liste' => $liste,
            'prefix' => \session('PREFIX'),
            'alt_baslik' => 'New Request',
            'uti_filtre_yil' => session('UTI_FILTRE_YIL'),
            'filtre_ulke_id' => session('UTI_FILTRE_ULKE_ID'),
            'filtre_dil_id' => session('UTI_FILTRE_DIL_ID'),
            'filtre_kategori_id' => session('UTI_FILTRE_KATEGORI_ID'),
            'filtre_egitim_id' => session('UTI_FILTRE_EGITIM_ID'),
            'filtre_egitmen_id' => session('UTI_FILTRE_EGITMEN_ID'),
        ];

        return view('egitmen.list', $data);
    }

    public function atanmis() {
        if(!Auth::user()->isAllow('iap_util_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session([
            'PREFIX' => 'ia_utilized',
            'EGITMEN_DURUM' => 3
        ]);

        $result = Egitmenler::wherenull('egitmenler.deleted_at')
            ->where('durum', 3)
            ->has('verdigiKurslar', '>', 0)
            ->orderBy('egitmenler.id', 'desc')
            ;

        $filtre_yil = $this->filtreOlustur($result);

        $liste = $result->paginate(100);

        $data = [
            'filtre_yil' => $filtre_yil,
            'liste' => $liste,
            'prefix' => \session('PREFIX'),
            'alt_baslik' => 'Utilized',
            'uti_filtre_yil' => session('UTI_FILTRE_YIL'),
            'filtre_ulke_id' => session('UTI_FILTRE_ULKE_ID'),
            'filtre_dil_id' => session('UTI_FILTRE_DIL_ID'),
            'filtre_kategori_id' => session('UTI_FILTRE_KATEGORI_ID'),
            'filtre_egitim_id' => session('UTI_FILTRE_EGITIM_ID'),
            'filtre_egitmen_id' => session('UTI_FILTRE_EGITMEN_ID'),
        ];
        return view('egitmen.list', $data);
    }

    public function atanmamis() {
        if(!Auth::user()->isAllow('iap_nonutil_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session([
            'PREFIX' => 'ia_nonutilized',
            'EGITMEN_DURUM' => 3
        ]);

        $result = Egitmenler::wherenull('egitmenler.deleted_at')
            ->where('durum', 3)
            ->has('verdigiKurslar', '=', 0)
            ->orderBy('egitmenler.id', 'desc');

        $filtre_yil = $this->filtreOlustur($result);

        $liste = $result->paginate(100);

        $data = [
            'filtre_yil' => $filtre_yil,
            'liste' => $liste,
            'prefix' => \session('PREFIX'),
            'alt_baslik' => 'Non Utilized',
            'uti_filtre_yil' => session('UTI_FILTRE_YIL'),
            'filtre_ulke_id' => session('UTI_FILTRE_ULKE_ID'),
            'filtre_dil_id' => session('UTI_FILTRE_DIL_ID'),
            'filtre_kategori_id' => session('UTI_FILTRE_KATEGORI_ID'),
            'filtre_egitim_id' => session('UTI_FILTRE_EGITIM_ID'),
            'filtre_egitmen_id' => session('UTI_FILTRE_EGITMEN_ID'),
        ];

        return view('egitmen.list', $data);
    }

    public function reddedilmis() {
        if(!Auth::user()->isAllow('iap_reject_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $result = Egitmenler::wherenull('egitmenler.deleted_at')
            ->where('durum', 2)
            ->orderBy('egitmenler.id', 'desc')
            ->paginate(100);

        session([
            'PREFIX' => 'ia_rejected',
            'EGITMEN_DURUM' => 2
        ]);

        $data = [
            'liste' => $result,
            'prefix' => 'ia_rejected',
            'alt_baslik' => 'Rejected'
        ];
        return view('egitmen.list', $data);
    }

    public function pasif() {
        if(!Auth::user()->isAllow('iap_passive_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $result = Egitmenler::wherenull('egitmenler.deleted_at')
            ->whereHas('kullaniciBilgi', function ($query) {
                $query->where('flg_durum', '0');
            })
            ->orderBy('egitmenler.id', 'desc')
            ->paginate(100);

        session(['PREFIX' => 'ia_passive']);

        $data = [
            'liste' => $result,
            'prefix' => 'ia_passive',
            'alt_baslik' => 'Passive'
        ];
        return view('egitmen.list', $data);
    }

    public function egitmen_degerlendirme(Request $request) {
        if(!Auth::user()->isAllow('iap_eval_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        if(intval($request->filtre_hoca_id) > 0)
            session(['FILTRE_HOCA_ID' => $request->filtre_hoca_id]);
        elseif(intval($request->filtre_hoca_id) < 0)
            session()->forget('FILTRE_HOCA_ID');

        if(intval($request->filtre_yil) > 0)
            session(['FILTRE_YIL' => $request->filtre_yil]);
        elseif(intval($request->filtre_yil) < 0)
            session()->forget('FILTRE_YIL');

        $result = EgitimHocalar::wherenull('egitim_hocalar.deleted_at')
            ->has('egitmenDegerlendirme', '>', 0)
            ->select('egitim_hocalar.*')
            ->orderby('egitim_hocalar.id', 'desc');

        if(session('FILTRE_HOCA_ID')) {
            $result->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitim_hocalar.hoca_id')
                ->where('kullanicilar.id', session('FILTRE_HOCA_ID'));
        }
        if(session('FILTRE_YIL')) {
            $result->leftjoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'egitim_hocalar.egitim_kayit_id')
                ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
                ->whereraw('year(egitim_tarihleri.baslama_tarihi) = '.session('FILTRE_YIL'));
        }

        $data = [
            'liste' => $result->paginate(100),
            'egitmen_lişte' => EgitmenDegerlendirme::leftjoin('egitim_hocalar', 'egitim_hocalar.id', '=', 'egitmen_degerlendirme.egitim_hoca_id')
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitim_hocalar.hoca_id')
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi')
                ->selectraw('count(distinct egitmen_degerlendirme.egitim_hoca_id) as sayisi')
                ->orderby('kullanicilar.adi_soyadi')
                ->groupby('kullanicilar.id')
                ->get(),
            'yil_liste' => EgitmenDegerlendirme::leftjoin('egitim_hocalar', 'egitim_hocalar.id', '=', 'egitmen_degerlendirme.egitim_hoca_id')
                ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'egitim_hocalar.egitim_kayit_id')
                ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
                ->groupby(DB::raw('year(egitim_tarihleri.baslama_tarihi)'))
                ->selectraw('year(egitim_tarihleri.baslama_tarihi) as yil')
                ->selectraw('count(distinct egitim_hocalar.hoca_id) as sayisi')
                ->orderby('yil', 'desc')
                ->get()
        ];
        return view('egitmen.degerlendirme_view', $data);

    }

    public function personelEmailView($prefix, $egitmen_id) {
        $sablon = EmailSablon::find(40);
        $egitmen = Egitmenler::find($egitmen_id);

        $data = [
            'hid_egitmen_id' => $egitmen->id,
            'data' => [
                'from_email' => $sablon->alan4,
                'to_email' => $egitmen->sahsi_email,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{BASLAMA_TARIH}'
                ], [
                    '',
                    ''
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{HOCA_ADI}',
                        '{EGITIM_ADI}',
                        '{BASLAMA_TARIH}',
                    ],
                    [
                        $egitmen->adi_soyadi,
                        '',
                        '',
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX'),
            'guncellenecek_alan' => 'sahsi_mail_gon_tarih'
        ];
        return view('egitmen.personel_mail_view', $data);
    }

    public function personelEmailSend(Request $request) {
        try {
            $ekler1 = $ekler2 = "";
            if($request->file("ekler1"))
                $ekler1 = $request->file("ekler1")->store("public/mail_ekler");
            if($request->file("ekler2"))
                $ekler2 = $request->file("ekler2")->store("public/mail_ekler");
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != '' ? "," : "").$ekler2);

            SendEmail::create([
                'oncelik' => 5,
                'konu' => $request->konu,
                'from_email' => $request->from_email,
                'to_email' => $request->to_email,
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'icerik' => $request->icerik,
                'ekler' => $ekler
            ]);

            EgitmenlerBilgi::updateorcreate(
                ['egitmen_id' => $request->hid_egitmen_id],
                [
                    $request->guncellenecek_alan => date('Y-m-d H:i:s')
                ]
            );

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function corporateEmailView($prefix, $egitmen_id) {
        $sablon = EmailSablon::find(40);
        $egitmen = Egitmenler::find($egitmen_id);

        $data = [
            'hid_egitmen_id' => $egitmen->id,
            'data' => [
                'from_email' => $sablon->alan4,
                'to_email' => $egitmen->sahsi_email,
                'konu' => str_replace([
                    '{EGITIM_ADI}',
                    '{BASLAMA_TARIH}'
                ], [
                    '',
                    ''
                ], $sablon->alan1),
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{HOCA_ADI}',
                        '{EGITIM_ADI}',
                        '{BASLAMA_TARIH}',
                    ],
                    [
                        $egitmen->adi_soyadi,
                        '',
                        '',
                    ],
                    $sablon->alan2
                )
            ],
            'alt_baslik' => '--',
            'prefix' => session('PREFIX'),
            'guncellenecek_alan' => 'sirket_mail_gon_tarih'
        ];
        return view('egitmen.personel_mail_view', $data);
    }

    public function filtreUlkeGetirJSON(Request $request) {

        $query = Egitmenler::wherenull('egitmenler.deleted_at')
            ->where('durum', session('EGITMEN_DURUM'))
            ->leftjoin('ulkeler', 'ulkeler.id', '=', 'egitmenler.yasadigi_ulke')
            ->groupby('ulkeler.id')
            ->orderby('ulkeler.adi', 'asc')
            ->select('ulkeler.id', 'ulkeler.adi')
            ->wherenotnull('ulkeler.id')
            ->selectraw('count(egitmenler.id) as sayi');
        if($request->prefix == "ia_utilized") {
            $query->has('verdigiKurslar', '>', 0);
        } else if($request->prefix == "ia_nonutilized") {
            $query->has('verdigiKurslar', '=', 0);
        }
        if(!empty($request->filtre_yil)) {
            $query->whereraw('year(egitmenler.created_at) = ?', $request->filtre_yil);
        }
        return response()->json($query->get());
    }

    public function filtreDilGetirJSON(Request $request) {
        $query = Egitmenler::wherenull('egitmenler.deleted_at')
            ->wherenotnull('diller.id')
            ->where('durum', session('EGITMEN_DURUM'))
            ->leftjoin('egitmen_diller', 'egitmen_diller.egitmen_id', '=', 'egitmenler.id')
            ->leftjoin('diller', 'egitmen_diller.dil_id', '=', 'diller.id')
            ->groupby('diller.id')
            ->orderby('diller.adi', 'asc')
            ->select('diller.id', 'diller.adi')
            ->selectraw('count(egitmenler.id) as sayi');
        if($request->prefix == "ia_utilized") {
            $query->has('verdigiKurslar', '>', 0);
        } else if($request->prefix == "ia_nonutilized") {
            $query->has('verdigiKurslar', '=', 0);
        }
        if(!empty($request->filtre_yil)) {
            $query->whereraw('year(egitmenler.created_at) = ?', $request->filtre_yil);
        }
        if(!empty($request->filtre_ulke_id)) {
            $query->where('egitmenler.yasadigi_ulke', $request->filtre_ulke_id);
        }
        return response()->json($query->get());
    }

    public function filtreEgitimKategoriGetirJSON(Request $request) {
        $stmt = "select egitim_kategori.id, egitim_kategori.adi, count(distinct egitmenler.id) sayi
            from egitmenler
                left join egitmen_diller on egitmen_diller.egitmen_id = egitmenler.id
                left join egitmen_egitimkategori on egitmen_egitimkategori.egitmen_id = egitmenler.id
                left join egitim_kategori on egitim_kategori.id = egitmen_egitimkategori.egitim_kategori_id
            where egitmenler.deleted_at is null
                and egitmenler.durum = ".session('EGITMEN_DURUM')."
                ".($request->prefix == "ia_utilized" ? "and (select count(1) from egitim_hocalar where egitim_hocalar.hoca_id = egitmenler.kullanici_id) > 0" : "")."
                ".($request->prefix == "ia_nonutilized" ? "and (select count(1) from egitim_hocalar where egitim_hocalar.hoca_id = egitmenler.kullanici_id) = 0" : "")."
                and egitim_kategori.id is not null
                ".(!empty($request->filtre_yil) ? "and year(egitmenler.created_at) = ".$request->filtre_yil : "")."
                ".(!empty($request->filtre_ulke_id) ? "and egitmenler.yasadigi_ulke = ".$request->filtre_ulke_id : "")."
                ".(!empty($request->filtre_dil_id) ? "and egitmen_diller.dil_id = ".$request->filtre_dil_id : "")."
            group by egitim_kategori.id
            order by egitim_kategori.adi";
        return response()->json(DB::select($stmt));
    }

    public function filtreEgitimGetirJSON(Request $request) {
        $stmt = "select egitimler.id, egitimler.adi, count(distinct egitmenler.id) sayi
            from egitmenler
                left join egitmen_diller on egitmen_diller.egitmen_id = egitmenler.id
                left join egitmen_egitimkategori on egitmen_egitimkategori.egitmen_id = egitmenler.id
                left join egitimler on egitimler.kategori_id = egitmen_egitimkategori.egitim_kategori_id
            where egitmenler.deleted_at is null
                and egitmenler.durum = ".session('EGITMEN_DURUM')."
                ".($request->prefix == "ia_utilized" ? "and (select count(1) from egitim_hocalar where egitim_hocalar.hoca_id = egitmenler.kullanici_id) > 0" : "")."
                ".($request->prefix == "ia_nonutilized" ? "and (select count(1) from egitim_hocalar where egitim_hocalar.hoca_id = egitmenler.kullanici_id) = 0" : "")."
                and egitimler.id is not null
                ".(!empty($request->filtre_yil) ? "and year(egitmenler.created_at) = ".$request->filtre_yil : "")."
                ".(!empty($request->filtre_ulke_id) ? "and egitmenler.yasadigi_ulke = ".$request->filtre_ulke_id : "")."
                ".(!empty($request->filtre_dil_id) ? "and egitmen_diller.dil_id = ".$request->filtre_dil_id : "")."
                ".(!empty($request->filtre_kategori_id) ? "and egitmen_egitimkategori.egitim_kategori_id = ".$request->filtre_kategori_id : "")."
            group by egitimler.id
            order by egitimler.adi";
        return response()->json(DB::select($stmt));
    }
    public function filtreHocaAdiGetirJson(Request $request) {
        $stmt = "select egitmenler.id, egitmenler.adi_soyadi adi, count(distinct egitmenler.id) say
            from egitmenler
                left join egitmen_diller on egitmen_diller.egitmen_id = egitmenler.id
                left join egitmen_egitimkategori on egitmen_egitimkategori.egitmen_id = egitmenler.id
                left join egitimler on egitimler.kategori_id = egitmen_egitimkategori.egitim_kategori_id
            where egitmenler.deleted_at is null
                and egitmenler.durum = ".session('EGITMEN_DURUM')."
                ".($request->prefix == "ia_utilized" ? "and (select count(1) from egitim_hocalar where egitim_hocalar.hoca_id = egitmenler.kullanici_id) > 0" : "")."
                ".($request->prefix == "ia_nonutilized" ? "and (select count(1) from egitim_hocalar where egitim_hocalar.hoca_id = egitmenler.kullanici_id) = 0" : "")."
                ".(!empty($request->filtre_yil) ? "and year(egitmenler.created_at) = ".$request->filtre_yil : "")."
                ".(!empty($request->filtre_ulke_id) ? "and egitmenler.yasadigi_ulke = ".$request->filtre_ulke_id : "")."
                ".(!empty($request->filtre_dil_id) ? "and egitmen_diller.dil_id = ".$request->filtre_dil_id : "")."
                ".(!empty($request->filtre_kategori_id) ? "and egitmen_egitimkategori.egitim_kategori_id = ".$request->filtre_kategori_id : "")."
                ".(!empty($request->filtre_egitim_id) ? "and egitimler.id = ".$request->filtre_egitim_id : "")."
            group by egitmenler.id
            order by egitmenler.adi_soyadi";
        return response()->json(DB::select($stmt));
    }

    public function search(Request $request) {
        session(['UTI_FILTRE_YIL' => $request->filtre_yil]);
        session(['UTI_FILTRE_ULKE_ID' => $request->filtre_ulke_id]);
        session(['UTI_FILTRE_KATEGORI_ID' => $request->filtre_kategori_id]);
        session(['UTI_FILTRE_EGITIM_ID' => $request->filtre_egitim_id]);
        session(['UTI_FILTRE_EGITMEN_ID' => $request->filtre_egitmen_id]);
        return redirect()->to('/'.session('PREFIX'));
    }

    private function filtreOlustur(&$result) {
        $filtre_result = clone $result;

        $filtre_yil = $filtre_result
            ->selectraw('year(egitmenler.created_at) as yil')
            ->groupby(DB::raw('year(egitmenler.created_at)'))
            ->get()
        ;

        if((int)session('UTI_FILTRE_YIL') > 0) {
            $result->whereraw('year(egitmenler.created_at) = '.session('UTI_FILTRE_YIL'));
        }
        if((int)session('UTI_FILTRE_ULKE_ID') > 0) {
            $result->where('egitmenler.yasadigi_ulke', session('UTI_FILTRE_ULKE_ID'));
        }
        if((int)session('UTI_FILTRE_DIL_ID') > 0) {
            $result->leftjoin('egitmen_diller', 'egitmen_diller.egitmen_id', '=', 'egitmenler.id')
                ->where('egitmen_diller.dil_id', session('UTI_FILTRE_DIL_ID'));
        }
        if((int)session('UTI_FILTRE_KATEGORI_ID') > 0) {
            $result->leftjoin('egitmen_egitimkategori', 'egitmen_egitimkategori.egitmen_id', '=', 'egitmenler.id')
                ->where('egitmen_egitimkategori.egitim_kategori_id', session('UTI_FILTRE_KATEGORI_ID'));
        }
        if((int)session('UTI_FILTRE_EGITIM_ID') > 0) {
            $result->leftjoin('egitimler', 'egitimler.kategori_id', '=', 'egitmen_egitimkategori.egitim_kategori_id')
                ->where('egitimler.id', session('UTI_FILTRE_EGITIM_ID'));
        }
        if((int)session('UTI_FILTRE_EGITMEN_ID') > 0) {
            $result->where('egitmenler.id', session('UTI_FILTRE_EGITMEN_ID'));
        }

        return $filtre_yil;
    }
}
