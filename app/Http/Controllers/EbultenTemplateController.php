<?php

namespace App\Http\Controllers;

use App\Http\Models\EbultenTemplate;
use App\Http\Models\EbultenTemplateTurleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class EbultenTemplateController extends HomeController
{
    private $prefix = "em_messagetemplate";
    private $error_messages = array(
        'tur_id.required' => 'Message Related to is required.',
        'adi.required' => 'Message Title is required.',
        'icerik.required' => 'Message Body is required.',
    );
    private $rules = array(
        'tur_id' => 'required',
        'adi' => 'required',
        'icerik' => 'required',
    );
    private $tur_id;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('em_mt_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EbultenTemplate::orderby('tur_id', 'asc')
            ->orderby('created_at', 'desc');
        if($this->tur_id > 0)
            $liste = $liste->where('tur_id', $this->tur_id);

        $data = [
            'liste' => $liste->paginate(100),
            'prefix' => $this->prefix,
            'alt_baslik' => "Message Templates",
            'tur_id' => $this->tur_id,
            'tur_listesi' => EbultenTemplateTurleri::orderby('adi', 'asc')->get()
        ];
        return view('ebulten.template_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('em_mt_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Email Template",
            'data' => new EbultenTemplate(),
            'tur_listesi' => EbultenTemplateTurleri::orderby('adi', 'asc')->get()
        ];
        return view('ebulten.template_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('em_mt_add')) {
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

            EbultenTemplate::create([
                'tur_id' => $request->input('tur_id'),
                'adi' => $request->adi,
                'icerik' => $request->icerik,
                'cc_mails' => $request->cc_mails,
            ]);
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
     * @param  \App\Http\Models\EbultenTemplate  $ebultenTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(EbultenTemplate $ebultenTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EbultenTemplate  $ebultenTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(EbultenTemplate $ebultenTemplate, $id)
    {
        if(!Auth::user()->isAllow('em_mt_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Email Template",
            'data' => $ebultenTemplate->findorfail($id),
            'tur_listesi' => EbultenTemplateTurleri::orderby('adi', 'asc')->get()
        ];
        return view('ebulten.template_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EbultenTemplate  $ebultenTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbultenTemplate $ebultenTemplate, $id)
    {
        if(!Auth::user()->isAllow('em_mt_edit')) {
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

            $ebultenTemplate::findorfail($id)->update([
                'tur_id' => $request->input('tur_id'),
                'adi' => $request->adi,
                'icerik' => $request->icerik,
                'cc_mails' => $request->cc_mails,
            ]);
            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\EbultenTemplate  $ebultenTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbultenTemplate $ebultenTemplate, $id)
    {
        if(!Auth::user()->isAllow('sm_se_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $ebultenTemplate->destroy($id);
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

    public function search(Request $request) {
        $this->tur_id = $request->tur_id;

        return $this->index();
    }
}
