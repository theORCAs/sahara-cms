<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\EbultenCikisNedenleri;
use Auth;
use Validator;

class EbultenCikisNedenleriController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAllow('pu_unscribe_view')) {
            abort(403, config('messages.yetkiniz_yok'));
        }
        $liste = EbultenCikisNedenleri::wherenull('deleted_at')
            ->orderby('sira', 'asc')
            ->orderby('adi', 'asc')
            ->paginate(20);

        $data = [
            'liste' => $liste,
            'prefix' => "unsubscribe_reasons",
            'alt_baslik' => "Unsubscribe Reasons"
        ];
        return view('settings.unsubscribe.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAllow('pu_unscribe_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => "unsubscribe_reasons",
            'alt_baslik' => "Add New Unsubscribe Reasons",
            'data' => new EbultenCikisNedenleri()
        ];
        return view('settings.unsubscribe.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAllow('pu_unscribe_add')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        try {
            $error_messages = array(
                'adi.required' => 'Reason name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {


                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            EbultenCikisNedenleri::create([
                'adi' => $request->input('adi'),
                'sira' => $request->sira
            ]);
            return redirect('/unsubscribe_reasons')->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EbultenCikisNedenleri $ebultenCikisNedenleri, $id)
    {
        if(!Auth::user()->isAllow('pu_unscribe_edit')) {
            return redirect()
                ->back()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }
        $data = [
            'prefix' => "unsubscribe_reasons",
            'alt_baslik' => "Edit Unsubscribe Reasons",
            'data' => $ebultenCikisNedenleri->findorfail($id)
        ];
        return view('settings.unsubscribe.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbultenCikisNedenleri $ebultenCikisNedenleri, $id)
    {
        if(!Auth::user()->isAllow('pu_unscribe_edit')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('err_msj', config('messages.yetkiniz_yok'));
        }

        try {
            $error_messages = array(
                'adi.required' => 'Reason name is required.',
                'sira.required' => 'Order number is required.',
                'sira.numeric' => 'The order number must be a number.'
            );
            $rules = array(
                'adi' => 'required',
                'sira' => 'required|numeric'
            );
            $validator = Validator::make($request->all(), $rules, $error_messages);
            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            $ebultenCikisNedenleri->find($id)
                ->update([
                    'adi' => $request->input('adi'),
                    'sira' => $request->sira,
                ]);
            return redirect('/unsubscribe_reasons')->with(["msj" => config("messages.islem_basarili")]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbultenCikisNedenleri $ebultenCikisNedenleri, $id)
    {
        if(!Auth::user()->isAllow('pu_unscribe_del')) {
            return redirect()
                ->back()
                ->withInput()
                ->with("err_msj", config('messages.yetkiniz_yok'));
        }
        $ebultenCikisNedenleri->find($id)
            ->update([
                "deleted_at" => date("Y-m-d H:i:s")
            ]);

        return redirect()
            ->back()
            ->with('msj', config('messages.islem_basarili'));
    }
}
