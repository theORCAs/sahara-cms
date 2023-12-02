<?php

namespace App\Http\Controllers;

use App\Http\Models\WSMenuler;
use App\Http\Models\WSModuller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

class WSMenulerController extends HomeController
{
    private $prefix = "ws_menuler";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('wso_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = DB::select("select *
                from (
                    select a.*, concat(lpad(a.sira, 2, '0'), '00') leveli, 1 menu1, 0 menu2
                    from ws_menu a
                    where a.ana_menu_id is null
                        and flg_navigasyon = 0
                    -- order by a.sira
                    union
                    select b.*, concat(lpad(a.sira, 2, '0'), lpad(b.sira, 2, '0')) leveli, 0 menu1, 1 menu2
                    from ws_menu a
                        left join ws_menu b on b.ana_menu_id = a.id
                    where a.ana_menu_id is null
                        and a.flg_navigasyon = 0
                        and b.id is not null
                    -- order by b.sira
                ) tbl
                order by tbl.leveli");
        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Web Sites Menus"
        ];
        return view('website.menu_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wso_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Website Menu",
            'data' => new WSMenuler(),
            'menuler_listesi' => WSMenuler::wherenull('deleted_at')->wherenull('ana_menu_id')->where('flg_navigasyon', '0')->where('flg_aktif', '1')->where('flg_inmenu', '1')->orderby('sira', 'asc')->get(),
            'moduller_listesi' => WSModuller::wherenull('deleted_at')->where('flg_aktif', '1')->where('flg_external', '1')->orderby('adi', 'asc')->get()
        ];
        return view('website.menu_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('wso_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Title is required.',
                'shortcut.required' => 'Shortcut is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'shortcut' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            WSMenuler::create([
                'adi' => $request->input('adi'),
                'shortcut' => $request->input('shortcut'),
                'ana_menu_id' => $request->ana_menu_id > 0 ? $request->ana_menu_id : null,
                'link' => $request->link,
                'link_target' => $request->link_target,
                'flg_navigasyon' => intval($request->flg_navigasyon),
                'flg_aktif' => intval($request->flg_aktif),
                'flg_inmain' => intval($request->flg_inmain),
                'flg_inmenu' => intval($request->flg_inmenu),
                'flg_uyeozel' => intval($request->flg_uyeozel),
                'ws_modul_id' => $request->ws_modul_id > 0 ? $request->ws_modul_id : null,
                'icerik' => $request->icerik,
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
     * @param  \App\Http\Models\WSMenuler  $wSMenuler
     * @return \Illuminate\Http\Response
     */
    public function show(WSMenuler $wSMenuler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\WSMenuler  $wSMenuler
     * @return \Illuminate\Http\Response
     */
    public function edit(WSMenuler $wSMenuler, $id)
    {
        if(!Auth::user()->isAllow('wso_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Website Menu",
            'data' => $wSMenuler->findorfail($id),
            'menuler_listesi' => WSMenuler::wherenull('deleted_at')->wherenull('ana_menu_id')->where('flg_navigasyon', '0')->where('flg_aktif', '1')->where('flg_inmenu', '1')->orderby('sira', 'asc')->get(),
            'moduller_listesi' => WSModuller::wherenull('deleted_at')->where('flg_aktif', '1')->where('flg_external', '1')->orderby('adi', 'asc')->get()
        ];
        return view('website.menu_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\WSMenuler  $wSMenuler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WSMenuler $wSMenuler, $id)
    {
        if(!Auth::user()->isAllow('wso_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Title is required.',
                'shortcut.required' => 'Shortcut is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'shortcut' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $wSMenuler->find($id)
                ->update([
                    'adi' => $request->input('adi'),
                    'shortcut' => $request->input('shortcut'),
                    'ana_menu_id' => $request->ana_menu_id > 0 ? $request->ana_menu_id : null,
                    'link' => $request->link,
                    'link_target' => $request->link_target,
                    'flg_navigasyon' => intval($request->flg_navigasyon),
                    'flg_aktif' => intval($request->flg_aktif),
                    'flg_inmain' => intval($request->flg_inmain),
                    'flg_inmenu' => intval($request->flg_inmenu),
                    'flg_uyeozel' => intval($request->flg_uyeozel),
                    'ws_modul_id' => $request->ws_modul_id > 0 ? $request->ws_modul_id : null,
                    'icerik' => $request->icerik,
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
     * @param  \App\Http\Models\WSMenuler  $wSMenuler
     * @return \Illuminate\Http\Response
     */
    public function destroy(WSMenuler $wSMenuler, $id)
    {
        if(!Auth::user()->isAllow('wso_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $wSMenuler->destroy($id);

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
