<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Sahara Training">
    <meta name="keywords" content="Confirmation Letter">
    <title>Confirmation Letter - Sahara Training</title>
    <style>
        body{
            font-size: 11px;
        }
    </style>
</head>
<body>
@if(!empty($header_resim))
    <img src="{{URL::to($header_resim)}}" style="width: 700px;">
@else
    <div style="height: 130px;">&nbsp;</div>
@endif
<div style="width: 100%; text-align: center; border: 0px solid #000000;"><h3 style="margin: 2px;">Terms of Service Proposal</h3></div>
<div style="width: 100%; text-align: center; border: 0px solid #000000;"><h4 style="margin: 2px;">{{$sirket_adi}}</h4></div>
<div style="width: 100%; text-align: center; border: 0px solid #000000;"><h5 style="margin: 2px;">{{$sirket_ulke}}</h5></div>
<div><b>Subject: </b>{{$data->konu}}</div>
<div><b>Reference: </b>{{$data->referans}}</div>
<div style="">{!! $data->icerik !!}</div>
<table style="width: 100%">
    <tbody>
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <div style="font-size: 13px;">{!! nl2br($data->alt_bilgi) !!}</div>
        </td>
        <td style="vertical-align: top; text-align: center">
            @if(!empty($imza_resim))
                <img src="{{URL::to($imza_resim)}}" style="width: 200px;">
            @else
                <div style="height: 130px;">&nbsp;</div>
            @endif
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
