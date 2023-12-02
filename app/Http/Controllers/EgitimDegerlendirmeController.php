<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimDegerlendirme;
use App\Http\Models\EgitimDegerlendirmeTnm;
use App\Http\Models\EgitimHocalar;
use App\Http\Models\EgitimKayitlar;
use App\Http\Models\EmailSablon;
use App\Http\Models\Katilimcilar;
use App\Http\Models\KatilimcilarEk;
use App\Http\Models\SendEmail;
use App\Http\Models\Teklifler;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use PDF;

class EgitimDegerlendirmeController extends HomeController
{
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
     * @param  \App\Http\Models\EgitimDegerlendirme  $egitimDegerlendirme
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimDegerlendirme $egitimDegerlendirme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitimDegerlendirme  $egitimDegerlendirme
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitimDegerlendirme $egitimDegerlendirme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitimDegerlendirme  $egitimDegerlendirme
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitimDegerlendirme $egitimDegerlendirme)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EgitimDegerlendirme  $egitimDegerlendirme
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitimDegerlendirme $egitimDegerlendirme)
    {
        //
    }

    public function cae_upcoming() {
        if(!Auth::user()->isAllow('cae_uc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = Teklifler::where('teklifler.durum', 2)
            ->wherenotnull('teklif_gon_tarih')
            ->where('teklifler.flg_silindi', 0)
            ->whereHas('egitimKayit', function (Builder $query) {
                $query->wherenull('deleted_at');
            })
            ->whereHas('egitimKayit.egitimTarihi', function (Builder $query) {
                $query->whereRaw('baslama_tarihi >= curdate()');
            })
            //->orderby('egitim_tarihleri.baslama_tarihi', 'asc')
            //->orderby('teklifler.created_at', 'asc')
            //->orderby('teklifler.id', 'desc')
            // ->select('teklifler.*')
            ->paginate(10);

        session(['PREFIX' => 'cae_upcoming']);
        $data = [
            'liste' => $liste,
            'prefix' => 'cae_upcoming',
            'alt_baslik' => 'List Record'
        ];

        return view('egitim_kayitlar.degerlendirme.view', $data);
    }
    public function cae_past() {
        if(!Auth::user()->isAllow('cae_pc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = Teklifler::where('teklifler.durum', 2)
            ->wherenotnull('teklif_gon_tarih')
            ->where('teklifler.flg_silindi', 0)
            ->whereHas('egitimKayit', function (Builder $query) {
                $query->wherenull('deleted_at');
            })
            ->whereHas('egitimKayit.egitimTarihi', function (Builder $query) {
                $query->whereRaw('baslama_tarihi < curdate()');
            })
            //->orderby('egitim_tarihleri.baslama_tarihi', 'asc')
            //->orderby('teklifler.created_at', 'asc')
            //->orderby('teklifler.id', 'desc')
            // ->select('teklifler.*')
            ->paginate(100);

        session(['PREFIX' => 'cae_past']);

        $data = [
            'liste' => $liste,
            'prefix' => 'cae_past',
            'alt_baslik' => 'List Record'
        ];

        return view('egitim_kayitlar.degerlendirme.view', $data);
    }

    public function evaluationFormCreate($prefix, $katilimci_id) {
        $katilimci = Katilimcilar::find($katilimci_id);
        $egitim_kayit = EgitimKayitlar::find($katilimci->egitim_kayit_id);
        $teklif_id = $egitim_kayit->ref_teklif_id;
        $data = [
            'katilimci' => $katilimci,
            'egitim_kayit' => $egitim_kayit,
            'hocalar_listesi' => EgitimHocalar::where('egitim_hocalar.egitim_kayit_id', $katilimci->egitim_kayit_id)
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'egitim_hocalar.hoca_id')
                ->leftjoin('egitmen_degerlendirme', function($join) use ($katilimci_id) {
                    $join->on('egitmen_degerlendirme.egitim_hoca_id', '=', 'egitim_hocalar.id');
                    $join->on('egitmen_degerlendirme.katilimci_id', '=', DB::raw($katilimci_id));
                })
                ->wherenotnull('egitim_hocalar.hoca_id')
                ->select('egitim_hocalar.id', 'kullanicilar.adi_soyadi', 'egitmen_degerlendirme.soru1',
                    'egitmen_degerlendirme.soru2', 'egitmen_degerlendirme.soru3')
                ->groupby('kullanicilar.id')
                ->orderby('kullanicilar.adi_soyadi')
                ->get(),
            'sorular_listesi' => EgitimDegerlendirmeTnm::where('egitim_degerlendirme_tnm.grup_id', '1')
                ->leftjoin('egitim_degerlendirme', function ($join) use ($katilimci_id, $teklif_id) {
                    $join->on('egitim_degerlendirme.katilimci_id', '=', DB::raw($katilimci_id));
                    $join->on('egitim_degerlendirme.teklif_id', '=', DB::raw($teklif_id));
                })
                ->select('egitim_degerlendirme_tnm.adi', 'egitim_degerlendirme_tnm.flg_radio', 'egitim_degerlendirme_tnm.alan_adi',
                    'egitim_degerlendirme.soru1', 'egitim_degerlendirme.soru2', 'egitim_degerlendirme.soru3', 'egitim_degerlendirme.soru4',
                    'egitim_degerlendirme.soru5', 'egitim_degerlendirme.soru6', 'egitim_degerlendirme.soru7')
                ->orderby('egitim_degerlendirme_tnm.sira', 'asc')
                ->get(),
        ];
        $path = "public/evaluation_form/";
        $filename = "evalform_".$katilimci_id.".pdf";

        $pdf = PDF::loadView('egitim_kayitlar/pdf/eval_form_view', $data);
        //$pdf->setOption('footer-center', 'deneme');
        $pdf->setPaper('a4', 'portraid');

        $pdf->save(storage_path().'/app/'.$path.$filename);
        KatilimcilarEk::updateorcreate([
            'egitim_kayit_id' => $egitim_kayit->id,
            'teklif_id' => $egitim_kayit->ref_teklif_id,
            'katilimci_id' => $katilimci->id
        ], [
            'evaluation_form_pdf' => $path.$filename
        ]);

        //return redirect('/pm_wait');
        return $pdf->stream($filename);
    }

    public function evaluationMailView($prefix, $katilimci_id) {
        $katilimci = Katilimcilar::find($katilimci_id);
        $egitim_kayit = EgitimKayitlar::find($katilimci->egitim_kayit_id);
        $sablon = EmailSablon::find(14);
        $link = "http://www.saharatraining.com/?course_evaluation_form,".md5($katilimci->id);
        $link_text = "<a href=\"$link\">$link</a>";
        $data = [
            'prefix' => 'cae_upcoming',
            'alt_baslik' => 'Email for Online Evaluation',
            'katilimci_id' => $katilimci->id,
            'egitim_kayit_id' => $katilimci->egitim_kayit_id,
            'teklif_id' => $egitim_kayit->ref_teklif_id,
            'data' => [
                'from_email' => $sablon->alan4,
                'to_email' => $katilimci->email,
                'konu' => $sablon->alan1,
                'cc' => $sablon->alan6,
                'bcc' => $sablon->alan7,
                'icerik' => str_replace(
                    [
                        '{KISI_ADI}',
                        '{COURSE_EVALUATION_FORM_LINK}'
                    ],
                    [
                        $katilimci->adi_soyadi,
                        $link_text
                    ],
                    $sablon->alan2
                )
            ],
        ];

        return view('egitim_kayitlar.degerlendirme.eval_mail_view', $data);
    }

    public function evaluationMailSend(Request $request, $prefix, $katilimci_id) {
        try {
            $rules = [
                'from_email' => 'required|email',
                'to_email' => 'required|email',
                'konu' => 'required',
                'icerik' => 'required',
            ];
            $error_messages = [
                'from_email.required' => 'From email (Reply to) is required.',
                'from_email.email' => 'From email (Reply to) is not valid email.',
                'to_email.required' => 'To receive is required.',
                'to_email.email' => 'To receive is not valid email.',
                'konu.required' => 'Subject is required.',
                'icerik.required' => 'Content is required.',
            ];
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
            $ekler = trim($ekler1.($ekler1 != "" && $ekler2 != "" ? "," : "").$ekler2);


            SendEmail::create([
                'oncelik' => 5,
                'konu' => $request->konu,
                'from_email' => $request->from_email,
                'to_email' => trim($request->to_email),
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'icerik' => $request->icerik,
                'ekler' => $ekler
            ]);

            KatilimcilarEk::updateorcreate([
                'egitim_kayit_id' => $request->hid_egitim_kayit_id,
                'teklif_id' => $request->hid_teklif_id,
                'katilimci_id' => $request->hid_katilimci_id,
            ], [
                'evaluation_mail' => date('Y-m-d H:i:s')
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
