<?php

namespace App\Http\Controllers;

use App\Http\Models\TeklifFormlar;
use App\Http\Models\Teklifler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Auth;

class TeklifFormlarController extends HomeController
{
    /**
     * @var string
     */
    private $_prefix = "teklifFormlar";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($teklif_id=null, $tur_id=null)
    {
        $teklif_obj = Teklifler::find($teklif_id);

        $data = [
            'teklif_id' => $teklif_id,
            'egitim_kayit_id' => $teklif_obj->egitim_kayit_id,
            'tur_id' => $tur_id,
            'prefix' => $this->_prefix,
            'ust_baslik' => $tur_id == 1 ? 'Evaluation Form' : 'Attendance Form',
            'alt_baslik' => $teklif_obj->egitimKayit->egitimler->adi,
            'liste' => TeklifFormlar::where('teklif_id', $teklif_id)
                ->where('tur_id', $tur_id)->get(),
            'data' => new TeklifFormlar()
        ];
        return view('teklifler.formlar.evaluation_view', $data);
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
    public function store(Request $request, $teklif_id)
    {
        try {
            $error_messages = [
                'aciklama.required' => 'Explanation is required.',
                'dosya.required' => 'Form file is required.',
                'dosya.mimes' => 'Form file type must be pdf,doc,docx',
                'dosya.max' => 'Form file max 2MB',
            ];
            $rules = [
                'aciklama' => 'required',
                'dosya' => 'required|mimes:pdf,doc,docx|max:2048',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ins_data = [
                'aciklama' => $request->input('aciklama'),
                'egitim_kayit_id' => $request->hid_egitim_kayit_id ?? null,
                'teklif_id' => $teklif_id,
                'tur_id' => $request->hid_tur_id
            ];
            if($request->file("dosya") != "") {
                $ins_data["dosya"] = $request->file("dosya")->store("public/teklif_formlar");
            }
            TeklifFormlar::create($ins_data);

            return redirect('/'.$this->_prefix."/".$teklif_id."/".$request->hid_tur_id)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\TeklifFormlar  $teklifFormlar
     * @return \Illuminate\Http\Response
     */
    public function show(TeklifFormlar $teklifFormlar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\TeklifFormlar  $teklifFormlar
     * @return \Illuminate\Http\Response
     */
    public function edit(TeklifFormlar $teklifFormlar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\TeklifFormlar  $teklifFormlar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeklifFormlar $teklifFormlar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\TeklifFormlar  $teklifFormlar
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeklifFormlar $teklifFormlar, $id)
    {
        try {
            $form = $teklifFormlar->find($id);
            Storage::delete($form->dosya);
            $teklifFormlar->destroy($id);
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
