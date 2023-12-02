<?php

namespace App\Http\Controllers;

use App\Http\Models\EmailSablon;
use App\Http\Models\SendEmail;
use App\Http\Models\SifreReset;
use App\User;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function forgetPass(Request $request) {
        try {
            $kullanici = User::where('email', $request->email)->first();
            if($kullanici["id"] > 0) {
                $reset_kontrol = SifreReset::where('kullanici_id', $kullanici->id)
                    ->whereraw('tarih = curdate()')
                    ->wherenull('sifre_degisti')
                    ->first();
                if($reset_kontrol->id > 0) {
                    return redirect()->back()->with(["err_msj" => "You have already made a request earlier. Please check your email"]);
                } else {
                    $reset = SifreReset::updateorcreate([
                        'kullanici_id' => $kullanici->id,
                        'email' => $kullanici->email,
                        'tarih' => date('Y-m-d'),
                        'sifre_degisti' => null
                    ]);
                    $sablon = EmailSablon::find(27);
                    $reset_link = "http://www.saharatraining.com/?password-reset," . md5($reset->id);
                    $icerik = str_replace([
                        '{ADI_SOYADI}',
                        '{RESET_LINK}',
                    ], [
                        $kullanici->adi_soyadi,
                        "<a href='$reset_link'>$reset_link</a>",
                    ], $sablon->alan2);
                    SendEmail::create([
                        'oncelik' => 5,
                        'konu' => $sablon->alan1,
                        'from_email' => $sablon->alan4,
                        'to_email' => $kullanici->email,
                        'cc' => '',
                        'bcc' => $sablon->alan7,
                        'icerik' => $icerik,
                        'ekler' => ''
                    ]);
                    return redirect()->back()->with(["msj" => "An email message for resetting has been sent to you. Please check your email for the rest of process. Thanks"]);
                }
            } else {
                return redirect()->back()->with(["err_msj" => "Mail address not found."]);
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
        return redirect()->back()->with(['msj' => 'Your password reset mail has been send. '.$request->email]);
    }
}
