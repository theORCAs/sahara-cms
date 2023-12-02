<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FtStaticPages extends Model
{
    protected $table = "ft_static_pages";

    protected $guarded = ['id'];
    protected $fillable = [
        'title',
        'url',
        'content',
        'created_at'
    ];
    public function saveInquiryForm($response)
    {
        \DB::table('inquiry_form')->insert($response);
    }

    public function getReferencesList()
    {
        try {
            //  \DB::enableQueryLog(); // Enable query log
            $result = \DB::table('referanslar as r')
                ->select('r.id', 'r.adi as reference_name', 'u.adi as country_name', 's.adi as sector_name')
                ->join('ulkeler as u', 'u.id', '=', 'r.ulke_id')
                ->join('sektorler as s', 's.id', '=', 'r.sektor_id')
                ->where('r.flg_aktif', 1)
                ->where('r.flg_notinlist', 0)->get()->toArray();
            //  dd(\DB::getQueryLog());
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result ?? [];
    }

    public function getTrainingCategories()
    {
        try {
            $result = \DB::table('egitim_kategori as ek')
                ->select('ek.id as categoryId','ek.adi as categoryName', 'ek.onsoz as content', 'ek.resim', 'ek.ucret', 'ek.sira')
                ->where('flg_aktif', 1)
                ->get();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result ?? [];
    }

    public function getTraining()
    {
        try {
              //\DB::enableQueryLog(); // Enable query log
            $result = \DB::table('egitimler as e')
                ->select(
                    'e.id',
                    'e.kategori_id as categoryId',
                    'e.kodu as code',
                    'e.adi',
                    'e.onsoz',
                    'e.keyword',
                    'e.aciklama',
                    'e.icerik',
                    'e.objective',
                    'e.attend',
                    'e.ucret as fee',
                    'e.egitim_part_id',
                    'e.egitim_suresi as trainingTime',
                    'e.flg_kisitli',
                    'e.sira'
                )
                ->where('e.flg_aktif', 1)->leftJoin('egitim_tarihleri as et','et.egitim_id', '=','e.id')
                ->whereBetween('et.baslama_tarihi',[date('Y-m-d'),\DB::raw('DATE_ADD(CURDATE(), interval et.egitim_suresi day)')])
                ->get()->toArray();
            //dd(\DB::getQueryLog());
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        if(empty($result))
        {
            return [];
        }
        $data = [];
        foreach ($result as $item) {
            $data[$item->categoryId][] = $item;
        }
        return $data;
    }
    public function getCountries()
    {
        try {
            $result = \DB::table('ulkeler as u')
                ->select('id','adi as name')
                ->get()->toArray();
        }catch (\Exception $e){
            error_log($e->getMessage());
        }
        return $result ?? [];
    }

    /**
     * @return array
     */
    public function getFaqFrequentInquiries(): array
    {
        try {
            $result = \DB::table('ws_sss as q')
                ->select('q.id','q.soru as  questions','q.cevap as answer','q.sira as order')
                ->where('q.flg_aktif',1)
                ->orderBy('q.sira')
                ->get()->toArray();
        }catch (\Exception $e){
            error_log($e->getMessage());
        }
        return $result ?? [];
    }
    public function getTrainingsForm(int $educationId): array
    {
        if(empty($educationId))
        {
            return [];
        }
        try {
            $result = \DB::table('egitim_tarihleri as et')
                ->select('e.id as education_id','e.adi as education_name','e.egitim_suresi as education_time','e.kodu as code','et.baslama_tarihi as starting_date','e.ucret as fee','ey.adi as education_place')
                ->leftJoin('egitim_yerleri as ey','ey.id', '=','et.egitim_yeri_id')
                ->leftJoin('egitimler as e','e.id', '=','et.egitim_id')
                ->where('et.egitim_id',$educationId)
                ->where(\DB::raw('year(et.baslama_tarihi)'),2021)
                ->orderBy('et.baslama_tarihi')
                ->get()->toArray();
        }catch (\Exception $e){
            error_log($e->getMessage());
        }
        return $result ?? [];
    }

}
