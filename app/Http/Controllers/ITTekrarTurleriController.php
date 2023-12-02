<?php

namespace App\Http\Controllers;

use App\Http\Models\ITTekrarTurleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ITTekrarTurleriController extends HomeController
{
    private $prefix = "jfu_frequency";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
        'sira.required' => 'Order is required.',
        'sira.numeric' => 'Order must be number',
    );
    private $rules = array(
        'adi' => 'required',
        'sira' => 'required|numeric',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('jfm_fre_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = ITTekrarTurleri::orderby('sira', 'asc')
            ->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Job Frequencies'
        ];
        return view('office_management.istakip.tekrarturleri_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('jfm_fre_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Job Frequencies",
            'data' => new ITTekrarTurleri()
        ];
        return view('office_management.istakip.tekrarturleri_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('jfm_fre_add')) {
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

            ITTekrarTurleri::create([
                'adi' => $request->input('adi'),
                'sira' => $request->sira
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
     * @param  \App\Http\Models\ITTekrarTurleri  $iTTekrarTurleri
     * @return \Illuminate\Http\Response
     */
    public function show(ITTekrarTurleri $iTTekrarTurleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\ITTekrarTurleri  $iTTekrarTurleri
     * @return \Illuminate\Http\Response
     */
    public function edit(ITTekrarTurleri $iTTekrarTurleri, $id)
    {
        if(!Auth::user()->isAllow('jfm_fre_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Job Frequencies",
            'data' => $iTTekrarTurleri->findorfail($id)
        ];
        return view('office_management.istakip.tekrarturleri_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\ITTekrarTurleri  $iTTekrarTurleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ITTekrarTurleri $iTTekrarTurleri, $id)
    {
        if(!Auth::user()->isAllow('jfm_fre_edit')) {
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

            $iTTekrarTurleri::findorfail($id)->update([
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
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
     * @param  \App\Http\Models\ITTekrarTurleri  $iTTekrarTurleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(ITTekrarTurleri $iTTekrarTurleri, $id)
    {
        if(!Auth::user()->isAllow('jfm_fre_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $iTTekrarTurleri->destroy($id);
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
