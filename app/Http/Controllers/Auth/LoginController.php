<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\KullaniciGirisler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function password()
    {
        return 'sifre';
    }

    protected function attemptLogin(Request $request)
    {
        $user = \App\User::where([
            'email' => $request->email
        ])->first();

        if ($user) {
            if(md5($request->password) == $user->sifre) {
                $this->guard()->login($user, $request->has('remember'));
                KullaniciGirisler::insert([
                    'kullanici_id' => $user->id,
                    'ip' => $request->ip(),
                    'durum' => 1,
                    'giris_yeri' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                return true;
            }
            KullaniciGirisler::insert([
                'kullanici_id' => $user->id,
                'ip' => $request->ip(),
                'durum' => -1,
                'giris_yeri' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return false;
        }

        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        $this->setUserSession($user);
    }

    protected function setUserSession($user)
    {
        session(
            [
                'ROL_ID' => $user->kullaniciRolu()->rol_id,
                'ROL_ADI' => $user->kullaniciRolu()->rol_adi,
                'ADI_SOYADI' => $user->adi_soyadi,
                'KULLANICI_ID' => $user->id
            ]
        );
    }
}
