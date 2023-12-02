<?php

namespace App\Http\Controllers;

use App\Http\Models\Havaalanlari;
use Illuminate\Http\Request;
use Validator;
use Auth;

class HavaalanlariController extends HomeController
{
    private $prefix = "at_airport";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('at_ap_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $liste = Havaalanlari::wherenull('deleted_at')
            ->orderBy('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => $this->prefix,
            'alt_baslik' => "Airport List"
        ];

        return view('airport_transfer.airport_view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('at_ap_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Add New Airport",
            'data' => new Havaalanlari()
        ];
        return view('airport_transfer.airport_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('at_ap_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Airport name is required.'
            );
            $rules = array(
                'adi' => 'required',
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            Havaalanlari::create([
                'adi' => $request->input('adi')
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
     * @param  \App\Http\Models\Havaalanlari  $havaalanlari
     * @return \Illuminate\Http\Response
     */
    public function show(Havaalanlari $havaalanlari)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Havaalanlari  $havaalanlari
     * @return \Illuminate\Http\Response
     */
    public function edit(Havaalanlari $havaalanlari, $id)
    {
        if(!Auth::user()->isAllow('at_ap_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => $this->prefix,
            'alt_baslik' => "Edit Airport",
            'data' => $havaalanlari->findorfail($id)
        ];

        return view('airport_transfer.airport_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Havaalanlari  $havaalanlari
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Havaalanlari $havaalanlari, $idsi)
    {
        if(!Auth::user()->isAllow('at_ap_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Airport name is required.'
            );
            $rules = array(
                'adi' => 'required',
            );

            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $havaalanlari->find($idsi)->update([
                'adi' => $request->input('adi')
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
     * @param  \App\Http\Models\Havaalanlari  $havaalanlari
     * @return \Illuminate\Http\Response
     */
    public function destroy(Havaalanlari $havaalanlari, $idsi)
    {
        if(!Auth::user()->isAllow('at_ap_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        try {
            $havaalanlari->destroy($idsi);
            return redirect('/'.$this->prefix)->with(['msj' => config('messages.islem_basarili')]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }

    }
}
