<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Sahara Training">
    <meta name="keywords" content="Invoice">
    <title>Invoice - Sahara Training</title>
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
<div style="width: 100%; text-align: center; border: 0px solid #000000;"><h1 style="margin-top: -35px">Invoice</h1></div>
<div><b>Date: </b>{{date('d F Y', strtotime($data->tarih))}}</div>
<div><b>Referans No: </b>{{$data->referans_no}}</div>
<div><b>Company: </b>{{$data->sirket_adi}}</div>
<div><b>Address: </b>{{$data->sirket_adres}}</div>
<div><b>Country: </b>{{$data->sirketUlkesi["adi"]}}</div>
@php
    $amount = floatval($data->miktar) * floatval($data->ucret);
    $sub_total = $amount;
    $total = $sub_total + floatval($data->genel_indirim);
    $rowspan = 2;
    if($data->genel_indirim){
        $rowspan = 3;
    }
@endphp
<table cellpadding="0" cellspacing="0" border="1" style="width: 100%;">
    <tbody>
    <tr>
        <td><b>Service Description</b></td>
        <td style="width: 12%; text-align: center;"><b>Quantity</b></td>
        <td style="width: 12%; text-align: center;"><b>Unit Price</b></td>
        <td style="width: 12%; text-align: center"><b>Amount ({{$data->paraBirim->adi}})</b></td>
    </tr>
    <tr>
        <td style="font-family: DejaVu Sans">{!! $data->aciklama !!}</td>
        <td style="text-align: center; vertical-align: middle;">{{$data->miktar}}</td>
        <td style="text-align: center; vertical-align: middle;">{{$data->ucret}}</td>
        <td style="text-align: center; vertical-align: middle;">{{$amount}}</td>
    </tr>
    <tr>
        <td rowspan="{{$rowspan}}"></td>
        <td colspan="2" style="text-align: right;"><b>Sub-Total</b></td>
        <td style="text-align: center;">{{$sub_total}}</td>
    </tr>
    @if($data->genel_indirim)
        <tr>
            <td colspan="2" style="text-align: right;"><b>Surcharge/Discount</b></td>
            <td style="text-align: center;">{{$data->genel_indirim}}</td>
        </tr>
    @endif
    <tr>
        <td colspan="2" style="text-align: right;"><b>Total</b></td>
        <td style="text-align: center;">{{$total}}</td>
    </tr>
    <tr>
        <td colspan="4">{{$data->x}}</td>
    </tr>
    </tbody>
</table>
<table style="width: 100%">
    <tbody>
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <div style="margin-top: 10px;">{{$data->ekalan_alt}}</div>
            <div>{{$data->ekalan_1}}</div>
            <div><b>Name: </b>{{$data->isim}}</div>
            <div><b>Position: </b>{{$data->pozisyon}}</div>
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
<div style="font-size: 8.5px">{!! nl2br($data->banka_detay) !!}</div>
</body>
</html>
