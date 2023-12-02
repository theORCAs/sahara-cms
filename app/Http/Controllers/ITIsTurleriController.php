<?php

namespace App\Http\Controllers;

use App\Http\Models\ITIsTurleri;
use App\Http\Models\ITKategoriler;
use App\Http\Models\ITTekrarTurleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ITIsTurleriController extends HomeController
{
    private $prefix = "jfu_jobtypes";
    private $error_messages = array(
        'kategori_id.required' => 'Category is required.',
        'adi.required' => 'Name is required.',
        'tekrar_id.required' => 'Repeat Sequence is required.',
    );
    private $rules = array(
        'kategori_id' => 'required',
        'adi' => 'required',
        'tekrar_id' => 'required',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('jfm_type_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = ITIsTurleri::leftjoin('it_kategori', 'it_kategori.id', '=', 'it_isturleri.kategori_id')
            ->leftjoin('it_tekrar_turleri', 'it_tekrar_turleri.id', '=', 'it_isturleri.tekrar_id')
            ->select('it_kategori.adi as kategori_adi', 'it_tekrar_turleri.adi as tekrar_turu', 'it_isturleri.*')
            ->orderby('it_tekrar_turleri.sira', 'asc')
            ->orderby('it_tekrar_turleri.adi', 'asc')
            ->paginate(20);

        //return $liste;
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Job Types'
        ];
        return view('office_management.istakip.isturleri_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('jfm_type_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Job Types",
            'data' => new ITIsTurleri(),
            'kategori_listesi' => ITKategoriler::orderby('sira', 'asc')->orderby('adi', 'asc')->get(),
            'tekrar_listesi' => ITTekrarTurleri::orderby('sira', 'asc')->orderby('adi', 'asc')->get()
        ];
        return view('office_management.istakip.isturleri_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('jfm_type_add')) {
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

            ITIsTurleri::create([
                'kategori_id' => $request->input('kategori_id'),
                'adi' => $request->input('adi'),
                'aciklama' => $request->aciklama,
                'tekrar_id' => $request->tekrar_id,
                'flg_harici' => intval($request->flg_harici),
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
     * @param  \App\Http\Models\ITIsTurleri  $iTIsTurleri
     * @return \Illuminate\Http\Response
     */
    public function show(ITIsTurleri $iTIsTurleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\ITIsTurleri  $iTIsTurleri
     * @return \Illuminate\Http\Response
     */
    public function edit(ITIsTurleri $iTIsTurleri, $id)
    {
        if(!Auth::user()->isAllow('jfm_type_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Job Type",
            'data' => $iTIsTurleri->findorfail($id),
            'kategori_listesi' => ITKategoriler::orderby('sira', 'asc')->orderby('adi', 'asc')->get(),
            'tekrar_listesi' => ITTekrarTurleri::orderby('sira', 'asc')->orderby('adi', 'asc')->get()
        ];
        return view('office_management.istakip.isturleri_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\ITIsTurleri  $iTIsTurleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ITIsTurleri $iTIsTurleri, $id)
    {
        if(!Auth::user()->isAllow('jfm_type_edit')) {
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

            $iTIsTurleri::findorfail($id)->update([
                'kategori_id' => $request->input('kategori_id'),
                'adi' => $request->input('adi'),
                'aciklama' => $request->aciklama,
                'tekrar_id' => $request->tekrar_id,
                'flg_harici' => intval($request->flg_harici),
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
     * @param  \App\Http\Models\ITIsTurleri  $iTIsTurleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(ITIsTurleri $iTIsTurleri, $id)
    {
        if(!Auth::user()->isAllow('jfm_type_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $iTIsTurleri->destroy($id);
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
