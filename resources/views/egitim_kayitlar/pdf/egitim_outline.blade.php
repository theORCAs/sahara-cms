<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Sahara Training">
    <meta name="keywords" content="Confirmation Letter">
    <title>Course Outline - Sahara Training</title>
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
            <div style="width: 100%; text-align: center; border: 0px solid #000000;"><h4 style="margin: 2px;">{{$data->kodu." ".$data->adi}}</h4></div>
        </td>
    </tr>
    @if($sch)
    <tr>
        <td>
            <div><h3>Schedule</h3></div>
            <div>
                <table style="width: 100%; border: 1px solid #000000;">
                    <tr>
                        <td><b>Start Date</b></td>
                        <td><b>Duration</b></td>
                        <td><b>Venue</b></td>
                        <td><b>Fees</b></td>
                    </tr>
                    @foreach($data->egitimGelecekTarihler as $row)
                        <tr>
                            <td>{{date('d.m.Y', strtotime($row->baslama_tarihi))}}</td>
                            <td>{{$row->egitim_suresi." ".$row->egitimPart->adi}}</td>
                            <td>{{$row->egitimYeri->adi}}</td>
                            <td>$ {{$row->egitimUcretiGetir()}}</td>
                        </tr>
                        @endforeach
                </table>
            </div>
        </td>
    </tr>
    @endif
    <tr>
        <td><div><h3>Course Description</h3></div></td>
    </tr>
    <tr>
        <td><div style="">{!! $data->keyword !!}</div></td>
    </tr>
    <tr>
        <td><div><h3>Course Objective</h3></div></td>
    </tr>
    <tr>
        <td><div style="">{!! $data->objective !!}</div></td>
    </tr>
    <tr>
        <td><div><h3>Who Should Attend?</h3></div></td>
    </tr>
    <tr>
        <td><div style="">{!! $data->attend !!}</div></td>
    </tr>
    <tr>
        <td><div><h3>Course Detail/Schedule</h3></div></td>
    </tr>
    @foreach($data->egitimProgram as $key => $row)
        <tr>
            <td>
                <div><h5>{{$data->egitimPart["adi"]}} {{$key + 1}}</h5></div>
                <div>{!! $row["icerik"]; !!}</div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
