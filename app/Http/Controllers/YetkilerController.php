<?php

namespace App\Http\Controllers;

use App\Http\Models\Yetkiler;
use App\Http\Models\Moduller;
use App\Http\Models\Roller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YetkilerController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modul_listesi = DB::select("select a.id, 
                a.adi, 
                1 ana_kategori, 
                0 level1,
                0 level2, 
                concat(lpad(a.sirasi, 2, 0), '.00.00') level, 
                (select count(y.id) from yapi y where find_in_set(y.modul_id, getSubMenus(a.id))) yapi_sayisi
            from moduller a 
            where a.ana_modul_id is null
                and a.deleted_at is null 
                and a.flg_menude = 1 
            union 
            select b.id, 
                b.adi,
                0 ana_kategori, 
                1 level1, 
                0 level2, 
                concat(lpad(a.sirasi, 2, 0),'.', lpad(b.sirasi, 2, 0),'.00') level, 
                (select count(y.id) from yapi y where find_in_set(y.modul_id, getSubMenus(b.id))) yapi_sayisi
            from moduller a 
                left join moduller b on b.ana_modul_id = a.id 
            where a.ana_modul_id is null 
                and a.deleted_at is null 
                and a.flg_menude = 1 
                and b.deleted_at is null 
                and b.flg_menude = 1
            union 
            select c.id, 
                c.adi,
                0 ana_kategori, 
                0 level1, 
                1 level2, 
                concat(lpad(a.sirasi, 2, 0),'.', lpad(b.sirasi, 2, 0), '.', lpad(c.sirasi, 2, 0)) level, 
                (select count(y.id) from yapi y where find_in_set(y.modul_id, getSubMenus(c.id))) yapi_sayisi
            from moduller a 
                left join moduller b on b.ana_modul_id = a.id 
                left join moduller c on c.ana_modul_id = b.id 
            where a.ana_modul_id is null 
                and a.deleted_at is null 
                and a.flg_menude = 1 
                and b.deleted_at is null 
                and b.flg_menude = 1 
                and c.deleted_at is null
                and c.flg_menude = 1 
            order by level");

        $roller_listesi = Roller::wherenull('deleted_at')
            ->orderby('adi', 'asc')
            ->get();

        $data = [
            "modul_listesi" => $modul_listesi,
            "roller_listesi" => $roller_listesi
        ];

        return view('web_users.v_yetkilendirme', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $obj = new Yetkiler();
            $obj->rol_id = $request->input('rol_id');
            $obj->kullanici_id = $request->input('kullanici_id');
            $obj->yapi_id = $request->input('yapi_id');
            $obj->save();
            return response()->json([
                "cvp" => 1,
                "msj" => $obj->id
            ]);
        } catch(\Exception $e) {
            return response()->json([
                "cvp" => 0,
                "msj" => $e->getMessage()
            ]);
        }
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
     * @param  \App\Models\Yetkiler  $yetkiler
     * @return \Illuminate\Http\Response
     */
    public function show(Yetkiler $yetkiler)
    {
        $data["roller"] = Roller::get();
        $data["kullanicilar"] = User::wherenull("deleted_at")
            ->where("flg_durum", 1)
            ->get();
        $data["yapi_id"] = Request()->input("yapi_id");
        return view("web_users.yetkili_ekle_modal", $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Yetkiler  $yetkiler
     * @return \Illuminate\Http\Response
     */
    public function edit(Yetkiler $yetkiler)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Yetkiler  $yetkiler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Yetkiler $yetkiler)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Yetkiler  $yetkiler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Yetkiler $yetkiler, $id)
    {
        try {
            Yetkiler::destroy($id);
            return response()->json([
                "cvp" => 1,
                "msj" => ""
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "cvp" => 0,
                "msj" => $e->getMessage()
            ]);
        }
    }

    public function yapiListesiView(Request $request) {
        $data['modul_id'] = $request->modul_id;

        if($request->modul_id > 0) {
            $modul_bilgi = DB::select('select adi from moduller where id = :modul_id', ['modul_id' => $request->modul_id]);
            $data["modul_adi"] = $modul_bilgi[0]->adi;
            $data["yapi_listesi"] = DB::select("select id, adi, aciklama 
                from yapi where modul_id = :modul_id and deleted_at is null order by adi", ["modul_id" => $request->modul_id]);
        } else {
            $data["modul_adi"] = "General authorization structures (all site)";
            $data["yapi_listesi"] = DB::select("select id, adi, aciklama from yapi where modul_id is null and deleted_at is null order by adi");
        }
        return view('web_users.v_yetkilendime_yapi', $data);
    }

    public function yetkiliListesiView(Request $request) {
        $data['yapi_id'] = $request->yapi_id;
        $data["yetkili_listesi"] = DB::select('SELECT y.id, r.adi rol_adi, 1 sira  
                FROM yetkiler y 
                    left join roller r on r.id = y.rol_id 
                where y.yapi_id = :yapi_id
                    and y.kullanici_id is null
                union all 
                select y.id, u.adi_soyadi, 2 sira
                from yetkiler y 
                    left join kullanicilar u on u.id = y.kullanici_id
                where y.yapi_id = :yapi_id1 
                    and y.kullanici_id is not null
                order by sira', ['yapi_id' => $request->yapi_id, 'yapi_id1' => $request->yapi_id]);
        return view('web_users.v_yetkilendirme_yetkililer', $data);
    }

    public function yapiEkleModal(Request $request)
    {
        if ($request->modul_id > 0) {
            $sonuc = Moduller::where("id", $request->modul_id)->first();
            $data = [
                'modul_id' => $sonuc->id,
                'modul_adi' => $sonuc->adi
            ];
        } else {
            $data = [
                'modul_id' => '',
                'modul_adi' => 'General authorization structures (all site)'
            ];
        }
        return view('web_users.yapi_ekle_modal', $data);
    }
}
