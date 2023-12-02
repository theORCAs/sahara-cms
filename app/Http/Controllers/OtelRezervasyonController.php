<?php

namespace App\Http\Controllers;

use App\Http\Models\EmailSablon;
use App\Http\Models\KullaniciRolleri;
use App\Http\Models\OtelManzaralari;
use App\Http\Models\OtelRezervasyon;
use App\Http\Models\OtelRezervasyonOda;
use App\Http\Models\OtelSehirleri;
use App\Http\Models\SendEmail;
use App\User;
use Illuminate\Http\Request;
use Validator;
use Auth;

class OtelRezervasyonController extends HomeController
{
    public function bekleyenListe() {
        if(!Auth::user()->isAllow('hrm_rr_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session(['PREFIX' => 'hrsm_request']);

        $liste = OtelRezervasyon::wherenull('deleted_at')
            ->where('durum', 1) // bu açılacak ekranı güncellemek için değiştirildi...
            // ->where('durum', 4)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'liste' => $liste,
            'alt_baslik' => 'Reservation Request',
            'prefix' => session('PREFIX')
        ];

        return view("hotel_reservation.view", $data);

    }

    public function islemdekiListe() {
        if(!Auth::user()->isAllow('hrm_pr_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $data = [
            'alt_baslik' => 'Reservation Request',
            'prefix' => 'hrsm_request'
        ];

        $data["liste"] = OtelRezervasyon::wherenull('deleted_at')
            ->where('durum', 4)
            ->orderBy('created_at', 'desc')
            // ->select('otl_oteller.*')
            ->paginate(10);

        return view("hotel_reservation.view", $data);

    }

    public function onayliListe() {
        if(!Auth::user()->isAllow('hrm_confirmed_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $data = [
            'alt_baslik' => 'Reservation Request',
            'prefix' => 'hrsm_request'
        ];

        $data["liste"] = OtelRezervasyon::wherenull('deleted_at')
            ->where('durum', 2)
            ->orderBy('created_at', 'desc')
            // ->select('otl_oteller.*')
            ->paginate(10);

        return view("hotel_reservation.view", $data);

    }

    public function teyitMaili($prefix, $id) {
        return "aaaa".$id;

    }

    public function emailToHotel($prefix, $rezervasyon_oda_id) {
        $sablon = EmailSablon::find(38);
        $bilgi = OtelRezervasyonOda::find($rezervasyon_oda_id);

        $data = [
            'alt_baslik' => 'Email to Hotel Reservation',
            'prefix' => session('PREFIX'),
            'basarili_donus_url' => $prefix,
            'hatali_donus_url' => $prefix."/emailToHotel/".$rezervasyon_oda_id,
            'action_url' => $prefix."/emailToHotelSendMail/".$rezervasyon_oda_id, // eger bos gonderilirse global mail gonderimine yonlendirilir...
            'from_email' => $sablon->alan4,
            'to_email' => $bilgi->otel->email,
            'konu' => $sablon->alan1,
            'cc' => $sablon->alan6,
            'bcc' => $sablon->alan7,
            'icerik' => $sablon->alan2,
        ];
        return view('send_email_sablon', $data);
    }

    public function emailToHotelSendMail(Request $request, $prefix, $rezervasyon_oda_id) {
        $error_messages = array(
            'konu.required' => 'Subject is required.',
            'to_email.required' => 'To receive email is required.',
            'to_email.email' => 'To receive email must be valid',
            'from_email.required' => 'From email (Reply to) is required',
            'from_email.email' => 'From email (Reply to) must be valid',
        );
        $rules = array(
            'konu' => 'required',
            'to_email' => 'required|email',
            'from_email' => 'required|email',
        );
        $validator = Validator::make($request->all(), $rules, $error_messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $attach = "";
        if($request->file("ek_dosya1") != "") {
            $ek_dosya1 = $request->file("ek_dosya1")->store("public/mail_ekler");
            $attach .= ($attach != "" ? "," : "").$ek_dosya1;
        }
        if($request->file("ek_dosya2") != "") {
            $ek_dosya2 = $request->file("ek_dosya2")->store("public/mail_ekler");
            $attach .= ($attach != "" ? "," : "").$ek_dosya2;
        }

        $data = [
            'oncelik' => 15,
            'konu' => $request->konu,
            'from_email' => $request->from_email,
            'to_email' => $request->to_email,
            'cc' => $request->cc,
            'bcc' => $request->bcc,
            'icerik' => $request->icerik,
            'ekler' => $attach
        ];
        try {
            SendEmail::create($data);
            OtelRezervasyonOda::find($rezervasyon_oda_id)
                ->update([
                    'rm_tarih' => date('Y-m-d H:i:s'),
                    'rm_gonderen' => Auth::user()->id
                ]);
            return redirect('/'.$request->basarili_donus_url)->with(["msj" => config('messages.islem_basarili')]);
        } catch (\Exception $e) {
            if($request->basarisiz_donus_url != "")
                return redirect('/'.$request->basarisiz_donus_url)
                    ->withInput()
                    ->withErrors($e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Models\OtelRezervasyon  $otelRezervasyon
     * @return \Illuminate\Http\Response
     */
    public function show(OtelRezervasyon $otelRezervasyon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\OtelRezervasyon  $otelRezervasyon
     * @return \Illuminate\Http\Response
     */
    public function edit(OtelRezervasyon $otelRezervasyon, $id)
    {
        if(!Auth::user()->isAllow('wmo_hp_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Edit",
            'data' => $otelRezervasyon->findorfail($id),
            'sehir_listesi' => OtelSehirleri::wherenull('deleted_at')->orderby('adi', 'asc')->get(),
            'manzaralar' => OtelManzaralari::wherenull('deleted_at')->orderby('sira', 'asc')->get(),
            'ilgili_kisiler' => KullaniciRolleri::wherein("kullanici_rolleri.rol_id", [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->where('kullanicilar.flg_durum', 1)
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi')
                ->get()
        ];
        return view('hotel_reservation.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\OtelRezervasyon  $otelRezervasyon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtelRezervasyon $otelRezervasyon, $id)
    {
        if(!Auth::user()->isAllow('hrm_rr_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi_soyadi.required' => 'Name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be valid',
                'tarih_giris.required' => 'Check-in Date is required',
                'tarih_giris.date_format' => 'Check-in Date is not a valid date',
                'tarih_cikis.required' => 'Check-out Date is required',
                'tarih_cikis.date_format' => 'Check-out Date is not a valid date',
                'manzara_id.required' => 'View option is required',
                'durum.required' => 'Please select status',
            );
            $rules = array(
                'adi_soyadi' => 'required',
                'email' => 'required|email',
                'tarih_giris' => 'required|date_format:d.m.Y',
                'tarih_cikis' => 'required|date_format:d.m.Y',
                'manzara_id' => 'required',
                'durum' => 'required',
            );
            foreach($request->ki_id as $key => $row) {
                $rules['ki_adi.'.$key] = 'required';
                $error_messages['ki_adi.'.$key.".required"] = ($key + 1).". guest name is required";
                $rules['ki_yas.'.$key] = 'required|numeric';
                $error_messages['ki_yas.'.$key.".required"] = ($key + 1).". guest age is required";
                $error_messages['ki_yas.'.$key.".numeric"] = ($key + 1).". guest age must be number";
                $rules['ki_cinsiyet.'.$key] = 'required';
                $error_messages['ki_cinsiyet.'.$key.".required"] = ($key + 1).". guest gender is required";
            }
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $otelRezervasyon->find($id)
                ->update([
                    'adi_soyadi' => $request->input('adi_soyadi'),
                    'email' => $request->email,
                    'cep' => $request->cep,
                    'tarih_giris' => $request->tarih_giris != "" ? date("Y-m-d", strtotime($request->tarih_giris)) : null,
                    'tarih_cikis' => $request->tarih_cikis != "" ? date("Y-m-d", strtotime($request->tarih_cikis)) : null,
                    'manzara_id' => $request->manzara_id,
                    'ek_notlar' => $request->ek_notlar,
                    'ilgili_kisi' => $request->ilgili_kisi != "" ? $request->ilgili_kisi : null,
                    'islem_mesaji' => $request->islem_mesaji,
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
     * @param  \App\Http\Models\OtelRezervasyon  $otelRezervasyon
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtelRezervasyon $otelRezervasyon, $id)
    {
        if(!Auth::user()->isAllow('hrm_rr_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $otelRezervasyon->destroy($id);
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
}
