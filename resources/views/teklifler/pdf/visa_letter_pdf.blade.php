<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="author" content="Sahara Training">
    <meta name="keywords" content="VISA Letter">
    <title>Visa Letter - Sahara Training</title>
    <style type="text/css">
        body {
            font-family: "DejaVu sans";
            font-size: 12px;
        }
    </style>
</head>
<body>
<table style="width: 100%">
    <thead>
    <tr>
        <td><img src="{{URL::to('/')."/storage/images/sahara_antet.jpg"}}" style="width: 700px;"></td>
    </tr>
    </thead>
</table>
<div style="width: 100%; text-align: justify;">
    {!! $icerik !!}
</div>
<table style="width: 100%">
    <tbody>
    <tr>
        <td style="width: 60%; vertical-align: top;">
            {!! nl2br($alt_kisim) !!}
        </td>
        <td style="vertical-align: top; text-align: center"><img src="{{URL::to($imza_resim)}}" style="width: 200px;"></td>
    </tr>
    </tbody>
</table>
</body>
</html>
