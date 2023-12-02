<?php

namespace App\Http\Controllers;

use App\Http\Models\EbultenGonderim;
use App\Http\Models\EbultenGonderimGruplar;
use App\Http\Models\EbultenGruplar;
use App\Http\Models\EbultenTemplate;
use App\Http\Models\EbultenTemplateTurleri;
use App\Http\Models\Ulkeler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EbultenGonderimController extends HomeController
{
    private $prefix = "em_sendemail";
    private $error_messages = array(
        'template_tur_id.required' => 'Template Category is required.',
        'template_id.required' => 'Message to be sent is required.',
        'gonderim_tarihi.required' => 'Send Date is required.',
        'gonderim_tarihi.date_format' => 'Send Date must be day.month.year.',
    );
    private $rules = array(
        'template_tur_id' => 'required',
        'template_id' => 'required',
        'gonderim_tarihi' => 'required|date_format:d.m.Y',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('em_send_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EbultenGonderim::wherenull('deleted_at')
            ->orderby('created_at', 'desc')
            ->select('eb_gonderim.*')
            ->paginate(100);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Send Emails'
        ];
        return view('ebulten.gonderim_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('em_send_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $gonderim_data = new EbultenGonderim();
        $gonderim_data['gonderim_tarihi'] = date('Y-m-d');

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Email Send",
            'data' => $gonderim_data,
            'sablon_turleri' => EbultenTemplateTurleri::orderby('adi', 'asc')->get(),
            'grup1_listesi' => EbultenGruplar::where('flg_customer', '1')
                ->wherenotnull('dinamik_grup_id')
                ->orderby('adi', 'asc')
                ->get(),
            'grup2_listesi' => EbultenGruplar::where('flg_customer', '0')
                ->wherenotnull('dinamik_grup_id')
                ->orderby('adi', 'asc')
                ->get(),
            'grup3_listesi' => EbultenGruplar::wherenull('dinamik_grup_id')
                ->orderby('adi', 'asc')
                ->get(),
            'ulke_listesi' => Ulkeler::where('flg_aktif', 1)->orderby('adi', 'asc')->get()
        ];
        return view('ebulten.gonderim_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request->grup1;
        if(!Auth::user()->isAllow('em_send_add')) {
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

            $gonderim = EbultenGonderim::create([
                'template_tur_id' => $request->input('template_tur_id'),
                'template_id' => $request->template_id,
                'cc_mails' => $request->cc_mails,
                'ulke_id' => $request->ulke_id > 0 ? $request->ulke_id : null,
                'gonderim_tarihi' => date("Y-m-d", strtotime($request->gonderim_tarihi))
            ]);

            foreach($request->grup1 as $row) {
                EbultenGonderimGruplar::create([
                    'gonderim_id' => $gonderim->id,
                    'grup_id' => $row
                ]);
            }

            foreach($request->grup2 as $row) {
                EbultenGonderimGruplar::create([
                    'gonderim_id' => $gonderim->id,
                    'grup_id' => $row
                ]);
            }
            foreach($request->grup3 as $row) {
                EbultenGonderimGruplar::create([
                    'gonderim_id' => $gonderim->id,
                    'grup_id' => $row
                ]);
            }

            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\EbultenGonderim  $ebultenGonderim
     * @return \Illuminate\Http\Response
     */
    public function show(EbultenGonderim $ebultenGonderim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EbultenGonderim  $ebultenGonderim
     * @return \Illuminate\Http\Response
     */
    public function edit(EbultenGonderim $ebultenGonderim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EbultenGonderim  $ebultenGonderim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbultenGonderim $ebultenGonderim)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EbultenGonderim  $ebultenGonderim
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbultenGonderim $ebultenGonderim)
    {
        //
    }

    public function sablonGetirJson(Request $request) {
        $result = EbultenTemplate::where('tur_id', $request->template_tur_id)
            ->orderby('adi', 'asc')
            ->select('id', 'adi')
            ->get();

        return response()->json($result);
    }

    public function startSend($gonderim_id) {
        return $gonderim_id;
    }
}
