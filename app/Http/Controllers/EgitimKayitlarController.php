<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimKayitlar;
use App\Http\Models\Egitimler;
use App\Http\Models\ParaBirimi;
use App\Http\Models\PdfConfirmation;
use App\Http\Models\PdfInvoice;
use App\Http\Models\PdfProposal;
use App\Http\Models\Referanslar;
use App\Http\Models\SendEmail;
use App\Http\Models\SystemSetup;
use App\Http\Models\Teklifler;
use App\Http\Models\EmailSablon;
use App\Http\Models\Ulkeler;
use App\Http\Models\Unvanlar;
use App\Http\Models\Katilimcilar;
use App\Http\Models\Tanimlar;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use Validator;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;

class EgitimKayitlarController extends HomeController
{
    private $error_messages = array(
        'sirket_ulke_id.required' => 'Institution/Company country is required.',
        'sirket_adi.required' => 'Institution/Company name is required.',
        'sirket_web.required' => 'Institution/Company website is required.',
        'yapilan_is.required' => 'Nature of Business is required.',
        'ct_unvan_id.required' => 'Contact Person salutation is required.',
        'ct_adi_soyadi.required' => 'Contact Person name is required.',
        'ct_pozisyon.required' => 'Contact Person job title is required.',
        'sirket_adres.required' => 'Contact Person Postal Address is required.',
        'ct_sirket_email.required' => 'Contact Person corporate email is required.',
        'ct_sirket_email.email' => 'Contact Person corporate email no valid.',
        'ct_telefon.required' => 'Contact Person telephone is required.',
        'ct_cep.required' => 'Contact Person mobile is required.',
    );
    private $rules = array(
        'sirket_ulke_id' => 'required',
        'sirket_adi' => 'required_if:referans_id,',
        'sirket_web' => 'required',
       // 'yapilan_is' => 'required',
        'ct_unvan_id' => 'required',
        'ct_adi_soyadi' => 'required',
        'ct_pozisyon' => 'required',
        'sirket_adres' => 'required',
        'ct_sirket_email' => 'required|email',
        'ct_telefon' => 'required',
        'ct_cep' => 'required',
    );

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
     * @param  \App\Http\Models\EgitimKayitlar  $egitimKayitlar
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimKayitlar $egitimKayitlar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitimKayitlar  $egitimKayitlar
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitimKayitlar $egitimKayitlar, $id)
    {
        if(!Auth::user()->isAllow('pm_waiting_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        //todo burda silinen kayıtları tek sorguda getirilmesi gerkecek.
        $kayit = $egitimKayitlar->where('id',$id)->onlyTrashed()->first();
        if(empty($kayit)){
            $kayit = $egitimKayitlar->findorfail($id);
        }
        $ulkeler = Ulkeler::where("flg_aktif", 1)
            ->orwhere("id", $egitimKayitlar["sirket_ulke_id"])
            ->orderby("adi")->get();
        $unvanlar = Unvanlar::wherenull("deleted_at")->orderby("sira")->get();
        $referanslar = Referanslar::where('ulke_id', $kayit->sirket_ulke_id)->orderby('adi', 'asc')->get();
        $nereden_duydu = Tanimlar::where("grup_id", 1)->wherenull("deleted_at")->get();
        session(['PREFIX' => 'pm_send']);

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "CRF Update",
            'data' => $kayit,
            'ulkeler' => $ulkeler,
            'referanslar' => $referanslar,
            "unvanlar" => $unvanlar,
            "nereden_duydu" => $nereden_duydu,
            'crf_id' => $id
        ];

        return view('egitim_kayitlar.proposal_module.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitimKayitlar  $egitimKayitlar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitimKayitlar $egitimKayitlar)
    {
        if(!Auth::user()->isAllow('pm_waiting_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            foreach($request->hid_katilimci_id as $key => $katilimci_id) {
                $this->rules = array_merge($this->rules, [
                    'k_unvan_id.'.$key => 'required',
                    'k_adi_soyadi.'.$key => 'required',
                    //'k_email.'.$key => 'email',
                ]);

                $this->error_messages = array_merge($this->error_messages, [
                    "k_unvan_id.$key.required" => ($key + 1)." participant salutation is required",
                    "k_adi_soyadi.$key.required" => ($key + 1)." participant name is required",
                    //"k_email.$key.required" => ($key + 1)." participant email is required",
                  //  "k_email.$key.email" => ($key + 1)." participant email is not valid",
                ]);
            }

            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            EgitimKayitlar::find($request->all()['formId'])->update([
                "durum" => $request->durum,
                "sirket_ulke_id" => $request->sirket_ulke_id,
                "referans_id" => $request->referans_id > 0 ? $request->referans_id : null,
                "sirket_adi" => $request->sirket_adi,
                "sirket_web" => $request->sirket_web,
                "yapilan_is" => $request->yapilan_is,
                "ct_unvan_id" => $request->ct_unvan_id,
                "ct_adi_soyadi" => $request->ct_adi_soyadi,
                "ct_pozisyon" => $request->ct_pozisyon,
                "sirket_adres" => $request->sirket_adres,
                "ct_sirket_email" => $request->ct_sirket_email,
                "ct_telefon_kodu" => $request->ct_telefon_kodu,
                "ct_telefon" => $request->ct_telefon,
                "ct_cep_kodu" => $request->ct_cep_kodu,
                "ct_cep" => $request->ct_cep,
                "nereden_duydu_id" => $request->nereden_duydu_id
            ]);

            foreach($request->hid_katilimci_id as $key => $katilimci_id) {
                Katilimcilar::find($katilimci_id)->update([
                    "unvan_id" => $request->k_unvan_id[$key],
                    "adi_soyadi" => $request->k_adi_soyadi[$key],
                    "yasadigi_ulke_id" => $request->k_yasadigi_ulke_id[$key],
                   // "email" => $request->k_email[$key],
                    "email2" => $request->k_email2[$key] ?? null,
                    "is_pozisyonu" => $request->k_is_pozisyonu[$key] ?? null,
                    "cep_tel_kodu" => $request->k_cep_tel_kodu[$key] ?? null,
                    "cep_tel" => $request->k_cep_tel[$key] ?? null,
                    "cep_tel2_kodu" => $request->k_cep_tel2_kodu[$key] ?? null,
                    "cep_tel2" => $request->k_cep_tel2[$key] ?? null,
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
     * @param  \App\Http\Models\EgitimKayitlar  $egitimKayitlar
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitimKayitlar $egitimKayitlar)
    {
        try {
            $egitimKayitlar->update([
                    "durum" => 0,
                    "flg_silindi" => 1
                ]);
            $egitimKayitlar->destroy($egitimKayitlar->id);
            return response()->json([
                "cvp" => 1,
                "msj" => ""
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "cvp" => 0,
                "msj" => $e->getMessage()
            ]);
        }
    }

    public function pm_wait() {
        if(!Auth::user()->isAllow('pm_waiting_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimKayitlar::where("durum", 1)
            ->where("flg_silindi", 0)
            ->wherenull('deleted_at')
            ->whereHas('egitimTarihi', function (Builder $query) {
                $query->where('baslama_tarihi', '>=', date('Y-m-d'));
            })
            ->whereHas('aktifTeklif', function (Builder $query) {
                $query->where('durum', 1)
                    ->wherenull('teklif_gon_tarih');
            })
            ->orderBy("created_at", "desc")
            ->paginate(100);

        session(['PREFIX' => 'pm_wait']);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Waiting to be Sent'
        ];

        return view("egitim_kayitlar.proposal_module.pm_wait", $data);
    }

    public function pm_send() {
        if(!Auth::user()->isAllow('pm_send_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimKayitlar::where("durum", 1)
            ->where("flg_silindi", 0)
            ->wherenull('deleted_at')
            ->whereHas('aktifTeklif', function (Builder $query) {
                $query->where('durum', 1)
                    ->wherenotnull('teklif_gon_tarih');
            })
            ->orderBy("created_at", "desc")
            ->paginate(100);

        session(['PREFIX' => 'pm_send']);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Sending Proposal'
        ];

        return view("egitim_kayitlar.proposal_module.pm_wait", $data);
    }

    public function pm_rejected() {
        if(!Auth::user()->isAllow('pm_reject_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimKayitlar::where("durum", 1)
            ->where("flg_silindi", 0)
            ->wherenull('deleted_at')
            ->whereHas('aktifTeklif', function (Builder $query) {
                $query->where('durum', 3);
            })
            ->orderBy("created_at", "desc")
            ->paginate(100);

        session(['PREFIX' => 'pm_wait']);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Rejected Proposal'
        ];

        return view("egitim_kayitlar.proposal_module.pm_wait", $data);
    }

    public function pm_all() {
        if(!Auth::user()->isAllow('pm_all_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimKayitlar::where("durum", 1)
            ->where("flg_silindi", 0)
            ->wherenull('deleted_at')
            ->whereHas('aktifTeklif', function (Builder $query) {

            })
            ->orderBy("created_at", "desc")
            ->paginate(100);

        session(['PREFIX' => 'pm_wait']);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'All Proposal'
        ];

        return view("egitim_kayitlar.proposal_module.pm_wait", $data);
    }

    public function pm_deleted() {
        if(!Auth::user()->isAllow('pm_del_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimKayitlar::onlyTrashed()->paginate(100);

        session(['PREFIX' => 'pm_wait']);

        $data = [
            'liste' => $liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => 'Deleted Proposal'
        ];

        return view("egitim_kayitlar.proposal_module.pm_wait", $data);
    }

    public function inv_pdf(EgitimKayitlar $egitimKayitlar, $prefix, $egitim_kayit_id) {
        /*
        $data = array(
            "egitim_kayitlar" => EgitimKayitlar::find($egitim_kayit_id),
            "ayarlar" => EmailSablon::find(3),
            "ulkeler" => Ulkeler::where("flg_silindi", 0)->orderby("adi")->get()
        );
        */
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);
        $egitimler = Egitimler::find($egitim_kayitlar->egitim_id);
        $ayarlar = EmailSablon::find(3);

        $tmp_text = "";
        foreach($egitim_kayitlar->katilimcilar as $row) {
            $tmp_text .= ($tmp_text != "" ? "<br>" : "")."- ".$row->adi_soyadi;
        }
        if(!empty($egitim_kayitlar->pdfInvoice->aciklama."ss")){
            $aciklama = $egitim_kayitlar->pdfInvoice->aciklama;
        }else{
            $aciklama = str_replace(
                [
                    '{EGITIM_ADI}',
                    '{BASLAMA_TARIHI}',
                    '{EGITIM_DILI}',
                    '{EGITIM_SURESI}',
                    '{EGITIM_YERI}',
                    '{KATILIMCI_SAYISI}',
                    '{KATILIMCI_LISTESI}',
                ],
                [
                    trim($egitim_kayitlar->egitimler->kodu." - ".$egitim_kayitlar->egitimler->adi),
                    date('d.m.Y', strtotime($egitim_kayitlar->egitimTarihi->baslama_tarihi)),
                    $egitimler->egitimDil->adi,
                    $egitim_kayitlar->egitimTarihi->egitim_suresi." ".$egitim_kayitlar->egitimTarihi->egitimPart->adi,
                    $egitim_kayitlar->egitimTarihi->egitimYeri->adi,
                    $egitim_kayitlar->katilimcilar->count(),
                    $tmp_text

                ],
                $ayarlar->alan2);
        }


        $data = array(
            "ulkeler" => Ulkeler::where("flg_aktif", 1)
                ->orwhere("id", $egitim_kayitlar->pdfInvoice->sirket_ulke_id)
                ->orwhere("id", $egitim_kayitlar->sirket_ulke_id)
                ->orderby("adi")->get(),
            "egitim_kayit_id" => $egitim_kayitlar->id,
            "ekalan_ust" => $egitim_kayitlar->pdfInvoice->ekalan_ust,
            "tarih" => $egitim_kayitlar->pdfInvoice->tarih == "" ? date("Y-m-d") : $egitim_kayitlar->pdfInvoice->tarih,
            "referans_no" => $egitim_kayitlar->pdfInvoice->referans_no == "" ? date("Y", strtotime($egitim_kayitlar->created_at))."/".$egitim_kayitlar->id : $egitim_kayitlar->pdfInvoice->referans_no,
            "sirket_adres" => $egitim_kayitlar->pdfInvoice->sirket_adres == "" ? $egitim_kayitlar->sirket_adres : $egitim_kayitlar->pdfInvoice->sirket_adres,
            "sirket_ulke_id" => $egitim_kayitlar->pdfInvoice->sirket_ulke_id == "" ? $egitim_kayitlar->sirket_ulke_id : $egitim_kayitlar->pdfInvoice->sirket_ulke_id,
            //"aciklama" => $egitim_kayitlar->pdfInvoice->aciklama == "" ? $aciklama : $egitim_kayitlar->pdfInvoice->aciklama,
            "aciklama" => $aciklama,
            "miktar" => intval($egitim_kayitlar->pdfInvoice->miktar) > 0 ? intval($egitim_kayitlar->pdfInvoice->miktar) : intval($egitim_kayitlar->katilimcilar->count()),
            "ucret" => floatval($egitim_kayitlar->pdfInvoice->ucret) > 0 ? floatval($egitim_kayitlar->pdfInvoice->ucret) : floatval($egitim_kayitlar->egitimTarihi->egitimUcretiNumber()),
            "genel_indirim" => floatval($egitim_kayitlar->pdfInvoice->genel_indirim),
            "ekalan_alt" => $egitim_kayitlar->pdfInvoice->ekalan_alt,
            "ekalan_1" => $egitim_kayitlar->pdfInvoice->ekalan_1 != '' ? $egitim_kayitlar->pdfInvoice->ekalan_1 : $ayarlar->alan3,
            "isim" => $egitim_kayitlar->pdfInvoice->isim != '' ? $egitim_kayitlar->pdfInvoice->isim : $ayarlar->alan4,
            "pozisyon" => $egitim_kayitlar->pdfInvoice->pozisyon != '' ? $egitim_kayitlar->pdfInvoice->pozisyon : $ayarlar->alan5,
            "banka_detay" => $egitim_kayitlar->pdfInvoice->banka_detay != '' ? $egitim_kayitlar->pdfInvoice->banka_detay : $ayarlar->alan7
        );
        if($egitim_kayitlar->pdfInvoice->sirket_adi != "") {
            $data["sirket_adi"] = $egitim_kayitlar->pdfInvoice->sirket_adi;
        } else if($egitim_kayitlar->referans_id != "") {
            $data["sirket_adi"] = $egitim_kayitlar->sirketReferans->adi;
        } else {
            $data["sirket_adi"] = $egitim_kayitlar->sirket_adi;
        }

        $data["egitim_kayitlar"] = $egitim_kayitlar;
        $data["prefix"] = session('PREFIX');
        $data['para_birimleri'] = ParaBirimi::orderby('id')->get();

        return view("egitim_kayitlar.pdf.inv_pdf", $data);
    }

    public function inv_pdf_kaydet(Request $request, $tmp, $egitim_kayit_id) {
        try {
            $teklif_id = $this->inv_pdf_save($request, $egitim_kayit_id);

            return redirect()->back()->with(["msj" => config('messages.islem_basarili')]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function inv_pdf_create(Request $request, $tmp, $idsi) {

        $this->inv_pdf_save($request, $idsi);
        $teklif_id = EgitimKayitlar::find($idsi)->ref_teklif_id;
        $obj = PdfInvoice::where("teklif_id", $teklif_id)->first();
        // echo "<pre>"; dd($obj->banka_detay);exit;
        $ayarlar = EmailSablon::find(3);

        $sistem_ayar = SystemSetup::find(1);

        $data = [
            'data' => $obj,
            'imza_resim' => isset($request->imza_ekle) && !empty($sistem_ayar->imza_resmi) ? Storage::url($sistem_ayar->imza_resmi) : null, //Storage::url($ayarlar->alan1)
            'header_resim' => isset($request->header_ekle) && !empty($sistem_ayar->header_resmi) ? Storage::url($sistem_ayar->header_resmi) : null,
        ];
        $path = "public/invoice_pdf/";
        $filename = $teklif_id."_".$obj->id."_invoice.pdf";

        $viewhtml = View::make('egitim_kayitlar/pdf/inv_pdf_view', $data)->render();
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $pdf->loadHtml($viewhtml);

        Teklifler::find($teklif_id)->update([
            'invoice_pdf' => $path.$filename
        ]);
        $pdf->render();
        $output = $pdf->output();
        file_put_contents(storage_path().'/app/'.$path.$filename, $output);
        return $pdf->stream('inv_pdf_view.pdf',["Attachment" => false]);
    }

    private function inv_pdf_save($request, $egitim_kayit_id) {
        $obj_egitim_kayit = EgitimKayitlar::find($egitim_kayit_id);
        $teklif_id = $obj_egitim_kayit->ref_teklif_id;
        if(intval($teklif_id) == 0) {
            $obj_teklif = Teklifler::create([
                'egitim_kayit_id' => $obj_egitim_kayit->id,
                'durum' => 1
            ]);
            $obj_egitim_kayit->update(['ref_teklif_id' => $obj_teklif->id]);
            $teklif_id = $obj_teklif->id;
        }
        //Log::error('deneme'.$obj_egitim_kayit->ref_teklif_id);
        $obj = PdfInvoice::updateorcreate(
            [
                'teklif_id' => $teklif_id
            ],
            [
                "teklif_id" => $teklif_id,
                "ekalan_ust" => $request->ekalan_ust,
                "tarih" => date("Y-m-d", strtotime($request->tarih)),
                "referans_no" => $request->referans_no,
                "sirket_adi" => $request->sirket_adi,
                "sirket_adres" => $request->sirket_adres,
                "sirket_ulke_id" => $request->sirket_ulke_id,
                "aciklama" => $request->aciklama,
                "miktar" => $request->miktar,
                "ucret" => $request->ucret,
                "genel_indirim" => $request->genel_indirim,
                "genel_ucret_yazisi" => $request->genel_ucret_yazisi,
                "ekalan_alt" => $request->ekalan_alt,
                "ekalan_1" => $request->ekalan_1,
                "isim" => $request->isim,
                "pozisyon" => $request->pozisyon,
                "banka_detay" => str_replace('\\n', '<br>', $request->banka_detay),
                "imza_ekle" => isset($request->imza_ekle) ? 1 : 0,
                "para_birimi" => $request->para_birimi ?? 2,
            ]
        );

        return $obj->id;
    }

    public function cnf_pdf(EgitimKayitlar $egitimKayitlar, $tmp, $egitim_kayit_id) {
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);
        if($egitim_kayitlar->pdfConfirmation->id > 0) {
            $icerik = $egitim_kayitlar->pdfConfirmation->icerik;
            $alt_bilgi = $egitim_kayitlar->pdfConfirmation->alt_bilgi;
        } else {
            $tmp_katilimcilar = "";
            foreach($egitim_kayitlar->katilimcilar as $row) {
                $tmp_katilimcilar .= "<li>".$row->adi_soyadi."</li>";
            }
            $tmp_katilimcilar = $tmp_katilimcilar != "" ? "<ul>$tmp_katilimcilar</ul>" : "";

            $ayarlar = EmailSablon::find(4);
            $icerik  = str_replace(
                [
                    '{course_title}',
                    '{course_start_date}',
                    '{course_duration}',
                    '{participant_name}',
                    '{course_location}'
                ],
                [
                    $egitim_kayitlar->egitimler['kodu']." ".$egitim_kayitlar->egitimler['adi'],
                    date('d F Y', strtotime($egitim_kayitlar->egitimTarihi['baslama_tarihi'])),
                    $egitim_kayitlar->egitimTarihi['egitim_suresi']." ".$egitim_kayitlar->egitimTarihi->egitimPart["adi"],
                    $tmp_katilimcilar,
                    $egitim_kayitlar->egitimTarihi->egitimYeri['adi']
                ],
                $ayarlar->alan2);

            $alt_bilgi = $ayarlar->alan3;
        }
        $data = array(
            "egitim_kayit_id" => $egitim_kayitlar->id,
            "icerik" => $icerik,
            "alt_bilgi" => $alt_bilgi
        );
        return view("egitim_kayitlar.pdf.cnf_pdf", $data);
    }

    public function cnf_pdf_kaydet(Request $request, $tmp, $egitim_kayit_id) {
        try {
            $teklif_id = $this->cnf_pdf_save($request, $egitim_kayit_id);

            return redirect()->back()->with(["msj" => config('messages.islem_basarili')]);
        } catch (\Exception $e)  {
            return redirect()->back()->withErrors($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @param $tmp
     * @param $egitim_kayit_id
     * @return mixed
     */
    public function cnf_pdf_create(Request $request, $tmp, $egitim_kayit_id) {
        $this->cnf_pdf_save($request, $egitim_kayit_id);

        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);

        $teklif_id = $egitim_kayitlar->ref_teklif_id;
        $obj = PdfConfirmation::where("teklif_id", $teklif_id)->first();
        $sistem_ayar = SystemSetup::find(1);

        $data = [
            'data' => $obj,
            'sirket_adi' => $egitim_kayitlar->sirket_adi,
            'sirket_ulke' => $egitim_kayitlar->sirketUlke["adi"],
            'imza_resim' => isset($request->imza_ekle) && !empty($sistem_ayar->imza_resmi) ? Storage::url($sistem_ayar->imza_resmi) : null,
            'header_resim' => isset($request->header_ekle) && !empty($sistem_ayar->header_resmi) ? Storage::url($sistem_ayar->header_resmi) : null,
        ];
        $path = "public/cnf_pdf/";
        $filename = $teklif_id."_".$obj->id."_cnf.pdf";

        $viewhtml = View::make('egitim_kayitlar/pdf/cnf_pdf_view', $data)->render();
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $pdf->loadHtml($viewhtml);

        Teklifler::find($teklif_id)->update([
            'confirmation_pdf' => $path.$filename
        ]);
        $pdf->render();
        $output = $pdf->output();
        file_put_contents(storage_path().'/app/'.$path.$filename, $output);
        return $pdf->stream('cnf_pdf_view.pdf',["Attachment" => false]);
    }

    private function cnf_pdf_save($request, $egitim_kayit_id) {
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);

        $teklif_id = $egitim_kayitlar->ref_teklif_id;

        if(intval($teklif_id) == 0) {
            $obj_teklif = Teklifler::create([
                'egitim_kayit_id' => $egitim_kayitlar->id,
                'durum' => 1
            ]);
            $egitim_kayitlar->update(['ref_teklif_id' => $obj_teklif->id]);
            $teklif_id = $obj_teklif->id;
        }

        $obj = PdfConfirmation::where("teklif_id", $teklif_id)->first();
        $data = [
            "teklif_id" => $teklif_id,
            "icerik" => $request->icerik,
            "alt_bilgi" => $request->alt_bilgi
        ];
        if($obj["id"] > 0) {
            $result = $obj->update($data);
        } else {
            $obj = PdfConfirmation::create($data);
        }

        return $obj->id;
    }

    public function prp_pdf(EgitimKayitlar $egitimKayitlar, $tmp, $egitim_kayit_id) {
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);
        if($egitim_kayitlar->pdfProposal->id > 0) {
            $konu = $egitim_kayitlar->pdfProposal->konu;
            $referans = $egitim_kayitlar->pdfProposal->referans;
            $icerik = $egitim_kayitlar->pdfProposal->icerik;
            $alt_bilgi = $egitim_kayitlar->pdfProposal->alt_bilgi;
        } else {
            $ayarlar = EmailSablon::find(1);
            $konu = "";
            $referans = "";
            $icerik  = $ayarlar->alan2;
            $alt_bilgi = $ayarlar->alan3;
        }
        $data = array(
            "egitim_kayit_id" => $egitim_kayitlar->id,
            "konu" => $konu,
            "referans" => $referans,
            "icerik" => $icerik,
            "alt_bilgi" => $alt_bilgi
        );
        return view("egitim_kayitlar.pdf.prp_pdf", $data);
    }

    public function prp_pdf_kaydet(Request $request, $tmp, $egitim_kayit_id) {
        try {
            $teklif_id = $this->prp_pdf_save($request, $egitim_kayit_id);

            return redirect()->back()->with(["msj" => config('messages.islem_basarili')]);
        } catch (\Exception $e)  {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function prp_pdf_create(Request $request, $tmp, $egitim_kayit_id) {
        $this->prp_pdf_save($request, $egitim_kayit_id);

        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);

        $teklif_id = $egitim_kayitlar->ref_teklif_id;
        $obj = PdfProposal::where("teklif_id", $teklif_id)->first();
        $sistem_ayar = SystemSetup::find(1);

        $data = [
            'data' => $obj,
            'sirket_adi' => $egitim_kayitlar->sirket_adi,
            'sirket_ulke' => $egitim_kayitlar->sirketUlke["adi"],
            // 'imza_resim' => EmailSablon::find(1)->alan1
            'imza_resim' => isset($request->imza_ekle) && !empty($sistem_ayar->imza_resmi) ? Storage::url($sistem_ayar->imza_resmi) : null,
            'header_resim' => isset($request->header_ekle) && !empty($sistem_ayar->header_resmi) ? Storage::url($sistem_ayar->header_resmi) : null,
        ];
        $path = "public/prp_pdf/";
        $filename = $teklif_id."_".$obj->id."_prp.pdf";

        $viewhtml = View::make('egitim_kayitlar/pdf/prp_pdf_view', $data)->render();
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $pdf->loadHtml($viewhtml);
        Teklifler::find($teklif_id)->update([
            'proposal_pdf' => $path.$filename
        ]);
        $pdf->render();
        $output = $pdf->output();
        file_put_contents(storage_path().'/app/'.$path.$filename, $output);
        return $pdf->stream('prp_pdf_view.pdf',["Attachment" => false]);
    }

    private function prp_pdf_save($request, $egitim_kayit_id) {
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);

        $teklif_id = $egitim_kayitlar->ref_teklif_id;

        if(intval($teklif_id) == 0) {
            $obj_teklif = Teklifler::create([
                'egitim_kayit_id' => $egitim_kayitlar->id,
                'durum' => 1
            ]);
            $egitim_kayitlar->update(['ref_teklif_id' => $obj_teklif->id]);
            $teklif_id = $obj_teklif->id;
        }

        $obj = PdfProposal::where("teklif_id", $teklif_id)->first();
        $data = [
            "teklif_id" => $teklif_id,
            "konu" => $request->konu,
            "referans" => $request->referans,
            "icerik" => $request->icerik,
            "alt_bilgi" => $request->alt_bilgi
        ];
        if($obj["id"] > 0) {
            $result = $obj->update($data);
        } else {
            $obj = PdfProposal::create($data);
        }

        return $obj->id;
    }

    public function outl_pdf_create(Request $request, $tmp, $egitim_kayit_id) {
        ini_set('memory_limit',-1);set_time_limit(0);
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);
        $sistem_ayar = SystemSetup::find(1);

        $teklif_id = $egitim_kayitlar->ref_teklif_id;
        $data = [
            'data' => $egitim_kayitlar,
            'imza_resim' => !empty($sistem_ayar->imza_resmi) ? Storage::url($sistem_ayar->imza_resmi) : null,
            'header_resim' => !empty($sistem_ayar->header_resmi) ? Storage::url($sistem_ayar->header_resmi) : null,
        ];
        $path = "public/outl_pdf/";
        $filename = $teklif_id."_".$egitim_kayitlar->id."_outline.pdf";

        $viewhtml = View::make('egitim_kayitlar/pdf/outl_pdf_view', $data)->render();
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $pdf->loadHtml($viewhtml);

        Teklifler::find($teklif_id)->update([
            'outline_pdf' => $path.$filename
        ]);
        $pdf->render();
        $output = $pdf->output();
        file_put_contents(storage_path().'/app/'.$path.$filename, $output);
        return $pdf->stream('outl_pdf_view.pdf',["Attachment" => false]);
    }

    public function send_email(EgitimKayitlar $egitimKayitlar, $egitim_kayit_id) {
        $egitim_kayitlar = EgitimKayitlar::find($egitim_kayit_id);
        $ayarlar = EmailSablon::find(2);

        $konu = str_replace([
                '{EGITIM_ADI_GETIR}',
                '{EGITIM_TARIHI}'
            ], [
                $egitim_kayitlar->egitimler['kodu']." ".$egitim_kayitlar->egitimler['adi'],
                date('d.m.Y', strtotime($egitim_kayitlar->egitimTarihi['baslama_tarihi']))
            ], $ayarlar->alan1);
        $email_icerik = str_replace([
            '{EMAILI_YAZAN_KISI_ADI}',
            '{EMAILI_YAZAN_COMPANY_COUNTRY}',
            '{participant_name}',
            '{course_title}',
            '{course_start_date}',
            '{course_duration}'
        ], [
            $egitim_kayitlar->ct_adi_soyadi,
            $egitim_kayitlar->sirket_adi.", ".$egitim_kayitlar->sirketUlke["adi"],
            Katilimcilar::getKatilimcilarUL($egitim_kayit_id),
            $egitim_kayitlar->egitimler['kodu']." ".$egitim_kayitlar->egitimler['adi'],
            date('d.m.Y', strtotime($egitim_kayitlar->egitimTarihi['baslama_tarihi'])),
            $egitim_kayitlar->egitimTarihi['egitim_suresi']." ".$egitim_kayitlar->egitimTarihi->egitimPart['adi']
        ], $ayarlar->alan2);
        $katilim_result = $egitim_kayitlar->katilimcilar()->get();
        $katilimcilar_text = "";
        foreach($katilim_result as $k_row) {
            //return print_r($k_row);
            $katilimcilar_text .= $k_row->email;
        }

        $data = [
            'egitim_kayit_id' => $egitim_kayit_id,
            'konu' => $konu,
            'from_email' => $ayarlar->alan4,
            'to_email' => $egitim_kayitlar->ct_sirket_email,
            'sahara_team_email' => $ayarlar->alan6,
            'katilimcilar_mails' => $katilimcilar_text,
            'bcc_email' => $ayarlar->alan7,
            'email_icerik' => $email_icerik,
            'pdf_invoice' => $egitim_kayitlar->aktifTeklif['invoice_pdf'],
            'pdf_proposal' => $egitim_kayitlar->aktifTeklif['proposal_pdf'],
            'pdf_confirmation' => $egitim_kayitlar->aktifTeklif['confirmation_pdf'],
            'pdf_outline' => $egitim_kayitlar->aktifTeklif['outline_pdf']
        ];
        return view('egitim_kayitlar.proposal_module.sendemail', $data);
    }

    public function mail_gonder(Request $request) {
        $attach = "";
        $attach .= ($attach != "" ? "," : "").$request->pdf_invoice;
        $attach .= ($attach != "" ? "," : "").$request->pdf_proposal;
        $attach .= ($attach != "" ? "," : "").$request->pdf_confirmation;
        $attach .= ($attach != "" ? "," : "").$request->pdf_outline;
        if($request->file("ek_dosya1") != "") {
            $ek_dosya1 = $request->file("ek_dosya1")->store("public/mail_ekler");
            $attach .= ($attach != "" ? "," : "").$ek_dosya1;
        }
        if($request->file("ek_dosya2") != "") {
            $ek_dosya2 = $request->file("ek_dosya2")->store("public/mail_ekler");
            $attach .= ($attach != "" ? "," : "").$ek_dosya2;
        }

        $cc = "";
        $cc .= ($cc != "" ? "," : "").$request->cc_sirket_yetkili;
        $cc .= ($cc != "" ? "," : "").$request->cc_participant;
        $cc .= ($cc != "" ? "," : "").$request->cc_ourteam;
        $data = [
            'oncelik' => 5,
            'konu' => $request->konu,
            'from_email' => $request->from_email,
            'to_email' => $request->to_email,
            'cc' => $cc,
            'bcc' => $request->bcc,
            'icerik' => $request->icerik,
            'ekler' => $attach
        ];
        try {
            $egitim_kayitlar = EgitimKayitlar::find($request->egitim_kayit_id);

            SendEmail::create($data);
            $egitim_kayitlar->aktifTeklif->update(['teklif_gon_tarih' => date('Y-m-d H:i:s')]);

            return redirect('/pm_send')->with(["msj" => config('messages.islem_basarili')]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function referansSirketGetirJson(Request $request) {
        $result = Referanslar::where('ulke_id', $request->sirket_ulke_id)
            ->where('flg_aktif', '1')
            ->orderby('adi', 'asc')
            ->get();

        return \response()->json($result);
    }

    public function commentView($egitim_kayit_id, $teklif_id) {
        $result = EgitimKayitlar::find($egitim_kayit_id);
        $data = [
            'prefix' => 'pm_wait',
            'data' => Teklifler::find($teklif_id),
            'alt_baslik' => $result->ct_adi_soyadi." Write Comment",
            'teklif_id' => $teklif_id
        ];
        return view('egitim_kayitlar.proposal_module.comment_view', $data);
    }

    public function commentSave(Request $request) {
        try {

            Teklifler::find($request->hid_teklif_id)->update([
                'yorum' => $request->input('yorum'),
                'yorum_tarih' => date('Y-m-d H:i:s')
            ]);
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }
}
