<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimKategori;
use App\Http\Models\Egitimler;
use App\Http\Models\EgitimPart;
use App\Http\Models\EgitimProgram;
use App\Http\Models\EgitimTarihleri;
use App\Http\Models\EgitimYerleri;
use App\Http\Models\KullaniciRolleri;
use Illuminate\Http\Request;
use Validator;
use Auth;
use PDF;

class EgitimlerController extends HomeController
{
    private $prefix = "to_outline";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('to_oas_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $kategori_listesi = EgitimKategori::where('flg_aktif', '1')
            ->orderby('sira', 'asc')
            ->get();


        $liste = Egitimler::where('kategori_id', session('KATEGORI_ID'))
            ->wherenull('deleted_at')
            ->orderby('flg_aktif', 'desc')
            ->orderby('sira', 'asc')
            ->orderby('kodu', 'asc')
            ->paginate(100);

        $data = [
            'kategori_id' => session('KATEGORI_ID'),
            'kategori_listesi' => $kategori_listesi,
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Outlines and Schedules'
        ];
        return view('egitimler.egitimler_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('to_oas_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Training",
            'data' => new Egitimler(),
            'kategori_liste' => EgitimKategori::where('flg_aktif', '1')->orderby('sira', 'asc')->select('id', 'adi')->get()
        ];
        return view('egitimler.egitimler_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('to_oas_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $rules = [
                'kategori_id' => 'required',
                'kodu' => 'required',
                'adi' => 'required',
                'sira' => 'required|numeric',
            ];
            $error_messages = [
                'kategori_id.required' => 'Category is required.',
                'kodu.required' => 'Code is required.',
                'adi.required' => 'Name is required.',
                'sira.required' => 'Order is required.',
                'sira.numeric' => 'Order must be number.',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Egitimler::create([
                'kategori_id' => $request->input('kategori_id'),
                'kodu' => $request->input('kodu'),
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
                'flg_aktif' => intval($request->flg_aktif),
                'ucret' => floatval($request->ucret),
                'pb_id' => $request->pb_id,
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
     * @param  \App\Http\Models\Egitimler  $egitimler
     * @return \Illuminate\Http\Response
     */
    public function show(Egitimler $egitimler)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Egitimler  $egitimler
     * @return \Illuminate\Http\Response
     */
    public function edit(Egitimler $egitimler, $id)
    {
        if(!Auth::user()->isAllow('to_oas_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $result = $egitimler->findorfail($id);
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Training Schedule",
            'data' => $result,
            'kategori_liste' => EgitimKategori::where('flg_aktif', '1')->orwhere('id', $result->kategori_id)->orderby('sira', 'asc')->select('id', 'adi')->get()
        ];
        return view('egitimler.egitimler_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Egitimler  $egitimler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Egitimler $egitimler, $id)
    {
        if(!Auth::user()->isAllow('to_oas_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $rules = [
                'kategori_id' => 'required',
                'kodu' => 'required',
                'adi' => 'required',
                'sira' => 'required|numeric',
            ];
            $error_messages = [
                'kategori_id.required' => 'Category is required.',
                'kodu.required' => 'Code is required.',
                'adi.required' => 'Name is required.',
                'sira.required' => 'Order is required.',
                'sira.numeric' => 'Order must be number.',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $egitimler->find($id)
                ->update([
                    'kategori_id' => $request->input('kategori_id'),
                    'kodu' => $request->input('kodu'),
                    'adi' => $request->input('adi'),
                    'sira' => $request->sira,
                    'flg_aktif' => intval($request->flg_aktif),
                    'ucret' => floatval($request->ucret),
                    'pb_id' => $request->pb_id,
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
     * @param  \App\Http\Models\Egitimler  $egitimler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Egitimler $egitimler, $id)
    {
        if(!Auth::user()->isAllow('to_oas_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $egitimler->destroy($id);
            return redirect('/'.$this->prefix)
                ->with('msj', config('messages.islem_basarili'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function search(Request $request) {
        session(['KATEGORI_ID' => $request->kategori_id]);

        return $this->index();
    }

    public function outlineEdit($prefix, $id) {
        if(!Auth::user()->isAllow('to_oas_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $result = Egitimler::findorfail($id);
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Training Outline",
            'data' => $result,
            'ilgili_kisiler' => KullaniciRolleri::wherein("kullanici_rolleri.rol_id", [3, 6, 8, 12, 13])
                ->leftjoin('kullanicilar', 'kullanicilar.id', '=', 'kullanici_rolleri.kullanici_id')
                ->leftjoin('roller', 'roller.id', '=', 'kullanici_rolleri.rol_id')
                ->where('kullanicilar.flg_durum', 1)
                ->select('kullanicilar.id', 'kullanicilar.adi_soyadi', 'roller.adi as rol_adi')
                ->orderby('roller.adi', 'asc')
                ->orderby('kullanicilar.adi_soyadi')
                ->get(),
            'egitim_part_listesi' => EgitimPart::wherenull('deleted_at')->orwhere('id', $result->egitim_part_id)->orderby('adi', 'asc')->get(),
            'egitim_program' => EgitimProgram::where('egitim_id', $result->id)->orderby('gun', 'asc')->get(),
        ];
        return view('egitimler.egitimler_outline_edit', $data);
    }

    public function outlineEditSave(Request $request, Egitimler $egitimler, $prefix, $id) {

        if(!Auth::user()->isAllow('to_oas_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $rules = [
                'keyword' => 'required',
                'icerik' => 'required',
                'egitim_suresi' => 'required|numeric',
            ];
            $error_messages = [
                'keyword.required' => 'SEO Keyword is required.',
                'icerik.required' => 'Course Description is required.',
                'egitim_suresi.required' => 'Topics in Text Boxes is required.',
                'egitim_suresi.numerix' => 'Topics in Text Boxes must be number.',
            ];
            if(isset($request->p_gun)) {
                foreach($request->p_gun as $key => $row) {
                    $rules['p_icerik.'.$key] = 'required';
                    $error_messages['p_icerik.'.$key.".required"] = 'Part '.($key+1)." description is required.";
                }
            }

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $kayit = $egitimler->find($id);

            $kayit->update([
                    'teklif_eden_kisi' => intval($request->teklif_eden_kisi) > 0 ? $request->teklif_eden_kisi : null,
                    'keyword' => $request->input('keyword'),
                    'aciklama' => $request->input('aciklama'),
                    'icerik' => $request->icerik,
                    'objective' => $request->objective,
                    'onsoz' => $request->onsoz,
                    'attend' => $request->attend,
                    'flg_kisitli' => intval($request->flg_kisitli),
                    'egitim_part_id' => $request->egitim_part_id,
                    'egitim_suresi' => intval($request->egitim_suresi),
                ]);
            $key = -1;
            if(isset($request->p_gun)) {
                foreach($request->p_gun as $key => $row) {
                    EgitimProgram::updateorcreate([
                        'egitim_id' => $kayit->id,
                        'gun' => ($key + 1)
                    ], [
                        // 'gun' => ($key + 1),
                        'icerik' => $request->p_icerik[$key],
                        'flg_gosterme' => in_array(($key + 1), $request->p_flg_gosterme) ? 1 : 0,
                    ]);
                }
            }
            for($i = $key + 1; $i < $request->egitim_suresi; $i++) {
                EgitimProgram::updateorcreate([
                    'egitim_id' => $kayit->id,
                    'gun' => ($i + 1)
                ], [
                    // 'gun' => ($key + 1),
                    'icerik' => isset($request->p_icerik[$i]) ? $request->p_icerik[$i] : null,
                    'flg_gosterme' => in_array(($i + 1), $request->p_flg_gosterme) ? 1 : 0,
                ]);
            }
            if($request->hid_geri_don == "1")
                return redirect()
                    ->back()
                    ->withInput();

            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function scheduleEdit($prefix, $id) {
        if(!Auth::user()->isAllow('to_oas_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => 'Schedule Edit',
            'egitim_id' => $id,
            'data' => [
                'egitim_suresi' => 5
            ],
            'egitim_yerleri' => EgitimYerleri::orderby('flg_default', 'desc')->orderby('adi', 'asc')->select('id', 'adi')->get(),
            'egitim_part_listesi' => EgitimPart::wherenull('deleted_at')->orderby('adi', 'asc')->get(),
            'liste' => EgitimTarihleri::where('egitim_id', $id)->whereraw('baslama_tarihi > curdate()')->orderby('baslama_tarihi', 'asc')->get()
        ];

        return view('egitimler.egitimler_schedule_edit', $data);
    }

    public function egitimTarihSaveJson(Request $request, EgitimTarihleri $egitimTarihleri, $id) {
        if(!Auth::user()->isAllow('to_oas_edit')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }

        try {
            $rules = [
                'baslama_tarihi' => 'required',
                'egitim_suresi' => 'required|numeric',
            ];
            $error_messages = [
                'baslama_tarihi.required' => 'Start Date is required.',
                'egitim_suresi.required' => 'Duration is required.',
                'egitim_suresi.numeric' => 'Duration must be number.',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);

            if ($validator->fails()) {
                return response()->json([
                    'cvp' => '0',
                    'msj' => $validator->errors()->first()
                ]);
            }

            $egitimTarihleri->find($id)
                ->update([
                    'egitim_yeri_id' => $request->input('egitim_yeri_id'),
                    'baslama_tarihi' => date('Y-m-d', strtotime($request->baslama_tarihi)),
                    'egitim_suresi' => $request->input('egitim_suresi'),
                    'egitim_part_id' => $request->egitim_part_id,
                    'ucret' => floatval($request->ucret),
                    'ucret_para_birimi' => $request->ucret_para_birimi,
                ]);
            return response()->json([
                'cvp' => '1',
                'msj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()->first()
            ]);
        }
    }

    public function egitimTarihDelJson(EgitimTarihleri $egitimTarihleri, $id) {
        if(!Auth::user()->isAllow('to_oas_del')) {
            return response()->json([
                'cvp' => '0',
                'msj' => config('messages.yetkiniz_yok')
            ]);
        }
        try {
            $egitimTarihleri->destroy($id);
            return response()->json([
                'cvp' => '1',
                'msj' => ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => '0',
                'msj' => $e->getMessage()->first()
            ]);
        }

    }

    public function egitimTarihiEkleYearly(Request $request, EgitimTarihleri $egitimTarihleri, $prefix, $id) {
        try {
            $rules = [
                'baslama_tarihi' => 'required',
                'egitim_suresi' => 'required|numeric',
            ];
            $error_messages = [
                'baslama_tarihi.required' => 'Schedule start date is required.',
                'egitim_suresi.required' => 'Duration is required.',
                'egitim_suresi.numeric' => 'Duration must be number.',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            for($i = 0; $i < $request->tekrar_haftasi; $i++) {
                $hafta = $i * $request->atlanacak_ara;
                $baslama_tarihi = date('Y-m-d', strtotime($request->baslama_tarihi." +$hafta week"));
                $egitimTarihleri::create([
                    'egitim_id' => $id,
                    'egitim_yeri_id' => $request->input('fre_egitim_yeri'),
                    'baslama_tarihi' => $baslama_tarihi,
                    'egitim_suresi' => $request->input('egitim_suresi'),
                    'egitim_part_id' => $request->egitim_part_id,
                ]);
            }


            return redirect()
                ->back()
                ->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function egitimTarihiEkleIndividual(Request $request, EgitimTarihleri $egitimTarihleri, $prefix, $id) {
        try {
            $rules = [
                'baslama_tarihi' => 'required',
                'egitim_suresi' => 'required|numeric',
            ];
            $error_messages = [
                'baslama_tarihi.required' => 'Course Start date is required.',
                'egitim_suresi.required' => 'Duration is required.',
                'egitim_suresi.numeric' => 'Duration must be number.',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $egitimTarihleri::create([
                'egitim_id' => $id,
                'egitim_yeri_id' => $request->input('fre_egitim_yeri'),
                'baslama_tarihi' => date('Y-m-d', strtotime($request->baslama_tarihi)),
                'egitim_suresi' => $request->input('egitim_suresi'),
                'egitim_part_id' => $request->egitim_part_id,
            ]);
            return redirect()
                ->back()
                ->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function outlinePdfCreate(Egitimler $egitimler, $id, $sch=null) {
        $data = [
            'data' => $egitimler->find($id),
            'sch' => $sch
        ];
        $path = "public/outl_pdf/";
        $filename = "course_outline_".$id.".pdf";

        $pdf = PDF::loadView('egitim_kayitlar/pdf/egitim_outline', $data)->setPaper('a4', 'portraid');

        $pdf->save(storage_path().'/app/'.$path.$filename);

        //return redirect('/pm_wait');
        return $pdf->stream($filename);
    }
}
