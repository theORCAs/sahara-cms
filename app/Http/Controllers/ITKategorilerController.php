<?php

namespace App\Http\Controllers;

use App\Http\Models\ITKategoriler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ITKategorilerController extends HomeController
{
    private $prefix = "jfu_category";
    private $error_messages = array(
        'adi.required' => 'Name is required.',
        'sira.required' => 'Order number is required.',
        'sira.numeric' => 'Order number must be number.',
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
        if(!Auth::user()->isAllow('jfm_cat_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = ITKategoriler::orderby('sira', 'asc')->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Job Categories'
        ];
        return view('office_management.istakip.kategoriler_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('jfm_cat_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Catgory",
            'data' => new ITKategoriler()
        ];
        return view('office_management.istakip.kategoriler_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('jfm_cat_add')) {
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

            ITKategoriler::create([
                'adi' => $request->input('adi'),
                'sira' => $request->input('sira'),
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
     * @param  \App\Http\Models\ITKategoriler  $iTKategoriler
     * @return \Illuminate\Http\Response
     */
    public function show(ITKategoriler $iTKategoriler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\ITKategoriler  $iTKategoriler
     * @return \Illuminate\Http\Response
     */
    public function edit(ITKategoriler $iTKategoriler, $id)
    {
        if(!Auth::user()->isAllow('jfm_cat_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Category",
            'data' => $iTKategoriler->findorfail($id)
        ];
        return view('office_management.istakip.kategoriler_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\ITKategoriler  $iTKategoriler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ITKategoriler $iTKategoriler, $id)
    {
        if(!Auth::user()->isAllow('jfm_cat_edit')) {
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

            $iTKategoriler::findorfail($id)->update([
                'adi' => $request->input('adi'),
                'sira' => $request->input('sira'),
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
     * @param  \App\Http\Models\ITKategoriler  $iTKategoriler
     * @return \Illuminate\Http\Response
     */
    public function destroy(ITKategoriler $iTKategoriler, $id)
    {
        if(!Auth::user()->isAllow('jfm_cat_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $iTKategoriler->destroy($id);
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
