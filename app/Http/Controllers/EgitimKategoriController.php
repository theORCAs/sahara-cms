<?php

namespace App\Http\Controllers;

use App\Http\Models\EgitimKategori;
use App\Http\Models\ParaBirimi;
use App\Mail\DenemeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Validator;
use Auth;

class EgitimKategoriController extends HomeController
{
    private $prefix = "to_categories";
    private $error_messages = array(
        'sira.required' => 'Order is required.',
        'sira.numeric' => 'Order must be number.',
        'adi.required' => 'Name is required.',
    );
    private $rules = array(
        'sira' => 'required|numeric',
        'adi' => 'required',
    );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('to_category_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = EgitimKategori::orderby('flg_aktif', 'desc')
            ->orderby('sira', 'asc')
            ->paginate(100);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => 'Categories'
        ];
        return view('egitimler.kategori_view', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create()
    {
        if(!Auth::user()->isAllow('to_category_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Training Category",
            'data' => new EgitimKategori(),
            'para_birimi' => ParaBirimi::get(),
        ];
        return view('egitimler.kategori_edit', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('to_category_add')) {
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

            $sonuc = EgitimKategori::create([
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
                'onsoz' => $request->onsoz,
                'ucret' => $request->ucret,
                'pb_id' => $request->pb_id,
                'flg_aktif' => intval($request->flg_aktif),
            ]);
            if($request->file('resim') != "") {

                EgitimKategori::find($sonuc->id)->update([
                    'resim' => $request->file("resim")->store("public/urun_kategori_resim")
                ]);
            }
            $this->kategoriSiralamaDuzelt($sonuc->id, $request->sira);
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
     * @param  \App\Http\Models\EgitimKategori  $egitimKategori
     * @return \Illuminate\Http\Response
     */
    public function show(EgitimKategori $egitimKategori)
    {
        //
    }

    /**
     * @param EgitimKategori $egitimKategori
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(EgitimKategori $egitimKategori, $id)
    {
        if(!Auth::user()->isAllow('to_category_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Training Category",
            'data' => $egitimKategori->findorfail($id),
            'para_birimi' => ParaBirimi::get()
        ];
        return view('egitimler.kategori_edit', $data);
    }

    /**
     * @param Request $request
     * @param EgitimKategori $egitimKategori
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, EgitimKategori $egitimKategori, $id)
    {
        if(!Auth::user()->isAllow('to_category_edit')) {
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

            $sonuc = $egitimKategori->find($id);
            $eski_resim = $sonuc->resim;

            if(isset($request->tmp_del_image)) {
                Storage::delete($eski_resim);
                $sonuc->update([
                    'resim' => null
                ]);
            }

            $sonuc->update([
                    'adi' => $request->input('adi'),
                    'sira' => $request->sira,
                    'onsoz' => $request->onsoz,
                    'ucret' => $request->ucret,
                    'pb_id' => $request->pb_id,
                    'flg_aktif' => intval($request->flg_aktif),
                ]);
            if($request->file('resim') != "") {
                if($eski_resim != "") {
                    Storage::delete($eski_resim);
                }
                $sonuc->update([
                        'resim' => $request->file("resim")->store("public/urun_kategori_resim")
                    ]);
            }
            $this->kategoriSiralamaDuzelt($id, $request->sira);
            return redirect('/'.$this->prefix)->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * @param EgitimKategori $egitimKategori
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(EgitimKategori $egitimKategori, $id)
    {
        if(!Auth::user()->isAllow('to_category_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $egitimKategori->destroy($id);
            $this->kategoriSiralamaDuzelt();
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

    /**
     * @param null $id
     * @param null $sira
     */
    private function kategoriSiralamaDuzelt($id=null, $sira=null) {
        if(!empty($id) && !empty($sira)) {
            EgitimKategori::where('sira', '>=', $sira)
                ->where('id', '!=', $id)
                ->where('flg_aktif', 1)
                ->update([
                    'sira' => DB::raw('sira+1')
                ]);
        }
        DB::statement(DB::raw('set @count:=0'));
        EgitimKategori::where('flg_aktif', 1)
            ->orderby('sira')
            ->update([
                'sira' => DB::raw('@count:=@count+1')
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCateogryOrder(Request $request) {
        $category_id = $request->id ?? null;
        $order = $request->order ?? null;
        EgitimKategori::find($category_id)->update([
            'sira' => $order
        ]);
        $this->kategoriSiralamaDuzelt($category_id, $order);
        return response()->json([
            'success' => true,
            'message'
        ]);
    }
}
