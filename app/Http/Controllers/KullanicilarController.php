<?php

namespace App\Http\Controllers;

use App\Http\Models\KullaniciRolleri;
use App\Http\Models\Roller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

class KullanicilarController extends HomeController
{
    private $error_messages = array(
        'rol_id.required' => 'User Type is required.',
        'adi_soyadi.required' => 'Name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Email must be valid.',
        'sifre.required_if' => 'Password is required.',
        'sifre_tekrar.same' => 'Repeat Password is required.',
    );
    private $rules = array(
        'rol_id' => 'required',
        'adi_soyadi' => 'required',
        'email' => 'required|email',
        'sifre' => 'required_if:hid_guncelleme,0',
        'sifre_tekrar' => "required_with:sifre|same:sifre",
    );
    private $liste;
    private $alt_baslik;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roller = Roller::all();
        $data = [
            'roller_listesi' => $roller,
            'filtre_rol_id' => session('FILTRE_ROL_ID'),
            'filtre_kullanici_id' => session('FILTRE_KULLANICI_ID') ?? null,
            'liste' => $this->liste,
            'prefix' => session('PREFIX'),
            'alt_baslik' => $this->alt_baslik,
        ];
        return view('web_users.user_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('wu_as_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Add New Web User",
            'data' => new User(),
            'roller' => Roller::orderby('adi', 'asc')
                ->wherenotin('id', [3, 4, 5])
                ->select('id', 'adi')->get()
        ];
        return view('web_users.user_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('wu_as_add')) {
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

            $user = User::create([
                'adi_soyadi' => $request->input('adi_soyadi'),
                'email' => $request->email,
                'sifre' => md5($request->sifre),
                'flg_durum' => intval($request->flg_durum),
            ]);

            KullaniciRolleri::create([
                'kullanici_id' => $user->id,
                'rol_id' => $request->rol_id
            ]);
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
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
    public function edit(KullaniciRolleri $kullaniciRolleri, $id)
    {
        if(!Auth::user()->isAllow('wu_as_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => session('PREFIX'),
            'alt_baslik' => "Add New Web User",
            'data' => $kullaniciRolleri->where('kullanici_rolleri.id', $id)
                ->leftjoin('kullanicilar', 'kullanici_rolleri.kullanici_id', '=', 'kullanicilar.id')
                ->select('kullanicilar.*', 'kullanici_rolleri.rol_id', 'kullanici_rolleri.id as id')
                ->first(),
            'roller' => Roller::orderby('adi', 'asc')
                ->wherenotin('id', [3, 4, 5])
                ->select('id', 'adi')->get()
        ];
        return view('web_users.user_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KullaniciRolleri $kullaniciRolleri, $id)
    {
        if(!Auth::user()->isAllow('wu_as_edit')) {
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

            $kk = $kullaniciRolleri->find($id);
            $kk->update([
                    'rol_id' => $request->input('rol_id'),
                ]);
            User::where('id', $kk->kullanici_id)
                ->update([
                    'adi_soyadi' => $request->input('adi_soyadi'),
                    'email' => $request->email,
                    'flg_durum' => intval($request->flg_durum),
                ]);
            if($request->sifre != "")
                User::where('id', $kk->kullanici_id)
                    ->update([
                        'sifre' => md5($request->sifre),
                    ]);
            return redirect('/'.session('PREFIX'))->with(["msj" => config("messages.islem_basarili")]);
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
    public function destroy(KullaniciRolleri $kullaniciRolleri, $id)
    {
        if(!Auth::user()->isAllow('wu_as_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $kk = $kullaniciRolleri->find($id);
            User::destroy($kk->kullanici_id);
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

    public function viewActiveUser(Request $request) {
        //$data = User::where("flg_durum", 1)->get();
        /*
        $data = DB::select("select k.id, r.adi rolu, k.adi_soyadi, k.email,
                    trim(concat(e.cep_tel_kod, ' ', e.cep_tel)) cep_tel,
                    ifnull((select date_format(g.created_at, '%d.%m.%Y %H:%i') from kullanici_girisler g where g.kullanici_id = k.id and g.durum = 1 order by g.id desc limit 1), 'Never Logged in') last_login
                from kullanicilar k
                    left join kullanici_rolleri kr on kr.kullanici_id = k.id
                    left join roller r on r.id = kr.rol_id
                    left join egitmenler e on e.kullanici_id = k.id
                where k.flg_durum = 1
                order by r.adi, k.adi_soyadi");
        return view("web_users.v_activeusers", ["kullanicilar" => $data, "etiket" => "ACTIVE USERS LIST"]);
        */

        if(!Auth::user()->isAllow('wu_as_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = User::where('kullanicilar.flg_durum', 1)
            ->leftjoin('kullanici_rolleri', 'kullanici_rolleri.kullanici_id', '=', 'kullanicilar.id')
            ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
            ->leftjoin('egitmenler', 'egitmenler.kullanici_id', '=', 'kullanicilar.id')
            ->select('kullanici_rolleri.id', 'roller.adi as rol_adi', 'kullanicilar.adi_soyadi', 'kullanicilar.email')
            ->selectRaw("trim(concat(egitmenler.cep_tel_kod, ' ', egitmenler.cep_tel)) as cep_tel")
            ->selectRaw("ifnull((select date_format(kullanici_girisler.created_at, '%d.%m.%Y %H:%i')
                        from kullanici_girisler
                        where kullanici_girisler.kullanici_id = kullanicilar.id
                        and kullanici_girisler.durum = 1 order by kullanici_girisler.id desc limit 1), 'Never Logged in') as last_login")
            ->orderby('roller.adi', 'asc')
            ->orderby('kullanicilar.adi_soyadi', 'asc');
        if((int) session('FILTRE_KULLANICI_ID') > 0) {
            $liste->where('kullanicilar.id', session('FILTRE_KULLANICI_ID'));
        }
        if((int) session('FILTRE_ROL_ID') > 0) {
            $liste->where('roller.id', session('FILTRE_ROL_ID'));
        }


        $this->liste = $liste->paginate(100);
        $this->alt_baslik = "Active User List";

        session([
            'PREFIX' => 'active_user',
            'FLG_DURUM' => 1
        ]);

        return $this->index();
    }

    public function viewPassiveUser(Request $request) {
        if(!Auth::user()->isAllow('wu_ps_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = User::where('kullanicilar.flg_durum', 0)
            ->leftjoin('kullanici_rolleri', 'kullanici_rolleri.kullanici_id', '=', 'kullanicilar.id')
            ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
            ->leftjoin('egitmenler', 'egitmenler.kullanici_id', '=', 'kullanicilar.id')
            ->select('kullanici_rolleri.id', 'roller.adi as rol_adi', 'kullanicilar.adi_soyadi', 'kullanicilar.email')
            ->selectRaw("trim(concat(egitmenler.cep_tel_kod, ' ', egitmenler.cep_tel)) as cep_tel")
            ->selectRaw("ifnull((select date_format(kullanici_girisler.created_at, '%d.%m.%Y %H:%i')
                        from kullanici_girisler
                        where kullanici_girisler.kullanici_id = kullanicilar.id
                        and kullanici_girisler.durum = 1 order by kullanici_girisler.id desc limit 1), 'Never Logged in') as last_login")
            ->orderby('roller.adi', 'asc')
            ->orderby('kullanicilar.adi_soyadi', 'asc')
            ->paginate(100);

        $this->liste = $liste;
        $this->alt_baslik = "Passive User List";

        session([
            'PREFIX' => 'passive_user',
            'FLG_DURUM' => 0
        ]);

        return $this->index();
    }

    public function kullanicilarGetirJson($prefix, Request $request) {
        $kullanicilar = User::select('kullanicilar.id', 'kullanicilar.adi_soyadi')
            ->join('kullanici_rolleri', 'kullanici_rolleri.kullanici_id', '=', 'kullanicilar.id')
            ->where('kullanici_rolleri.rol_id', $request->filtre_rol_id)
            ->where('kullanicilar.flg_durum', session('FLG_DURUM'))
            ->get();

        return response()->json($kullanicilar);
    }

    public function userSearch($prefix, Request $request) {
        session([
            'FILTRE_ROL_ID' => $request->filtre_rol_id,
            'FILTRE_KULLANICI_ID' => $request->filtre_kullanici_id
        ]);
        return redirect()->to('/'.session('PREFIX'));
    }
}
