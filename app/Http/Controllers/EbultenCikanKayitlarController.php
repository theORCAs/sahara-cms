<?php

namespace App\Http\Controllers;

use App\Http\Models\EbultenCikanKayitlar;
use App\Http\Models\EbultenGruplar;
use Illuminate\Http\Request;
use Auth;

class EbultenCikanKayitlarController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($grup_id=null)
    {
        if(!Auth::user()->isAllow('em_ul_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }

        $grup_listesi = EbultenGruplar::wherenull('eb_gruplar.deleted_at')
            ->has('cikanKayitSayisi', '>', 0)
            ->orderbyraw('if(dinamik_grup_id is null, 0, 1)', 'asc')
            ->orderby('eb_gruplar.sira', 'asc')
            ->select('eb_gruplar.id', 'eb_gruplar.adi')
            ->get();
        if($grup_id > 0) {
            $liste = EbultenCikanKayitlar::where('grup_id', $grup_id)
                ->orderby('created_at', 'desc')
                ->paginate(100);
        } else {
            $liste = EbultenCikanKayitlar::whereHas('grup', function ($query) {
                    $query->orderby('adi', 'asc');
                })->orderby('created_at', 'desc')
                ->paginate(100);
        }

        $data = [
            'grup_id' => $grup_id,
            'grup_listesi' => $grup_listesi,
            'liste' => $liste,
            'prefix' => 'em_unsubscribedlist',
            'alt_baslik' => 'Unsubscribed List'
        ];
        return view('ebulten.bultenden_cikanlar_view', $data);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\EbultenCikanKayitlar  $ebultenCikanKayitlarController
     * @return \Illuminate\Http\Response
     */
    public function show(EbultenCikanKayitlarController $ebultenCikanKayitlarController)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EbultenCikanKayitlar  $ebultenCikanKayitlarController
     * @return \Illuminate\Http\Response
     */
    public function edit(EbultenCikanKayitlarController $ebultenCikanKayitlarController)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EbultenCikanKayitlar  $ebultenCikanKayitlarController
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbultenCikanKayitlarController $ebultenCikanKayitlarController)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EbultenCikanKayitlar  $ebultenCikanKayitlarController
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbultenCikanKayitlarController $ebultenCikanKayitlarController)
    {
        //
    }
}
