<?php

namespace App\Http\Controllers;

use App\Http\Models\CheckList;
use App\Http\Models\CheckListKategori;
use Illuminate\Http\Request;
use Validator;
use Auth;

class CheckListController extends HomeController
{
    private $prefix = "chkl_list";
    private $s_kategori_id;
    private $error_messages = array(
        'kategori_id.required' => 'Category is required.',
        'sira.required' => 'Order number is required.',
        'sira.numeric' => 'Order number must be number.',
        'adi.required' => 'Name is required.',
    );
    private $rules = array(
        'kategori_id' => 'required',
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
        if(!Auth::user()->isAllow('clm_list_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $kategori_listesi = CheckListKategori::wherenull('deleted_at')
            ->where('flg_aktif', 1)
            ->orderby('sira', 'asc')
            ->get();

        $liste = CheckList::wherenull('deleted_at')
            ->orderby('kategori_id', 'asc')
            ->orderby('flg_aktif', 'desc')
            ->orderby('sira', 'asc');

        if($this->s_kategori_id > 0) {
            $liste = $liste->where('kategori_id', $this->s_kategori_id);
        }

        $data = [
            'kategori_listesi' => $kategori_listesi,
            'liste' => $liste->paginate(20),
            'prefix' => $this->prefix,
            'alt_baslik' => 'Check List',
            's_kategori_id' => $this->s_kategori_id
        ];

        return view('office_management.checklist.liste_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('clm_list_add')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Check List Category",
            'data' => new CheckList(),
            'kategori_listesi' => CheckListKategori::where('flg_aktif', 1)->orderby('sira', 'asc')
                ->orderby('adi', 'asc')->get()
        ];
        return view('office_management.checklist.liste_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('clm_list_add')) {
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

            CheckList::create([
                'kategori_id' => $request->kategori_id,
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
                'flg_aktif' => $request->flg_aktif
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
     * @param  \App\Http\Models\CheckList  $checkList
     * @return \Illuminate\Http\Response
     */
    public function show(CheckList $checkList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\CheckList  $checkList
     * @return \Illuminate\Http\Response
     */
    public function edit(CheckList $checkList, $id)
    {
        if(!Auth::user()->isAllow('clm_list_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Update Check List Item",
            'data' => $checkList->findorfail($id),
            'kategori_listesi' => CheckListKategori::where('flg_aktif', 1)->orderby('sira', 'asc')->orderby('adi', 'asc')->get()
        ];


        return view('office_management.checklist.liste_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\CheckList  $checkList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CheckList $checkList, $id)
    {
        if(!Auth::user()->isAllow('clm_list_edit')) {
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

            $checkList->findorfail($id)->update([
                'kategori_id' => $request->kategori_id,
                'adi' => $request->input('adi'),
                'sira' => $request->sira,
                'flg_aktif' => $request->flg_aktif
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
     * @param  \App\Http\Models\CheckList  $checkList
     * @return \Illuminate\Http\Response
     */
    public function destroy(CheckList $checkList, $id)
    {
        if(!Auth::user()->isAllow('clm_list_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $checkList->destroy($id);
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

    public function search(Request $request) {
        $this->s_kategori_id = $request->s_kategori_id;

        return $this->index();
    }
}
