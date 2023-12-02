<?php

namespace App\Http\Controllers;

use App\Http\Models\Sss;
use Illuminate\Http\Request;
use Validator;
use Auth;

class SssController extends HomeController
{
    private $prefix = "faq_view";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('wmo_faq_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = Sss::wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->paginate(20);
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Frequently Asked Questions"
        ];
        return view('website.sss.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wmo_faq_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
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
        if(!Auth::user()->isAllow('wmo_faq_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'soru.required' => 'Question is required.',
                'cevap.required' => 'Answer is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'soru' => 'required',
                'cevap' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Sss::create([
                'soru' => $request->input('soru'),
                'cevap' => $request->input('cevap'),
                'sira' => $request->sira,
                'flg_aktif' => intval($request->flg_aktif)
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
     * @param  \App\Http\Models\Sss  $sss
     * @return \Illuminate\Http\Response
     */
    public function show(Sss $sss)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Sss  $sss
     * @return \Illuminate\Http\Response
     */
    public function edit(Sss $sss, $id)
    {
        if(!Auth::user()->isAllow('wmo_faq_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit FAQ",
            'data' => $sss->findorfail($id)
        ];
        return view('website.sss.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Sss  $sss
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sss $sss, $id)
    {
        if(!Auth::user()->isAllow('wmo_faq_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'soru.required' => 'Question is required.',
                'cevap.required' => 'Answer is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'soru' => 'required',
                'cevap' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $sss->find($id)
                ->update([
                    'soru' => $request->input('soru'),
                    'cevap' => $request->input('cevap'),
                    'sira' => $request->sira,
                    'flg_aktif' => intval($request->flg_aktif)
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
     * @param  \App\Http\Models\Sss  $sss
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sss $sss, $id)
    {
        if(!Auth::user()->isAllow('wmo_faq_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $sss->destroy($id);

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
