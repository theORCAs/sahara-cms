<?php

namespace App\Http\Controllers;

use App\Http\Models\SystemSetup;
use Illuminate\Http\Request;
use Validator;
use Auth;

class SystemSetupController extends HomeController
{
    private $prefix = "ss_view";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('ss_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = SystemSetup::where("id", 1)->first();
        $data = [
            'data' => $liste,
            'alt_baslik' => "System Settings",
            'prefix' => $this->prefix
        ];
        return view('settings.sistem_ayarlar.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!Auth::user()->isAllow('ss_update')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $upd_data = [];
            if($request->file("imza_resmi") != "") {
                $upd_data["imza_resmi"] = $request->file("imza_resmi")->store("public/sistem_ayar");
            }
            if($request->file("header_resmi") != "") {
                $upd_data["header_resmi"] = $request->file("header_resmi")->store("public/sistem_ayar");
            }
            if(!empty($upd_data)) {
                SystemSetup::find($id)->update($upd_data);
            }


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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
