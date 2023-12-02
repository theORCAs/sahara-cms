<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class YetkiKontrol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(false && session()->has('moduller')) {
            $this->moduller = session('moduller');
        } else {
            if(session()->has('KULLANICI_ID')) {
                DB::statement("SET SESSION group_concat_max_len = 1000000");
                $query = "
                    select *
                    from (
                        select concat(lpad(a.sirasi, 2, 0), '.00.00') level,
                            1 as ana_kategori,
                            0 as level1,
                            0 as level2,
                            a.id, a.adi, a.sirasi, a.menu_url, a.icon,
                            (select count(bb.id) from moduller bb where bb.ana_modul_id = a.id and bb.flg_menude = 1) sub_menu,
                            getSubMenus(a.id) sub_menu_ids,
                            (
                                select count(yt.id)
                                from yapi y
									left join yetkiler yt on yt.yapi_id = y.id
								where find_in_set(y.modul_id, getSubMenus(a.id))
									and (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
                            ) yetki_sayi
                        from moduller a
                        where a.ana_modul_id is null
                            and a.flg_menude = 1
                        union all
                        select concat(lpad(a.sirasi, 2, 0), '.', lpad(b.sirasi, 2, 0), '.00') level,
                            0 as ana_kategori,
                            1 as level1,
                            0 as level2,
                            b.id, b.adi, b.sirasi, b.menu_url, b.icon,
                            (select count(bb.id) from moduller bb where bb.ana_modul_id = b.id and bb.flg_menude = 1) sub_menu,
                            getSubMenus(b.id) sub_menu_ids,
                            (
                                /*
                                select count(yt.id)
                                from yapi y
									left join yetkiler yt on yt.yapi_id = y.id
								where find_in_set(y.modul_id, getSubMenus(b.id))
									and (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
                                */
                                /*
                                select find_in_set(b.id, group_concat(m.id, ',', m.ana_modul_id))
								from yetkiler yt
									left join yapi y on yt.yapi_id = y.id
									left join moduller m on m.id = y.modul_id
								where (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
								*/
								select find_in_set(b.id, group_concat(tbl.tmp_con))
                                from (
									select concat(ifnull(m.id, 0), ',', ifnull(m.ana_modul_id, 0)) tmp_con
									from yetkiler yt
										left join yapi y on yt.yapi_id = y.id
										left join moduller m on m.id = y.modul_id
									where (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
									group by tmp_con
								) tbl
                            ) yetki_sayi
                        from moduller a
                            left join moduller b on b.ana_modul_id = a.id
                        where a.ana_modul_id is null
                            and a.flg_menude = 1
                            and b.flg_menude = 1
                            and b.id is not null

                        union all
                        select concat(lpad(a.sirasi, 2, 0), '.', lpad(b.sirasi, 2, 0), '.', lpad(c.sirasi, 2, 0)) level,
                            0 as ana_kategori,
                            0 as level1,
                            1 as level2,
                            c.id, c.adi, c.sirasi, c.menu_url, c.icon,
                            (select count(bb.id) from moduller bb where bb.ana_modul_id = c.id and bb.flg_menude = 1) sub_menu,
                            getSubMenus(c.id) sub_menu_ids,
                            (
                                /*
                                select count(yt.id)
                                from yapi y
									left join yetkiler yt on yt.yapi_id = y.id
								where find_in_set(y.modul_id, getSubMenus(c.id))
									and (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
                                */
                                /*
                                select find_in_set(c.id, group_concat(m.id, ',', m.ana_modul_id))
								from yetkiler yt
									left join yapi y on yt.yapi_id = y.id
									left join moduller m on m.id = y.modul_id
								where (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
								*/
								select find_in_set(c.id, group_concat(tbl.tmp_con))
                                from (
									select concat(ifnull(m.id, 0), ',', ifnull(m.ana_modul_id, 0)) tmp_con
									from yetkiler yt
										left join yapi y on yt.yapi_id = y.id
										left join moduller m on m.id = y.modul_id
									where (yt.rol_id = '" . session('ROL_ID') . "' or yt.kullanici_id = '" . session('KULLANICI_ID') . "')
									group by tmp_con
								) tbl
                            ) yetki_sayi
                        from moduller a
                            left join moduller b on b.ana_modul_id = a.id
                            left join moduller c on c.ana_modul_id = b.id
                        where a.ana_modul_id is null
                            and a.flg_menude = 1
                            and b.flg_menude = 1
                            and c.flg_menude = 1
                            and c.id is not null
                    ) tbl
                    having tbl.yetki_sayi > 0
                    order by tbl.level
                ";
                //die($query);
                $this->moduller = DB::select($query);

            }

            //print_r($this->moduller);
        }

        session([
            "moduller" => $this->moduller,
            // "aktif_modul_id" => $this->getAktifModuleId()
        ]);
        //Log::error('deneme2 : '.session('KULLANICI_ID'));

        //return $next($request);

        // Log::error($request->user()->adi_soyadi." ".session('ROL_ID'));
        $tmp_path = explode("/", $request->path());
        if($tmp_path[0] != "") {
            $modul = DB::select("select count(m.id) modul_mu from moduller m where m.menu_url = '$tmp_path[0]'");
            if ($modul[0]->modul_mu > 0) {

                /**
                 * select m.id, m.adi,
                (
                select count(yt.id) from yetkiler yt
                where yt.yapi_id in  ( select group_concat(y.id) from yapi y where find_in_set(y.modul_id, getSubMenus(m.id)) )
                and (yt.rol_id = " . session('ROL_ID') . " or yt.kullanici_id = " . $request->user()->id . ")
                ) yetki_sayi
                from moduller m
                where m.menu_url = '$tmp_path[0]'
                and m.flg_menude = 1
                 */
                $yetki_kontrol = DB::select("select m.id, m.adi,
                            (
                                select count(yt.id)
                                from yapi y
                                    left join yetkiler yt on yt.yapi_id = y.id
                                where find_in_set(y.modul_id, getSubMenus(m.id))
                                    and (yt.rol_id = " . session('ROL_ID') . " or yt.kullanici_id = " . $request->user()->id . ")
                            ) yetki_sayi
                        from moduller m
                        where m.menu_url = '$tmp_path[0]'
                            and m.flg_menude = 1");

                if (sizeof($yetki_kontrol) > 0 && intval($yetki_kontrol[0]->yetki_sayi) > 0)
                    return $next($request);
            } else {
                return $next($request);
            }
        } else {
            return $next($request);
        }
        // return $next($request);
        Log::error("UserName: ".$request->user()->adi_soyadi." UserID: ".$request->user()->id." ROL_ID: ".session('ROL_ID')." PATH: ".$tmp_path[0]);
        return abort(401, 'This action is unauthorized.');
    }
}
