<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Certificate</title>
</head>
<body style="margin: 0px;">
<table style="">
    <tr>
        <td style="background-image: url('http://www.saharatraining.com/panel/certificate/sertifika.jpg'); background-size: 279mm 200mm; width: 277mm; height: 195mm; text-align: center;">
            <table style="width: 100%; height:40px; margin-top:90px;">
                <tr>
                    <td style="font-size:15px; font-family: Optima; color:#000; padding-left:0px;">CERTIFICATE REF. NO: {{ sprintf("%05d", $katilimci_id) }}</td>
                </tr>
            </table>
            <table style="width: 100%; height:50px; margin-top:20px;">
                <tr>
                    <td style="font-size:27px; font-family: Optima; color:#000;">To Certify That</td>
                </tr>
            </table>
            <table style="width: 100%; height:100px; margin-top:10px;">
                <tr>
                    <td style="font-family: Comic Sans MS; font-size:37px; color: #6a3837;">{{$adi_soyadi}}</td>
                </tr>
                <tr>
                    <td style="font-family: Optima; font-size:22px; color:#000;">Has attended and successfully completed the following training program:</td>
                </tr>
            </table>
            <table style="width: 100%; height:220px; margin-top:20px;">
                <tr>
                    <td style="font-family: Optima; font-size:30px; color:#000; font-weight:bold;">{{$egitim_adi}}</td>
                </tr>
                <tr>
                    <td style="font-family: Optima; font-size:28px; color:#000; font-weight:normal;">{{$baslama_tarihi." - ".$bitis_tarihi}}<br />{{$egitim_yeri.($egitim_yeri == 'Istanbul' ? ', Turkey' : '')}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script type="text/javascript">

    window.print();
    setTimeout(function () {
        window.close();
    }, 1000);

</script>
</body>
</html>
