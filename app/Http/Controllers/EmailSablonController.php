<?php

namespace App\Http\Controllers;

use App\Http\Models\EmailSablon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmailSablonController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templete_data = EmailSablon::where("flg_doc_templete", 1)
            ->where("flg_silindi", 0)
            ->orderby("sira")->get();
        $participants_data = EmailSablon::where("tur", 1)
            ->where("flg_silindi", 0)
            ->where("flg_doc_templete", 0)
            ->orderby("sira")->get();
        $instructors_data = EmailSablon::where("tur", 2)
            ->where("flg_silindi", 0)
            ->where("flg_doc_templete", 0)
            ->orderby("sira")->get();
        $guest_data = EmailSablon::where("tur", 3)
            ->where("flg_silindi", 0)
            ->where("flg_doc_templete", 0)
            ->orderby("sira")->get();
        $hotel_data = EmailSablon::where("tur", 4)
            ->where("flg_silindi", 0)
            ->where("flg_doc_templete", 0)
            ->orderby("sira")->get();

        return view("email_sablon.view", [
            "templete" => $templete_data,
            "participant" => $participants_data,
            "instructor" => $instructors_data,
            "guest" => $guest_data,
            "hotels" => $hotel_data
        ]);
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
     * @param  \App\EmailSablon  $emailSablon
     * @return \Illuminate\Http\Response
     */
    public function show(EmailSablon $emailSablon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmailSablon  $emailSablon
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailSablon $emailSablon, $id)
    {
        $data = $emailSablon->findorfail($id);
        if($data->flg_doc_templete == 1) {
            return view('email_sablon.edit', ["data" => $data, "id" => $id]);
        } else {
            return view('email_sablon.edit_participant', ["data" => $data, "id" => $id]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmailSablon  $emailSablon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailSablon $emailSablon, $id)
    {
        try {
            $data = EmailSablon::find($id);
            if($data->flg_doc_templete == 1) {
                $upd_data = array(
                    "aciklama" => $request->input("aciklama"),
                    "alan2" => $request->input("alan2"),
                    "alan3" => $request->input("alan3"),
                    "alan7" => $request->alan7,
                );
                if($request->file("alan1") != "") {
                    $upd_data["alan1"] = $request->file("alan1")->store("public/teklif");
                }
                EmailSablon::find($id)->update($upd_data);
            } else {
                $upd_data = array(
                    "tur" => $request->input("tur"),
                    "aciklama" => $request->input("aciklama"),
                    "alan4" => $request->input("alan4"),
                    "alan6" => $request->input("alan6"),
                    "alan7" => $request->input("alan7"),
                    "alan2" => $request->input("alan2")
                );
                if($request->file("alan3") != "") {
                    $upd_data["alan3"] = $request->file("alan3")->store("public/teklif");
                }
                if($request->file("alan5") != "") {
                    $upd_data["alan5"] = $request->file("alan5")->store("public/teklif");
                }
                EmailSablon::find($id)->update($upd_data);
            }

            return redirect('/form_setup')->with(["msj" => config("messages.islem_basarili")]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        /*
        $path = $request->file('alan1')->storeAs(
            'public/teklif', 'deneme.jpg'
        );
        */
        // $path = $request->file("alan1")->store("public/teklif");
        // echo $path;
        // echo $request->file('alan1');
        // $obj = $emailSablon->find($request->input(id))
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmailSablon  $emailSablon
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailSablon $emailSablon)
    {
        //
    }
}
