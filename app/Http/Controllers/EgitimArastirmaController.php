<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimArastirma;
use App\Http\Models\EmailSablon;
use App\Http\Models\SendEmail;
use Illuminate\Http\Request;
use Auth;
use Validator;

class EgitimArastirmaController extends HomeController
{
    private $flg_cevaplandi;
    private $alt_baslik;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $liste = EgitimArastirma::wherenull('deleted_at')
            ->where('flg_cevaplandi', $this->flg_cevaplandi)
            ->orderby('created_at', 'desc')
            ->paginate(100);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => $this->alt_baslik,
        ];

        return view('egitimler.inquryform_view', $data);
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
     * @param  \App\Http\Models\EgitimArastirma  $egitimArastirma
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimArastirma $egitimArastirma)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitimArastirma  $egitimArastirma
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitimArastirma $egitimArastirma, $id)
    {
        if(!Auth::user()->isAllow('if_nonchecked_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Edit Inqury Form",
            'data' => $egitimArastirma->findorfail($id)
        ];
        return view('egitimler.inquryform_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitimArastirma  $egitimArastirma
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitimArastirma $egitimArastirma, $id)
    {
        if(!Auth::user()->isAllow('if_nonchecked_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $egitimArastirma->find($id)
                ->update([
                    'flg_durum' => $request->input('flg_durum'),
                    'flg_cevaplandi' => intval($request->flg_cevaplandi)
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
     * @param  \App\Http\Models\EgitimArastirma  $egitimArastirma
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitimArastirma $egitimArastirma, $id)
    {
        if(!Auth::user()->isAllow('if_nonchecked_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $egitimArastirma->destroy($id);
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

    public function toBeChecked() {
        if(!Auth::user()->isAllow('if_checked_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session(['PREFIX' => 'if_tobechecked']);

        $this->flg_cevaplandi = "0";
        $this->alt_baslik = "To be checked";
        return $this->index();
    }

    public function checked() {
        if(!Auth::user()->isAllow('if_nonchecked_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        session(['PREFIX' => 'if_checked']);

        $this->flg_cevaplandi = "1";
        $this->alt_baslik = "Already Checked";
        return $this->index();
    }

    public function sendEmailView($id) {
        $sablon = EmailSablon::find(22);
        $kayit = EgitimArastirma::find($id);
        $data = [
            'alt_baslik' => 'Send Email',
            'hid_id' => $id,
            'data' => [
                'konu' => str_replace('{EGITIM_ADI}', $kayit->egitim->adi == $kayit->egitim->adi ? " - General Inqury" : " - ".$kayit->egitim->adi, $sablon->alan1),
                'from_email' => $sablon->alan4,
                'to_email' => $kayit->email,
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace([
                    '{TRAINING}',
                    '{FULL_NAME}',
                    '{JOB_TITLE}',
                    '{COUNTRY}',
                    '{CITY}',
                    '{COMPANY}',
                    '{C_WEBSITE}',
                    '{CONTACT_NO}',
                    '{FAX}',
                    '{EMAIL}',
                    '{INQUIRY}',
                ], [
                    $kayit->egitim->adi == "" ? "General Inqury" : $kayit->egitim->adi,
                    $kayit->adi_soyadi,
                    $kayit->departman,
                    $kayit->ulke->adi,
                    $kayit->sehir,
                    trim($kayit->referansSirket->adi." ".$kayit->sirket_adi),
                    $kayit->sirket_web,
                    trim($kayit->telefon_kodu." ".$kayit->telefon),
                    trim($kayit->faks_kodu." ".$kayit->faks),
                    $kayit->email,
                    nl2br($kayit->aciklama),
                ], $sablon->alan2),
            ],
            'prefix' => session('PREFIX')

        ];
        return view('egitimler.inquryform_email', $data);
    }

    public function sendEmail(Request $request) {
        $rules = [
            'to_email' => 'required|email',
            'konu' => 'required',
            'icerik' => 'required',
        ];
        $error_messages = [
            'to_email.required' => 'To receive is required.',
            'to_email.email' => 'To receive is mail is not valid.',
            'konu.required' => 'Subject is required.',
            'icerik.required' => 'Content is required.',
        ];
        try {
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

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

            EgitimArastirma::find($request->hid_id)->update([
                'flg_cevaplandi' => "1"
            ]);

            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }
}
