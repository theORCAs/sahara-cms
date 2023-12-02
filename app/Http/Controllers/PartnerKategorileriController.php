<?php

namespace App\Http\Controllers;

use App\Http\Models\PartnerKategorileri;
use Illuminate\Http\Request;
use Validator;
use Auth;

class PartnerKategorileriController extends HomeController
{
    private $prefix = "spm_kategori";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
        'sira.required' => 'Order number is required.',
        'sira.numeric' => 'The order number must be a number.',
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
        if(!Auth::user()->isAllow('spm_pc_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = PartnerKategorileri::wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Partner Categories'
        ];
        return view('office_management.payment_module.partnerkategori_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('spm_pc_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Payment Category",
            'data' => new PartnerKategorileri()
        ];
        return view('office_management.payment_module.partnerkategori_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('spm_pc_add')) {
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

            PartnerKategorileri::create([
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
     * @param  \App\Http\Models\PartnerKategorileri  $partnerKategorileri
     * @return \Illuminate\Http\Response
     */
    public function show(PartnerKategorileri $partnerKategorileri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\PartnerKategorileri  $partnerKategorileri
     * @return \Illuminate\Http\Response
     */
    public function edit(PartnerKategorileri $partnerKategorileri, $id)
    {
        if(!Auth::user()->isAllow('spm_pc_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Payment Category",
            'data' => $partnerKategorileri->findorfail($id)
        ];
        return view('office_management.payment_module.partnerkategori_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\PartnerKategorileri  $partnerKategorileri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PartnerKategorileri $partnerKategorileri, $id)
    {
        if(!Auth::user()->isAllow('spm_pc_edit')) {
            return redirect()
                ->back()
                ->withInput()
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

            $partnerKategorileri->find($id)->update([
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
     * @param  \App\Http\Models\PartnerKategorileri  $partnerKategorileri
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartnerKategorileri $partnerKategorileri, $id)
    {
        if(!Auth::user()->isAllow('spm_pc_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $partnerKategorileri->destroy($id);
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
