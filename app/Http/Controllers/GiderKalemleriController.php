<?php

namespace App\Http\Controllers;

use App\Http\Models\GiderKalemleri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class GiderKalemleriController extends HomeController
{
    private $prefix = "aca_expensetype";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('ama_et_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = GiderKalemleri::wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->paginate(100);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Expense Types"
        ];
        return view('admin_operation.hesap_modul.giderkalem_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('ama_et_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New expense types",
            'data' => new GiderKalemleri()
        ];
        return view('admin_operation.hesap_modul.gelirkalem_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('ama_et_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numberic' => 'Order number must be number',
            );
            $rules = array(
                'adi' => 'required',
                'sira' => 'required|numeric',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            GiderKalemleri::create([
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
     * Display the specified resource.
     *
     * @param  \App\Http\Models\GiderKalemleri  $giderKalemleri
     * @return \Illuminate\Http\Response
     */
    public function show(GiderKalemleri $giderKalemleri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\GiderKalemleri  $giderKalemleri
     * @return \Illuminate\Http\Response
     */
    public function edit(GiderKalemleri $giderKalemleri, $id)
    {
        if(!Auth::user()->isAllow('ama_et_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Expense Type",
            'data' => $giderKalemleri->findorfail($id)
        ];
        return view('admin_operation.hesap_modul.giderkalem_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\GiderKalemleri  $giderKalemleri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GiderKalemleri $giderKalemleri, $id)
    {
        if(!Auth::user()->isAllow('ama_et_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numberic' => 'Order number must be number',
            );
            $rules = array(
                'adi' => 'required',
                'sira' => 'required|numeric',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $giderKalemleri->findorfail($id)->update([
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
     * @param  \App\Http\Models\GiderKalemleri  $giderKalemleri
     * @return \Illuminate\Http\Response
     */
    public function destroy(GiderKalemleri $giderKalemleri, $id)
    {
        if(!Auth::user()->isAllow('ama_et_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $giderKalemleri->destroy($id);
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
