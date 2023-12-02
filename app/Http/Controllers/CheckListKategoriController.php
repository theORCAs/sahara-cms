<?php

namespace App\Http\Controllers;

use App\Http\Models\CheckListKategori;
use Illuminate\Http\Request;
use Validator;
use Auth;

class CheckListKategoriController extends HomeController
{
    private $prefix = "chkl_kategori";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('clm_clc_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = CheckListKategori::wherenull('deleted_at')
            ->orderby('flg_aktif', 'desc')
            ->orderby('sira', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Check List Categories'
        ];
        return view('office_management.checklist.kategori_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('clm_clc_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Check List Category",
            'data' => new CheckListKategori()
        ];
        return view('office_management.checklist.kategori_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('clm_clc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'Order number must be number',
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

            CheckListKategori::create([
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
     * @param  \App\Http\Models\CheckListKategori  $checkListKategori
     * @return \Illuminate\Http\Response
     */
    public function show(CheckListKategori $checkListKategori)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\CheckListKategori  $checkListKategori
     * @return \Illuminate\Http\Response
     */
    public function edit(CheckListKategori $checkListKategori, $id)
    {
        if(!Auth::user()->isAllow('clm_clc_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Checklist Category",
            'data' => $checkListKategori->findorfail($id)
        ];
        return view('office_management.checklist.kategori_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\CheckListKategori  $checkListKategori
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CheckListKategori $checkListKategori, $id)
    {
        if(!Auth::user()->isAllow('clm_clc_edit')) {
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

            $checkListKategori->findorfail($id)->update([
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
     * @param  \App\Http\Models\CheckListKategori  $checkListKategori
     * @return \Illuminate\Http\Response
     */
    public function destroy(CheckListKategori $checkListKategori, $id)
    {
        if(!Auth::user()->isAllow('clm_clc_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $checkListKategori->destroy($id);
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
