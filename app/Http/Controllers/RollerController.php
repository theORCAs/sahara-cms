<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Models\Roller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class RollerController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('wu_ut_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        /*
        $data = Roller::from("roller as r")
            // ->withTrashed()
            ->select("id", "adi",
                DB::raw("(select count(distinct k.id) from kullanici_rolleri kr left join kullanicilar k on k.id = kr.kullanici_id where kr.rol_id = r.id and k.flg_durum = 1 and k.deleted_at is null) as aktif_kullanici_sayisi"),
                DB::raw("(select count(distinct k.id) from kullanici_rolleri kr left join kullanicilar k on k.id = kr.kullanici_id where kr.rol_id = r.id and k.flg_durum = 0 and k.deleted_at is null) as pasif_kullanici_sayisi")
            )
            ->orderby("adi")
            ->get();
        */
        /*
        $data = Roller::select("id", "adi",
                DB::raw("(select count(distinct k.id) from kullanici_rolleri kr left join kullanicilar k on k.id = kr.kullanici_id where kr.rol_id = roller.id and k.flg_durum = 1) as aktif_kullanici_sayisi"),
                DB::raw("(select count(distinct k.id) from kullanici_rolleri kr left join kullanicilar k on k.id = kr.kullanici_id where kr.rol_id = roller.id and k.flg_durum = 0) as pasif_kullanici_sayisi")
            )
            ->orderby("roller.adi")
            ->get();
        */
        $data = Roller::orderby('adi')->get();
        return view("web_users.roller", ["roller" => $data]);
        // abort(403, 'Unauthorized action');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wu_ut_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $bilgi = new Roller;

        return view("web_users.rollerEdit", ["bilgi" => $bilgi]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('wu_ut_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $error_messages = array(
            'adi.required' => 'Role Type is required.'
        );
        $rules = array(
            'adi'       => 'required',
            //'email'      => 'required|email',
            //'nerd_level' => 'required|numeric'
        );
        $validator = Validator::make($request->all(), $rules, $error_messages);
        if ($validator->fails()) {
            return redirect('user_type/create')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        try {
            $obj = new Roller;
            $obj->adi = $request->input("adi");
            $obj->save();
            return redirect('/user_type')->with(["msj" => "Operation done successfully..."]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\app\Http\Models\Roller  $roller
     * @return \Illuminate\Http\Response
     */
    public function show(Roller $roller)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\app\Http\Models\Roller  $roller
     * @return \Illuminate\Http\Response
     */
    public function edit(Roller $roller, $id)
    {
        if(!Auth::user()->isAllow('wu_ut_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = Roller::find($id) ?? abort(403, "No such record found");

        return view("web_users.rollerEdit", ["bilgi" => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\app\Http\Models\Roller  $roller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Roller $roller, $id)
    {
        if(!Auth::user()->isAllow('wu_ut_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $error_messages = array(
            'adi.required' => 'Role Type is required.'
        );
        $rules = array(
            'adi'       => 'required',
            //'email'      => 'required|email',
            //'nerd_level' => 'required|numeric'
        );
        $validator = Validator::make($request->all(), $rules, $error_messages);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        try {
            $data = Roller::find($id);
            $data->adi = $request->input("adi");
            $result = $data->save();
            return redirect('/user_type')->with(["msj" => "Operation done successfully..."]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\app\Http\Models\Roller  $roller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Roller $roller, $id)
    {
        if(!Auth::user()->isAllow('wu_ut_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $roller->destroy($id);
            return redirect('/user_type')->with(["msj" => "Operation done successfully..."]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

    }
    public function yetki($id = 0){
        dd('ddd',$id);
    }
}
