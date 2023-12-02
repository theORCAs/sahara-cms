<?php

namespace App\Http\Controllers;

use App\Http\Models\Katilimcilar;
use App\Http\Models\KatilimcilarFoto;
use App\Http\Models\Teklifler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class KatilimcilarFotoController extends HomeController
{
    /**
     * @var string
     */
    private $_prefix = "participantPhoto";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($teklif_id=null)
    {
        $teklif_obj = Teklifler::find($teklif_id);

        $data = [
            'teklif_id' => $teklif_id,
            'prefix' => $this->_prefix,
            'alt_baslik' => $teklif_obj->egitimKayit->egitimler->adi,
            'liste' => KatilimcilarFoto::where('teklif_id', $teklif_id)->get(),
            'data' => new KatilimcilarFoto(),
            'katilimci_listesi' => $teklif_obj->egitimKayit->katilimcilar
        ];
        return view('teklifler.photo.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $teklif_id)
    {
        try {
            $error_messages = [
                'aciklama.required' => 'Explanation is required.',
                'resim.required' => 'Photo is required.',
                'resim.image' => 'Photo must be image',
                'resim.mimes' => 'Image type must be jpeg,png,jpg,gif,svg',
                'resim.max' => 'Image to big!!!',
            ];
            $rules = [
                'aciklama' => 'required',
                'resim' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ins_data = [
                'aciklama' => $request->input('aciklama'),
                'katilimci_id' => $request->katilimci_id ?? null,
                'teklif_id' => $teklif_id
            ];
            if($request->file("resim") != "") {
                $ins_data["resim"] = $request->file("resim")->store("public/katilimci_foto");
            }
            KatilimcilarFoto::create($ins_data);

            return redirect('/'.$this->_prefix."/".$teklif_id)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\KatilimcilarFoto  $katilimcilarFoto
     * @return \Illuminate\Http\Response
     */
    public function show(KatilimcilarFoto $katilimcilarFoto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\KatilimcilarFoto  $katilimcilarFoto
     * @return \Illuminate\Http\Response
     */
    public function edit(KatilimcilarFoto $katilimcilarFoto, $foto_id)
    {
        $data = $katilimcilarFoto->find($foto_id);

        $teklif_obj = Teklifler::find($data->teklif_id);

        $data = [
            'teklif_id' => $data->teklif_id,
            'prefix' => $this->_prefix,
            'alt_baslik' => $teklif_obj->egitimKayit->egitimler->adi,
            'liste' => KatilimcilarFoto::where('teklif_id', $data->teklif_id)->get(),
            'data' => $data,
            'katilimci_listesi' => $teklif_obj->egitimKayit->katilimcilar
        ];
        return view('teklifler.photo.list', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\KatilimcilarFoto  $katilimcilarFoto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KatilimcilarFoto $katilimcilarFoto, $id)
    {

        try {
            $error_messages = [
                'aciklama.required' => 'Explanation is required.',
                'resim.image' => 'Photo must be image',
                'resim.mimes' => 'Image type must be jpeg,png,jpg,gif,svg',
                'resim.max' => 'Image to big!!!',
            ];
            $rules = [
                'aciklama' => 'required',
                'resim' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }


            $ins_data = [
                'aciklama' => $request->input('aciklama'),
                'katilimci_id' => $request->katilimci_id ?? null,
            ];
            if($request->file("resim") != "") {
                $ins_data["resim"] = $request->file("resim")->store("public/katilimci_foto");
            }
            $data = $katilimcilarFoto->find($id);
            $data->update($ins_data);

            return redirect('/'.$this->_prefix."/".$data->teklif_id)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\KatilimcilarFoto  $katilimcilarFoto
     * @return \Illuminate\Http\Response
     */
    public function destroy(KatilimcilarFoto $katilimcilarFoto, $id)
    {
        try {
            $katilimcilarFoto->destroy($id);
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
}
