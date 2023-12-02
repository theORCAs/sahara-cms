<?php

namespace App;

use App\Http\Models\Egitmenler;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = "kullanicilar";
/*
    protected $fillable = [
        // 'name', 'email', 'password',
        'adi_soyadi', 'email', 'sifre',
    ];
*/
    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
        'sifre', 'remember_token',
    ];

    /*
    public function yetkiVarmi($yapi) {
        $sorgu = DB::table("yetkiler as a")
            ->leftJoin("yapi as b", "a.yapi_id", "=", "b.id")
            ->select(DB::Raw("count(a.id) yetki_sayi"))
            ->where("a.rol_id", 3)
            ->where("b.adi", $yapi)
            ->first()
        ;
        if(intval($sorgu->yetki_sayi) > 0)
            return true;
        return false;
    }
    */

    public function kullaniciRolu() {
        $data = DB::select("select a.rol_id, b.adi rol_adi 
            from kullanici_rolleri a
                left join roller b on b.id = a.rol_id 
            where a.kullanici_id = :kullanici_id order by a.id limit 1", ['kullanici_id' => $this->id]);
        return $data[0];
    }

    public function isAllow($yapi_adi) {
        $data = DB::select("select count(b.id) yetki_var
            from yetkiler a
                left join yapi b on b.id = a.yapi_id 
            where (a.rol_id = :rol_id or a.kullanici_id = :kullanici_id) 
                and b.adi = :yapi_adi", [
                    'yapi_adi' => $yapi_adi,
                    'rol_id' => session('ROL_ID'),
                    'kullanici_id' => $this->id
            ]);
        return intval($data[0]->yetki_var) > 0 ? true : false;
    }

    public function egitmenID() {
        $result = Egitmenler::where('kullanici_id', $this->id)->first();

        return intval($result->id);
    }
}
