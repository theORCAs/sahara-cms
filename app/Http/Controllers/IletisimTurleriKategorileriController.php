<?php

namespace App\Http\Controllers;

use App\Http\Models\IletisimTurleriKategorileri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class IletisimTurleriKategorileriController extends HomeController
{
    private $prefix = "pu_csc_view";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('pu_csc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = IletisimTurleriKategorileri::wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Contacting Status Category"
        ];
        return view('settings.iletisim_tur.kategori_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('pu_csc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Contacting Status Category",
            'data' => new IletisimTurleriKategorileri()
        ];
        return view('settings.iletisim_tur.kategori_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('pu_csc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Country name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
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

            IletisimTurleriKategorileri::create([
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
     * @param  \App\Http\Models\IletisimTurleriKategorileri  $iletisimTurleriKategorileri
     * @return \Illuminate\Http\Response
     */
    public function show(IletisimTurleriKategorileri $iletisimTurleriKategorileri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\IletisimTurleriKategorileri  $iletisimTurleriKategorileri
     * @return \Illuminate\Http\Response
     */
    public function edit(IletisimTurleriKategorileri $iletisimTurleriKategorileri, $id)
    {
        if(!Auth::user()->isAllow('pu_csc_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Contacting Status Category",
            'data' => $iletisimTurleriKategorileri->findorfail($id)
        ];
        return view('settings.iletisim_tur.kategori_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\IletisimTurleriKategorileri  $iletisimTurleriKategorileri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IletisimTurleriKategorileri $iletisimTurleriKategorileri, $id)
    {
        if(!Auth::user()->isAllow('pu_csc_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Country name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
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

            $iletisimTurleriKategorileri->find($id)
                ->update([
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
     * @param  \App\Http\Models\IletisimTurleriKategorileri  $iletisimTurleriKategorileri
     * @return \Illuminate\Http\Response
     */
    public function destroy(IletisimTurleriKategorileri $iletisimTurleriKategorileri, $id)
    {
        if(!Auth::user()->isAllow('pu_csc_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $iletisimTurleriKategorileri->find($id)
                ->update([
                    "deleted_at" => date("Y-m-d H:i:s")
                ]);

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
