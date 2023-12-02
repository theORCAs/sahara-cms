<?php

namespace App\Http\Controllers;

use App\Http\Models\Ulkeler;
use Illuminate\Http\Request;
use Validator;
use Auth;

class UlkelerController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('pu_countries_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = Ulkeler::wherenull('deleted_at')
            ->orderby('flg_aktif', 'desc')
            ->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => "countries_view",
            'alt_baslik' => "Countries"
        ];
        return view('settings.country.view', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create()
    {
        if(!Auth::user()->isAllow('pu_contries_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => "countries_view",
            'alt_baslik' => "Add New Country",
            'data' => new Ulkeler()
        ];
        return view('settings.country.edit', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('pu_contries_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Country name is required.',
                'tel_kodu.required' => 'Phone code is required.'
            );
            $rules = array(
                'adi' => 'required',
                'tel_kodu' => 'required'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {


                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Ulkeler::create([
                'adi' => $request->input('adi'),
                'tel_kodu' => $request->tel_kodu,
                'flg_aktif' => intval($request->flg_aktif)
            ]);
            return redirect('/countries_view')->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\Ulkeler  $ulkeler
     * @return \Illuminate\Http\Response
     */
    public function show(Ulkeler $ulkeler)
    {
        //
    }

    /**
     * @param Ulkeler $ulkeler
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Ulkeler $ulkeler, $id)
    {
        if(!Auth::user()->isAllow('pu_countries_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => "countries_view",
            'alt_baslik' => "Edit Country",
            'data' => $ulkeler->findorfail($id)
        ];
        return view('settings.country.edit', $data);
    }

    /**
     * @param Request $request
     * @param Ulkeler $ulkeler
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Ulkeler $ulkeler, $id)
    {
        if(!Auth::user()->isAllow('pu_countries_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Country name is required.',
                'tel_kodu.required' => 'Phone code is required.'
            );
            $rules = array(
                'adi' => 'required',
                'tel_kodu' => 'required'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ulkeler->find($id)
                ->update([
                'adi' => $request->input('adi'),
                'tel_kodu' => $request->tel_kodu,
                'flg_aktif' => intval($request->flg_aktif)
            ]);
            return redirect('/countries_view')->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * @param Ulkeler $ulkeler
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Ulkeler $ulkeler, $id)
    {
        if(!Auth::user()->isAllow('pu_countries_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $ulkeler->find($id)
                ->update([
                    "deleted_at" => date("Y-m-d H:i:s")
                ]);

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
