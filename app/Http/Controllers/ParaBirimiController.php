<?php

namespace App\Http\Controllers;

use App\Http\Models\ParaBirimi;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ParaBirimiController extends HomeController
{
    private $_prefix = "currency_type_view";

    private $_error_messages = [
            'adi.required' => 'Currency name is required.',
            'kisaltma.required' => 'Currency shortname is required.',
        ];

    private $_rules = [
            'adi' => 'required',
            'kisaltma' => 'required',
        ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('currency_type_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = ParaBirimi::orderby('id', 'asc')->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->_prefix,
            'alt_baslik' => "Currency Type"
        ];
        return view('settings.para_birimi.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('currency_type_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->_prefix,
            'alt_baslik' => "Add New Currency Type",
            'data' => new ParaBirimi()
        ];
        return view('settings.para_birimi.edit', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('currency_type_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $validator = Validator::make($request->all(), $this->_rules, $this->_error_messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            ParaBirimi::create([
                'adi' => $request->input('adi'),
                'kisaltma' => $request->kisaltma ?? null,
                'banka_bilgileri' => $request->banka_bilgileri ?? null,
            ]);
            return redirect('/'.$this->_prefix)->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  \App\Http\Models\ParaBirimi  $paraBirimi
     * @return \Illuminate\Http\Response
     */
    public function show(ParaBirimi $paraBirimi)
    {
        //
    }

    /**
     * @param ParaBirimi $paraBirimi
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(ParaBirimi $paraBirimi, $id)
    {
        if(!Auth::user()->isAllow('currency_type_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->_prefix,
            'alt_baslik' => "Edit Currency Type",
            'data' => $paraBirimi->findorfail($id)
        ];
        return view('settings.para_birimi.edit', $data);
    }

    /**
     * @param Request $request
     * @param ParaBirimi $paraBirimi
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ParaBirimi $paraBirimi, $id)
    {
        if(!Auth::user()->isAllow('currency_type_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $validator = Validator::make($request->all(), $this->_rules, $this->_error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $paraBirimi->find($id)
                ->update([
                    'adi' => $request->input('adi'),
                    'kisaltma' => $request->kisaltma ?? null,
                    'banka_bilgileri' => $request->banka_bilgileri ?? null,
                ]);
            return redirect('/'.$this->_prefix)->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * @param ParaBirimi $paraBirimi
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ParaBirimi $paraBirimi, $id)
    {
        if(!Auth::user()->isAllow('currency_type_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $paraBirimi->destroy($id);
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
