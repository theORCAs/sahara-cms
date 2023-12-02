<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Sahara Training">
    <meta name="keywords" content="Course Evaluation Form">
    <style type="text/css">
       .text-center {
           text-align: center;
           text-align: center;
       }
    </style>
    <title>Course Evaluation Form - Sahara Training</title>
</head>
<body>
<table style="width: 100%">
    <thead>
    <tr>
        <td><img src="{{URL::to('/')."/storage/images/sahara_antet.jpg"}}" style="width: 700px;"></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div style="width: 100%; text-align: center; border: 0px solid #000000;"><h3 style="margin: 2px;">Course Evaluation Form</h3></div>
        </td>
    </tr>
    </tbody>
</table>
<div>&nbsp;</div>
<div>We would appreciate if you could give a few minutes to evaluate the course. Your feedback will assist us in improving our course offerings. Thanks for your time and trust on us.</div>
<div>&nbsp;</div>
<table>
    <tbody>
    <tr>
        <td><b>Participant Name</b></td>
        <td>: {{$katilimci->adi_soyadi}}</td>
    </tr>
    <tr>
        <td><b>Company Name</b></td>
        <td>: {{$egitim_kayit->sirketReferans->adi == "" ? $egitim_kayit->sirket_adi : $egitim_kayit->sirketReferans->adi}}</td>
    </tr>
    <tr>
        <td><b>Course Title</b></td>
        <td>: {{$egitim_kayit->egitimler->kodu." ".$egitim_kayit->egitimler->adi}}</td>
    </tr>
    <tr>
        <td><b>Course Start Date</b></td>
        <td>: {{date('d.m.Y', strtotime($egitim_kayit->egitimTarihi->baslama_tarihi))}}</td>
    </tr>
    <tr>
        <td><b>Course Location</b></td>
        <td>: {{$egitim_kayit->egitimTarihi->egitimYeri->adi}}</td>
    </tr>
    </tbody>
</table>
<div>&nbsp;</div>
@if($hocalar_listesi->count() > 0)
    <table style="width: 100%; border:1px solid #000000;" border="1" cellspacing="0">
        <tbody>
        <tr>
            <td><b>Course Instructor(s)</b></td>
            <td><b>Question(s)</b></td>
            <td class="text-center"><b>Excellent</b></td>
            <td class="text-center"><b>V.Good</b></td>
            <td class="text-center"><b>Good</b></td>
            <td class="text-center"><b>Fair / Ave</b></td>
            <td class="text-center"><b>Poor</b></td>
        </tr>
        @foreach($hocalar_listesi as $row)
            <tr>
                <td rowspan="3">{{$row->adi_soyadi}}</td>
                <td>Field Knowledge</td>
                <td class="text-center">{{$row->soru1 == 5 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru1 == 4 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru1 == 3 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru1 == 2 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru1 == 1 ? 'X' : ''}}</td>
            </tr>
            <tr>
                <td>Presentation Skills</td>
                <td class="text-center">{{$row->soru2 == 5 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru2 == 4 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru2 == 3 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru2 == 2 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru2 == 1 ? 'X' : ''}}</td>
            </tr>
            <tr>
                <td>Course Material</td>
                <td class="text-center">{{$row->soru3 == 5 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru3 == 4 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru3 == 3 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru3 == 2 ? 'X' : ''}}</td>
                <td class="text-center">{{$row->soru3 == 1 ? 'X' : ''}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
<div>&nbsp;</div>
<table style="width: 100%; border:1px solid #000000;" border="1" cellspacing="0">
    <tbody>
    <tr>
        <td><b>Additional Services</b></td>
        <td class="text-center"><b>Excellent</b></td>
        <td class="text-center"><b>V. Good</b></td>
        <td class="text-center"><b>Good</b></td>
        <td class="text-center"><b>Fair/ Ave</b></td>
        <td class="text-center"><b>Poor</b></td>
    </tr>
    @foreach($sorular_listesi as $row)
        @if($row->flg_radio != 1)
            @continue
            @endif
        <tr>
            <td>{{$row->adi}}</td>
            <td class="text-center">{{$row[$row->alan_adi] == 5 ? 'X' : ''}}</td>
            <td class="text-center">{{$row[$row->alan_adi] == 4 ? 'X' : ''}}</td>
            <td class="text-center">{{$row[$row->alan_adi] == 3 ? 'X' : ''}}</td>
            <td class="text-center">{{$row[$row->alan_adi] == 2 ? 'X' : ''}}</td>
            <td class="text-center">{{$row[$row->alan_adi] == 1 ? 'X' : ''}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div>&nbsp;</div>
@foreach($sorular_listesi as $row)
    @if($row->flg_radio == 1)
        @continue
    @endif
    <div><b>{{$row->adi}}</b></div>
    <p>{{$row[$row->alan_adi] != "" ? $row[$row->alan_adi] : '......'}}</p>
    @endforeach
</body>
</html>
