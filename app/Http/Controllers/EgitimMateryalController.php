<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimHocalar;
use App\Http\Models\EgitimMateryal;
use App\Http\Models\Teklifler;
use App\Http\Models\WSMenuler;
use http\Env\Response;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EgitimMateryalController extends HomeController
{
    private $prefix = "cm_view";
    private $error_messages = array(
        'aciklama.required' => 'Explanation is required.',
        'dosya.required' => 'Material is required.',
        'dosya.mimes' => 'File must be ppt, pptx, doc, docx, pdf'
    );
    private $rules = array(
        'aciklama' => 'required',
        'dosya' => 'required|mimes:ppt,pptx,doc,docx,pdf'
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('im_cdc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = Teklifler::where('teklifler.durum', '2')
            ->leftjoin('egitim_hocalar', 'egitim_hocalar.teklif_id', '=', 'teklifler.id')
            ->leftjoin('egitim_kayitlar', 'egitim_kayitlar.id', '=', 'teklifler.egitim_kayit_id')
            ->leftjoin('egitimler', 'egitimler.id', '=', 'egitim_kayitlar.egitim_id')
            ->leftjoin('egitim_tarihleri', 'egitim_tarihleri.id', '=', 'egitim_kayitlar.egitim_tarih_id')
            ->where('egitim_hocalar.hoca_id', Auth::user()->id)
            ->groupby('teklifler.id')
            ->orderby('egitim_tarihleri.baslama_tarihi', 'desc')
            ->select('teklifler.id as teklif_id', 'egitimler.adi as kurs_adi', 'teklifler.id',
                'egitim_hocalar.ony_materyal', 'egitim_hocalar.ony_guideline', 'egitim_hocalar.ony_feerate', 'egitim_hocalar.ony_feepay', 'egitim_hocalar.ony_confidentiality',
                'egitim_hocalar.id as eh_id')
            ->paginate(20);

        foreach($liste as $key => $row) {
            $result = EgitimHocalar::where('teklif_id', $row->teklif_id)
                ->where('hoca_id', Auth::user()->id)
                ->orderby('ders_sira', 'asc')
                ->select('id', 'ders_sira', 'ders_tarihi')
                ->get();

            $liste[$key]['egitim_bilgi'] = $result;

            $result = EgitimMateryal::where('teklif_id', $row->teklif_id)
                ->where('kullanici_id', Auth::user()->id)
                ->select('id', 'dosya_adi', 'dosya')
                ->orderby('id', 'desc')
                ->get();
            $liste[$key]['yuklu_dosyalar'] = $result;
        }

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "-"
        ];

        return view('egitmen.materyal_view', $data);
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
     * @param  \App\Http\Models\EgitimMateryal  $egitimMateryal
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimMateryal $egitimMateryal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EgitimMateryal  $egitimMateryal
     * @return \Illuminate\Http\Response
     */
    public function edit(EgitimMateryal $egitimMateryal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EgitimMateryal  $egitimMateryal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EgitimMateryal $egitimMateryal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EgitimMateryal  $egitimMateryal
     * @return \Illuminate\Http\Response
     */
    public function destroy(EgitimMateryal $egitimMateryal)
    {
        //
    }

    public function readAndConfirmation($eh_id) {
        $data = [
            'materyal_yazi' => WSMenuler::where('shortcut', 'material')->select('icerik')->first(),
            'delivery_yazi' => WSMenuler::where('shortcut', 'guidelines')->select('icerik')->first(),
            'fee_yazi' => WSMenuler::where('shortcut', 'rates')->select('icerik')->first(),
            'payment_yazi' => WSMenuler::where('shortcut', 'payments')->select('icerik')->first(),
            'conf_yazi' => WSMenuler::where('shortcut', 'confidentiality')->select('icerik')->first(),
            'data' => EgitimHocalar::where('id', $eh_id)->select('id', 'ony_materyal', 'ony_guideline',
                'ony_feerate', 'ony_feepay', 'ony_confidentiality')->first(),
            'prefix' => $this->prefix,
            'alt_baslik' => "-"
        ];
        return view('egitmen.materyal_onay', $data);
    }

    public function readAndConfirmationSet(Request $request) {
        if(!Auth::user()->isAllow('im_cdc_view')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            EgitimHocalar::find($request->id)->update([
                $request->alan_adi => $request->islem
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

    public function upload($teklif_id) {
        if(!Auth::user()->isAllow('im_cdc_view')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $egitim_bilgi = Teklifler::where('id', $teklif_id)->first();
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Course Material",
            'data' => new EgitimMateryal(),
            'teklif_id' => $teklif_id,
            'egitim_kayit_id' => $egitim_bilgi->egitim_kayit_id,
            'egitim_bilgi' => $egitim_bilgi
        ];
        return view('egitmen.materyal_upload', $data);
    }

    public function uploadYap(Request $request) {
        if(!Auth::user()->isAllow('im_cdc_view')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $this->rules['hid_egitim_kayit_id'] = 'required';
            $this->rules['hid_teklif_id'] = 'required';
            $this->error_message['hid_egitim_kayit_id.required'] = "Course information could not be retrieved.";
            $this->error_message['hid_teklif_id.required'] = "Failed to get proposal information.";

            $validator = Validator::make($request->all(), $this->rules, $this->error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            EgitimMateryal::create([
                'egitim_kayit_id' => $request->input('hid_egitim_kayit_id'),
                'teklif_id' => $request->hid_teklif_id,
                'kullanici_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'aciklama' => $request->aciklama,
                'dosya_adi' => $request->dosya->getClientOriginalName(),
                'dosya' => $request->file("dosya")->store("public/kurs_materyal"),
                'ext' => $request->dosya->getClientOriginalExtension(),
                'boyut' => $request->dosya->getSize(),
                'mime' => $request->dosya->getClientMimeType()
            ]);
            return redirect('/cm_upload/'.$request->hid_teklif_id)->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function uploadDelete($id) {
        if(!Auth::user()->isAllow('im_cdc_view')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            EgitimMateryal::destroy($id);
            return response()->json([
                'cvp' => '1'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()
            ]);
        }

    }
}
