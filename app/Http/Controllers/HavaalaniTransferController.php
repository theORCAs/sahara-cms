<?php

namespace App\Http\Controllers;

use App\Http\Models\EmailSablon;
use App\Http\Models\HavaalaniTransfer;
use App\Http\Models\HavaalaniTransferKisiler;
use App\Http\Models\HavaalaniTransferTnm;
use App\Http\Models\Havaalanlari;
use App\Http\Models\HavayoluSirket;
use App\Http\Models\KullaniciRolleri;
use App\Http\Models\Oteller;
use App\Http\Models\PartnerKategorileri;
use App\Http\Models\Partnerler;
use App\Http\Models\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

class HavaalaniTransferController extends HomeController
{
    public function onayliListe() {
        $data["liste"] = HavaalaniTransfer::wherenull('deleted_at')
            ->where('durum', 2)
            ->whereRaw('havaalani_transfer.gelis_tarih >= curdate()')
            ->orderby('havaalani_transfer.gelis_tarih', 'asc')
            ->paginate(10);

        //return $data["liste"];
        // return $query->toSql();

        // $data["liste"] = $query->paginate(10);
        session(['PREFIX' => "at_confirmed_arr"]);

        $data['prefix'] = session('PREFIX');
        $data['alt_baslik'] = "Confirmed-Arrivals";

        return view('airport_transfer.gelistransfer_list', $data);
    }

    public function onayliDepartureListe() {
        $liste = HavaalaniTransfer::wherenull('deleted_at')
            ->where('durum', 2)
            ->wherenotnull('gidis_havayolu_id')
            ->orderby('havaalani_transfer.gidis_tarih', 'desc')
            ->paginate(10);

        session(['PREFIX' => "at_confirmed_dep"]);
        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Confirmed-Departures"
        ];

        return view('airport_transfer.gelistransfer_list', $data);
    }

    public function onayliGecmisListe() {
        $data["liste"] = HavaalaniTransfer::wherenull('deleted_at')
            ->where('durum', 2)
            ->whereRaw('havaalani_transfer.gelis_tarih < curdate()')
            ->orderby('havaalani_transfer.gelis_tarih', 'desc')
            ->paginate(10);

        session(['PREFIX' => "at_past"]);

        $data['prefix'] = session('PREFIX');
        $data['alt_baslik'] = "Confirmed Past Arrivals";

        return view('airport_transfer.gelistransfer_list', $data);
    }

    public function reddedilmisListe() {
        if(!Auth::user()->isAllow('at_rejected_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data["liste"] = HavaalaniTransfer::wherenull('deleted_at')
            ->where('durum', 3)
            ->paginate(10);

        session(['PREFIX' => "at_rejected"]);

        $data['prefix'] = session('PREFIX');
        $data['alt_baslik'] = "Rejected Past Arrivals";

        return view('airport_transfer.gelistransfer_list', $data);
    }

    public function airportSign($prefix, $id) {
        $data['bilgi'] = HavaalaniTransfer::find($id);
        return view('airport_transfer.airportsign', $data);
    }

    public function gelisTransferFirmaOnayla($prefix, $id) {
        try {
            HavaalaniTransfer::find($id)->update([
                'gelis_tasima_onay_id' => Auth::user()->id,
                'gelis_tasima_onay_tarih' => date("Y-m-d H:i:s")
            ]);
            return redirect()
                ->back()
                ->with(['msj' => config('messages.islem_basarili')]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function gelisTransferFirmaOnaylama($prefix, $id) {
        try {
            HavaalaniTransfer::find($id)->update([
                'gelis_tasima_onay_id' => null,
                'gelis_tasima_onay_tarih' => null
            ]);
            return redirect()
                ->back()
                ->with(['msj' => config('messages.islem_basarili')]);

        } catch (\Exception $e) {
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
        if(!Auth::user()->isAllow('at_ca_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Add New FAQ",
            'data' => new Sss()
        ];
        return view('website.sss.edit', $data);
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
     * @param  \App\Http\Models\HavaalaniTransfer  $havaalaniTransfer
     * @return \Illuminate\Http\Response
     */
    public function show(HavaalaniTransfer $havaalaniTransfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\HavaalaniTransfer  $havaalaniTransfer
     * @return \Illuminate\Http\Response
     */
    public function edit(HavaalaniTransfer $havaalaniTransfer, $id)
    {
        if(!Auth::user()->isAllow('at_ca_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Edit",
            'data' => $havaalaniTransfer->findorfail($id),
            'havayollari' => HavayoluSirket::wherenull('deleted_at')->orderby('sira', 'asc')->get(),
            'havaalanlari' => Havaalanlari::wherenull('deleted_at')->orderby('adi', 'asc')->get(),
            'oteller' => Oteller::wherenull('deleted_at')->orderby('adi', 'asc')->get(),
            'sorumlular' => KullaniciRolleri::wherein('kullanici_rolleri.rol_id', [3, 5, 6])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->where('kullanicilar.id', '>', '0')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi', 'asc')
                ->select('kullanici_rolleri.*')
                ->get(),
            'tasima_firmalar' => PartnerKategorileri::where('partner_kategori.id', '2')
                ->leftjoin('partnerler', 'partnerler.kategori_id', '=', 'partner_kategori.id')
                ->orderby('partnerler.adi', 'asc')
                ->select('partner_kategori.*')
                ->get(),
            'tasima_amac_listesi' => HavaalaniTransferTnm::where('grup_id', 1)
                ->orderby('adi')
                ->get()
        ];
        return view('airport_transfer.gelistransfer_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\HavaalaniTransfer  $havaalaniTransfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HavaalaniTransfer $havaalaniTransfer, $id)
    {
        if(!Auth::user()->isAllow('at_ca_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'gelis_havayolu_id.required' => 'Please select Arrival Airlines',
                'gelis_havaalani_id.required' => 'Please select Arrival Airport.',
                'gelis_ucus_no.required' => 'Flight No is required.',
                'gelis_tarih.required' => 'Arrival Date is required',
                'gelis_tarih.date_format' => 'Arrival Date format must be day.month.Year',
                'gelis_saat.required' => 'Arrival Time is required',
                'gelis_saat.date_format' => 'Arrival Time format must be Hour:minute',
                'gidis_tarih.date_format' => 'Departure Date format must be day.month.Year',
                'gidis_saat.date_format' => 'Departure Time format must be Hour:minute',
                'kontak_email.email' => '1. Contact Person email must be valid',
                'kontak2_email.email' => '2. Contact Person email must be valid',
                'kontak3_email.email' => '3. Contact Person email must be valid',
                'gelis_tasima_ucreti.numeric' => 'Arrival Transfer Fee must be number',
                'gidis_tasima_ucreti.numeric' => 'Departure Transfer Fee must be number',
            );
            $rules = array(
                'gelis_havayolu_id' => 'required',
                'gelis_havaalani_id' => 'required',
                'gelis_ucus_no' => 'required',
                'gelis_tarih' => 'required|date_format:d.m.Y',
                'gelis_saat' => 'required|date_format:H:i',
                'gidis_tarih' => 'sometimes|nullable|date_format:d.m.Y',
                'gidis_saat' => 'sometimes|nullable|date_format:H:i',
                'kontak_email' => 'sometimes|nullable|email',
                'kontak2_email' => 'sometimes|nullable|email',
                'kontak3_email' => 'sometimes|nullable|email',
                'gelis_tasima_ucreti' => 'sometimes|nullable|numeric',
                'gidis_tasima_ucreti' => 'sometimes|nullable|numeric',
            );
            foreach($request->k_id as $key => $row) {
                $rules['k_adi.'.$key] = 'required';
                $error_messages['k_adi.'.$key.".required"] = ($key + 1).". passenger name must be required";
                $rules['k_email.'.$key] = 'sometimes|nullable|email';
                $error_messages['k_email.'.$key.'.email'] = ($key + 1).". passenger email is not valite";
                $rules['k_gsm.'.$key] = 'sometimes|nullable|numeric';
                $error_messages['k_gsm.'.$key.'.numeric'] = ($key + 1).". passenger GSM number must be numeric";
                $rules['k_yakinlik_derecesi.'.$key] = 'required';
                $error_messages['k_yakinlik_derecesi.'.$key.".required"] = ($key + 1).". passenger Purpose of Transfer is required";
            }
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $havaalaniTransfer->find($id)
                ->update([
                    'gelis_havayolu_id' => $request->input('gelis_havayolu_id'),
                    'gelis_havaalani_id' => $request->gelis_havaalani_id,
                    'gelis_ucus_no' => $request->gelis_ucus_no,
                    'gelis_tarih' => date("Y-m-d", strtotime($request->gelis_tarih)),
                    'gelis_saat' => $request->gelis_saat.":00",
                    'gidis_havayolu_id' => $request->gidis_havayolu_id > 0 ? $request->gidis_havayolu_id : null,
                    'gidis_havaalani_id' => $request->gidis_havaalani_id > 0 ? $request->gidis_havaalani_id : null,
                    'gidis_ucus_no' => $request->gidis_ucus_no,
                    'gidis_tarih' => $request->gidis_tarih != "" ? date('Y-m-d', strtotime($request->gidis_tarih)) : null,
                    'gidis_saat' => $request->gidis_saat != "" ? date('H:i:s', strtotime($request->gidis_saat)) : null,
                    'otel_id' => $request->otel_id > 0 ? $request->otel_id : null,
                    'otel_adi' => $request->otel_id > 0 ? null : $request->otel_adi,
                    'otel_website' => $request->otel_id > 0 ? null : $request->otel_website,
                    'gelis_tasima_firma_id' => $request->gelis_tasima_firma_id > 0 ? $request->gelis_tasima_firma_id : null,
                    'gelis_tasima_ucreti' => $request->gelis_tasima_ucreti > 0 ? $request->gelis_tasima_ucreti : null,
                    'gelis_ek_notlar' => $request->gelis_ek_notlar,
                    'gidis_tasima_firma_id' => $request->gidis_tasima_firma_id > 0 ? $request->gidis_tasima_firma_id : null,
                    'gidis_tasima_ucreti' => $request->gidis_tasima_ucreti > 0 ? $request->gidis_tasima_ucreti : null,
                    'gidis_ek_notlar' => $request->gidis_ek_notlar,
                    'durum' => $request->durum
                ]);
            foreach($request->k_id as $key => $k_idsi) {

                HavaalaniTransferKisiler::find($k_idsi)
                    ->update([
                        'adi' => $request->k_adi[$key],
                        'pasaport_no' => $request->k_pasaport_no[$key],
                        'email' => $request->k_email[$key],
                        'gsm_kodu' => $request->k_gsm[$key] != "" ? $request->k_gsm_kodu[$key] : null,
                        'gsm' => $request->k_gsm[$key] != "" ? $request->k_gsm[$key] : null,
                    ]);

            }

            if($request->durum != 3) {


                if(isset($request->tekrar_mail_gonder)) {
                    $kayit = $havaalaniTransfer->find($id);
                    $sablon = EmailSablon::find(7);
                    $konu = $sablon->alan1;
                    $konu = str_replace(
                        ['{course_title}', '{course_start_date}'],
                        [$kayit->teklif->egitimKayit->egitimler->kodu." ".$kayit->teklif->egitimKayit->egitimler->adi, date('d.m.Y', strtotime($kayit->teklif->egitimKayit->egitimTarihi->baslama_tarihi))],
                        $konu);
                    $icerik = str_replace('{TRANSFER_BILGILERI}', '', $sablon->alan2);
                    $attach = "";

                    $data = [
                        'oncelik' => 15,
                        'konu' => $konu,
                        'from_email' => $sablon->alan4,
                        'to_email' => $kayit->teklif->egitimKayit->ct_sirket_email,
                        'cc' => $sablon->alan6,
                        'bcc' => $sablon->alan7,
                        'icerik' => $icerik,
                        'ekler' => $attach
                    ];

                    SendEmail::create($data);
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
     * @param  \App\Http\Models\HavaalaniTransfer  $havaalaniTransfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(HavaalaniTransfer $havaalaniTransfer, $id)
    {
        if(!Auth::user()->isAllow('at_ca_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        $havaalaniTransfer->destroy($id);

        return redirect()
            ->back()
            ->with('msj', config('messages.islem_basarili'));
    }
}
