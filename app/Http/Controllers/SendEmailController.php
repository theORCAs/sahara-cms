<?php

namespace App\Http\Controllers;

use App\Http\Models\SendEmail;
use Illuminate\Http\Request;
use Validator;

class SendEmailController extends HomeController
{
    public function mailGonder(Request $request) {
        $error_messages = array(
            'konu.required' => 'Subject is required.',
            'to_email.required' => 'To receive email is required.',
            'to_email.email' => 'To receive email must be valid',
            'from_email.required' => 'From email (Reply to) is required',
            'from_email.email' => 'From email (Reply to) must be valid',
        );
        $rules = array(
            'konu' => 'required',
            'to_email' => 'required|email',
            'from_email' => 'required|email',
        );
        $validator = Validator::make($request->all(), $rules, $error_messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $attach = "";
        if($request->file("ek_dosya1") != "") {
            $ek_dosya1 = $request->file("ek_dosya1")->store("public/mail_ekler");
            $attach .= ($attach != "" ? "," : "").$ek_dosya1;
        }
        if($request->file("ek_dosya2") != "") {
            $ek_dosya2 = $request->file("ek_dosya2")->store("public/mail_ekler");
            $attach .= ($attach != "" ? "," : "").$ek_dosya2;
        }

        $data = [
            'oncelik' => 15,
            'konu' => $request->konu,
            'from_email' => $request->from_email,
            'to_email' => $request->to_email,
            'cc' => $request->cc,
            'bcc' => $request->bcc,
            'icerik' => $request->icerik,
            'ekler' => $attach
        ];
        try {
            SendEmail::create($data);
            return redirect('/'.$request->basarili_donus_url)->with(["msj" => config('messages.islem_basarili')]);
        } catch (\Exception $e) {
            if($request->basarisiz_donus_url != "")
                return redirect('/'.$request->basarisiz_donus_url)
                    ->withInput()
                    ->withErrors($e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }
}
