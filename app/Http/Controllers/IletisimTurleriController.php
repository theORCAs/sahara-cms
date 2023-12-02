<?php

namespace App\Http\Controllers;

use App\Http\Models\IletisimTurleri;
use App\Http\Models\IletisimTurleriKategorileri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class IletisimTurleriController extends HomeController
{
    private $prefix = "pu_cs_view";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('pu_cs_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = IletisimTurleri::wherenull('iletisim_turleri.deleted_at')
            ->leftjoin('iletisim_turleri_kategorileri', 'iletisim_turleri_kategorileri.id', '=', 'iletisim_turleri.kategori_id')
            ->orderby('iletisim_turleri_kategorileri.sira', 'asc')
            ->orderby('iletisim_turleri_kategorileri.adi', 'asc')
            ->orderby('iletisim_turleri.sira', 'asc')
            ->orderby('iletisim_turleri.adi', 'asc')
            ->select('iletisim_turleri.*')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Contacting Status"
        ];
        return view('settings.iletisim_tur.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('pu_cs_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Contacting Status",
            'data' => new IletisimTurleri(),
            'kategori_liste' => IletisimTurleriKategorileri::wherenull('deleted_at')->orderby('sira', 'asc')->get()
        ];
        return view('settings.iletisim_tur.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('pu_cs_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'kategori_id' => 'Category must be selected',
                'adi.required' => 'Country name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'kategori_id' => 'required',
                'adi' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {


                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            IletisimTurleri::create([
                'kategori_id' => $request->kategori_id,
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
     * @param  \App\Http\Models\IletisimTurleri  $iletisimTurleri
     * @return \Illuminate\Http\Response
     */
    public function show(IletisimTurleri $iletisimTurleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\IletisimTurleri  $iletisimTurleri
     * @return \Illuminate\Http\Response
     */
    public function edit(IletisimTurleri $iletisimTurleri, $id)
    {
        if(!Auth::user()->isAllow('pu_cs_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Contacting Status",
            'data' => $iletisimTurleri->findorfail($id),
            'kategori_liste' => IletisimTurleriKategorileri::wherenull('deleted_at')->orderby('sira', 'asc')->get()
        ];
        return view('settings.iletisim_tur.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\IletisimTurleri  $iletisimTurleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IletisimTurleri $iletisimTurleri, $id)
    {
        if(!Auth::user()->isAllow('pu_cs_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'kategori_id' => 'Category must be selected',
                'adi.required' => 'Country name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'kategori_id' => 'required',
                'adi' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $iletisimTurleri->find($id)
                ->update([
                    'kategori_id' => $request->kategori_id,
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\IletisimTurleri  $iletisimTurleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(IletisimTurleri $iletisimTurleri, $id)
    {
        if(!Auth::user()->isAllow('pu_cs_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        $iletisimTurleri->find($id)
            ->update([
                "deleted_at" => date("Y-m-d H:i:s")
            ]);

        return redirect()
            ->back()
            ->with('msj', config('messages.islem_basarili'));
    }
}
