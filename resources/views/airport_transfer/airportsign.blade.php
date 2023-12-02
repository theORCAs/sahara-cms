<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Sahara Training | CMS</title>
    </head>
    <body>

        @foreach($bilgi->kisiler as $row)
            <table cellspacing='0' cellpadding='2' border='0' width='1024' height='700' style="page-break-after: always;">
                <tr>
                    <td style="font-size: 150px;">{{$row->adi}}</td>
                </tr>
                <tr>
                    <td align='right' style='height:110px; bottom:0px; vertical-align:bottom;'>
                        <table style='font-size:9px;'>
                            <tr>
                                <td>Date : </td>
                                <td>{{date('d.m.Y', strtotime($bilgi->teklif->egitimKayit->egitimTarihi['baslama_tarihi']))}}</td>
                            </tr>
                            <tr>
                                <td>Arrival Time : </td>
                                <td>{{date("d.m.Y H:i", strtotime($bilgi->gelis_tarih." ".$bilgi->gelis_saat))}}</td>
                            </tr>
                            <tr>
                                <td>Flight number : </td>
                                <td>{{$bilgi->gelis_ucus_no}}</td>
                            </tr>
                            <tr>
                                <td>Hotel : </td>
                                <td>@if($bilgi->otel_adi != ""){{$bilgi->otel_adi}}@else{{$bilgi->otel->adi}}@endif</td>
                            </tr>
                            <tr>
                                <td># of people : </td>
                                <td>{{$bilgi->kisiler->count()}}</td>
                            </tr>
                            <tr>
                                <td>Course : </td>
                                <td>{{$bilgi->teklif->egitimKayit->egitimler['kodu']." ".$bilgi->teklif->egitimKayit->egitimler['adi']}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @endforeach
    </body>
</html>
<script type="text/javascript">

        window.print();
        setTimeout(function () {
            window.close();
        }, 500);

</script>
