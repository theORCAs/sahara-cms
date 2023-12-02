<?php

namespace App\Http\Controllers;

use App\Http\Models\Yapi;
use Illuminate\Http\Request;

class YapiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $obj = new Yapi();
            $obj->modul_id = $request->input("modul_id");
            $obj->adi = $request->input("yapi_adi");
            $obj->aciklama = $request->input("aciklama");
            $result = $obj->save();

            return response()->json([
                'cvp' => 1,
                'msj' => ""
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => 0,
                'msj' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\App\Http\Models\Yapi  $yapi
     * @return \Illuminate\Http\Response
     */
    public function show(Yapi $yapi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\App\Http\Models\Yapi  $yapi
     * @return \Illuminate\Http\Response
     */
    public function edit(Yapi $yapi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\App\Http\Models\Yapi  $yapi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Yapi $yapi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\App\Http\Models\Yapi  $yapi
     * @return \Illuminate\Http\Response
     */
    public function destroy($yapi_id)
    {
        try {
            Yapi::destroy($yapi_id);
            return response()->json([
                'cvp' => 1,
                'msj' => ""
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cvp' => 0,
                'msj' => $e->getMessage()
            ]);
        }
    }
}
