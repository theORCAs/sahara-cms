<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Sahara Training">
    <meta name="keywords" content="Confirmation Letter">
    <title>Course Outline - Sahara Training</title>
    <style>
        body{
            font-size: 11px;
        }
    </style>
</head>
<body>
<table style="width: 100%">
    <thead>
    <tr>
        <td>
            @if(!empty($header_resim))
                <img src="{{URL::to($header_resim)}}" style="width: 700px;">
            @else
                <div style="height: 130px;">&nbsp;</div>
            @endif
        </td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div style="width: 100%; text-align: center; border: 0px solid #000000;"><h4 style="margin: 2px;">{{$data->egitimler["kodu"]." ".$data->egitimler["adi"]}}</h4></div>
        </td>
    </tr>
    <tr>
        <td><div><b>Start Date: </b>{{$data->egitimTarihi["baslama_tarihi"]}}</div></td>
    </tr>
    <tr>
        <td><div><b>Course Duration: </b>{{$data->egitimTarihi["egitim_suresi"]." ".$data->egitimTarihi->egitimPart["adi"]}}</div></td>
    </tr>
    <tr>
        <td><div><b>Course Location: </b>{{$data->egitimTarihi->egitimYeri["adi"]}}</div></td>
    </tr>
    <tr>
        <td><div><h3>Course Description</h3></div></td>
    </tr>
    <tr>
        <td><div style="">{!! $data->egitimler["keyword"] !!}</div></td>
    </tr>
    <tr>
        <td><div><h3>Course Objective</h3></div></td>
    </tr>
    <tr>
        <td><div style="">{!! $data->egitimler["objective"] !!}</div></td>
    </tr>
    <tr>
        <td><div><h3>Who Should Attend?</h3></div></td>
    </tr>
    <tr>
        <td><div style="">{!! $data->egitimler["attend"] !!}</div></td>
    </tr>
    <tr>
        <td>
            <h3>Course Detail/Schedule</h3>
            @foreach($data->egitimler->egitimProgram as $key => $row)
                <div><h5>{{$data->egitimTarihi->egitimPart["adi"]}} {{$key + 1}}</h5></div>
                <div>{!! $row["icerik"]; !!}</div>
            @endforeach
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
