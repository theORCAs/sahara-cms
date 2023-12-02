<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimKayitlar;
use App\Http\Models\EgitmenKursOneri;
use App\Http\Models\Moduller;
use App\Http\Models\Roller;
use App\Http\Models\StokUrunler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $moduller;
    public $aktif_modul_id;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('yetkiKontrol');

        $registration_form_sayi = $this->getRegistrationFormUyari();
        View::share("registration_form_sayi", $registration_form_sayi);
        $course_outline_sayi = $this->getNewCourseOutlineUyari();
        View::share("course_outline_sayi", $course_outline_sayi);
        $stok_uyari_sayi = $this->getStockModuleUyari()->count();
        View::share("stok_uyari_sayi", $stok_uyari_sayi);

        View::share("aktif_modul_id", $this->getAktifModuleId());

    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getAktifModuleId() {
        try {
            /*
            if(count(request()->segments()) > 1) {
                $tmp_menu_url = request()->segment(1)."/".request()->segment(2);
            } else {
                $tmp_menu_url = request()->segment(1);
            }
            */
            $tmp_menu_url = request()->segment(1);

            $table = Moduller::where("menu_url", $tmp_menu_url)->first();
            if (!is_null($table)) {
                return $table->id;
            }
        } catch (Exception $e) {
            return null;
        }

    }

    public function convertNumberToString($number) {
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $string = str_replace('Point', 'point', ucwords($f->format($number)));

        return response()->json([
            "string" => $string
        ]);

    }

    public function switchUserModal() {
        $roller = Roller::wherenull('deleted_at')
            ->orderby('adi', 'asc')
            ->get();
        $data = [
            'roller_listesi' => $roller
        ];
        return view('layouts.switchuser_modal', $data);
    }

    public function su_kullanicilariGetir(Request $request) {
        $kullanicilar = DB::select("select k.id, trim(ifnull(e.adi_soyadi, k.adi_soyadi)) adi_soyadi, e.unvan_id, u.adi unvan_adi, r.rol_id
            from kullanici_rolleri r
                left join kullanicilar k on k.id = r.kullanici_id
                left join egitmenler e on e.kullanici_id = k.id
                left join unvanlar u on u.id = e.unvan_id
            where r.rol_id = $request->rol_id
                and k.flg_durum = 1
            order by adi_soyadi asc");

        return response()->json($kullanicilar);
    }

    public function su_GecisYap(Request $request) {
        if(!session()->has('SW_KULLANICI_ID')) {
            session([
                'SW_KULLANICI_ID' => Auth::user()->id
            ]);
        }

        Auth::loginUsingId($request->kullanici_id);
        session(
            [
                'ROL_ID' => Auth::user()->kullaniciRolu()->rol_id,
                'ROL_ADI' => Auth::user()->kullaniciRolu()->rol_adi,
                'ADI_SOYADI' => Auth::user()->adi_soyadi,
                'KULLANICI_ID' => Auth::user()->id
            ]
        );
        Session::forget('moduller');
        return response()->json(['cvp' => 1]);
        // return redirect()->to('/');
    }

    public function switchUserKapat() {
        Auth::loginUsingId(session('SW_KULLANICI_ID'));
        session(
            [
                'ROL_ID' => Auth::user()->kullaniciRolu()->rol_id,
                'ROL_ADI' => Auth::user()->kullaniciRolu()->rol_adi,
                'ADI_SOYADI' => Auth::user()->adi_soyadi,
                'KULLANICI_ID' => Auth::user()->id
            ]
        );
        Session::forget('moduller');
        Session::forget('SW_KULLANICI_ID');
        return response()->json(['cvp' => 1]);
    }

    public function getRegistrationFormUyari() {
        return EgitimKayitlar::where("durum", 1)
            ->where("flg_silindi", 0)
            ->wherenull('deleted_at')
            ->whereHas('egitimTarihi', function (Builder $query) {
                $query->where('baslama_tarihi', '>=', date('Y-m-d'));
            })
            ->whereHas('aktifTeklif', function (Builder $query) {
                $query->where('durum', 1)
                    ->wherenull('teklif_gon_tarih');
            })
            ->orderBy("created_at", "desc")
            ->count();
    }

    public function getNewCourseOutlineUyari() {
        return EgitmenKursOneri::wherenull('deleted_at')
            ->where('durum', '=', '0')
            ->count();
    }

    public function getStockModuleUyari() {
        return StokUrunler::wherenull('st_urunler.deleted_at')
            ->leftjoin("st_stoklar", "st_stoklar.stok_urun_id", "=", "st_urunler.id")
            ->select("st_urunler.id", "st_urunler.adi")
            ->selectRaw("ifnull(st_urunler.uyari_limit, 0) as uyari_limit")
            ->selectRaw("sum(st_stoklar.giris - st_stoklar.cikis) as stok_durum")
            ->where('uyari_limit', '>', '0')
            ->groupby("st_urunler.id")
            ->having("uyari_limit", ">", "stok_durum")
            ->get()
            ;
    }


}
