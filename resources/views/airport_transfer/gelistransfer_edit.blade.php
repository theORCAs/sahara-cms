@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Confirmed-Arrivals
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-8">
                @if($errors->count() > 0)
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-warning"></i> Error</h4>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                @if(Session::has("msj"))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> Success</h4>
                        {{Session::get("msj")}}
                    </div>
                @endif
                @if(session()->has("err_msj"))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-warning"></i> Error</h4>
                        {{Session::get("err_msj")}}
                    </div>
                @endif
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        @if($data["id"] > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}">
                            @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">A- Course Detail</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Title</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{$data->teklif->egitimKayit->egitimler['kodu']." ".$data->teklif->egitimKayit->egitimler['adi']}}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Start Date</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{date('d.m.Y', strtotime($data->teklif->egitimKayit->egitimTarihi['baslama_tarihi']))}}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">B - Flight Details (Arrival)</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Arrival Airlines</label>
                                    <div class="col-sm-6">
                                        <select name="gelis_havayolu_id" id="gelis_havayolu_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($havayollari as $row)
                                                <option value="{{$row->id}}" {{old('gelis_havayolu_id', $data['gelis_havayolu_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Arrival Airport</label>
                                    <div class="col-sm-6">
                                        <select name="gelis_havaalani_id" id="gelis_havaalani_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($havaalanlari as $row)
                                                <option value="{{$row->id}}" {{old('gelis_havaalani_id', $data['gelis_havaalani_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Flight No</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="gelis_ucus_no" name="gelis_ucus_no" class="form-control" value="{{old('gelis_ucus_no', $data["gelis_ucus_no"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Arrival Date</label>
                                    <div class="col-md-2">
                                        <input type="text" id="gelis_tarih" name="gelis_tarih"
                                               class="form-control date-picker"
                                               value="{{old('gelis_tarih', $data['gelis_tarih']) != "" ? date("d.m.Y", strtotime(old('gelis_tarih', $data['gelis_tarih']))) : ""}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Arrival Time</label>
                                    <div class="col-md-2">
                                        <input type="text" id="gelis_saat" name="gelis_saat"
                                               class="form-control"
                                               value="{{old('gelis_saat', $data['gelis_saat']) != "" ? date("H:i", strtotime(old('gelis_saat', $data['gelis_saat']))) : "" }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">C - Flight Details (Departure)</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Departure Airlines</label>
                                    <div class="col-sm-6">
                                        <select name="gidis_havayolu_id" id="gidis_havayolu_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($havayollari as $row)
                                                <option value="{{$row->id}}" {{old('gidis_havayolu_id', $data['gidis_havayolu_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Departure Airport</label>
                                    <div class="col-sm-6">
                                        <select name="gidis_havaalani_id" id="gidis_havaalani_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($havaalanlari as $row)
                                                <option value="{{$row->id}}" {{old('gidis_havaalani_id', $data['gidis_havaalani_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Flight No</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="gidis_ucus_no" name="gidis_ucus_no" class="form-control" value="{{old('gidis_ucus_no', $data["gidis_ucus_no"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Departure Date</label>
                                    <div class="col-md-2">
                                        <input type="text" id="gidis_tarih" name="gidis_tarih"
                                               class="form-control date-picker"
                                               value="{{old('gidis_tarih', $data['gidis_tarih']) != "" ? date("d.m.Y", strtotime(old('gidis_tarih', $data['gidis_tarih']))) : ""}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Departure Time</label>
                                    <div class="col-md-2">
                                        <input type="text" id="gidis_saat" name="gidis_saat"
                                               class="form-control"
                                               value="{{old('gidis_saat', $data['gidis_saat']) != "" ? date("H:i", strtotime(old('gidis_saat', $data['gidis_saat']))) : ""}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">D - Passenger(s) Details</label>
                                    </div>
                                </div>
                                @foreach($data->kisiler as $key => $row)
                                    <div class="form-group">
                                        <label class="col-md-2 control-label font-red">{{$key + 1}}. Passenger</label>
                                        <div class="col-md-6">
                                            <input type="hidden" id="k_id" name="k_id[]" value="{{$row->id}}">
                                            <input type="text" id="k_adi" name="k_adi[]" class="form-control" value="{{old('k_adi.'.$key, $row->adi)}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Passport No</label>
                                        <div class="col-md-6">
                                            <input type="text" id="k_pasaport_no" name="k_pasaport_no[]" class="form-control" value="{{old('k_pasaport_no.'.$key, $row->pasaport_no)}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Email</label>
                                        <div class="col-md-6">
                                            <input type="text" id="k_email" name="k_email[]" class="form-control" value="{{old('k_email.'.$key, $row->email)}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Mobile (GSM) Phone</label>
                                        <div class="col-md-1">
                                            <input type="text" id="k_gsm_kodu" name="k_gsm_kodu[]" class="form-control" value="{{old('k_gsm_kodu.'.$key, $row->gsm_kodu)}}" placeholder="Code">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="k_gsm" name="k_gsm[]" class="form-control" value="{{old('k_gsm.'.$key, $row->gsm)}}" placeholder="Number">
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Purpose of Transfer</label>
                                        <div class="col-md-3">
                                            <select id="k_yakinlik_derecesi" name="k_yakinlik_derecesi[]" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($tasima_amac_listesi as $ts_row)
                                                    <option value="{{$ts_row->id}}" {{old('k_yakinlik_derecesi.'.$key, $row->yakinlik_derecesi) == $ts_row->id ? " selected" : ""}}>{{$ts_row->adi}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endforeach
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Hotel Name</label>
                                    <div class="col-md-8">
                                        <select name="otel_id" id="otel_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($oteller as $row)
                                                <option value="{{$row->id}}" {{old('otel_id', $data['otel_id']) == $row->id ? " selected" : ""}}>{{$row->adi." / ".$row->sehir->adi." / ".$row->bolge->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Other Hotel</label>
                                    <div class="col-md-6">
                                        <input type="text" name="otel_adi" id="otel_adi" class="form-control" value="{{old('otel_adi', $data['otel_adi'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Other Hotel Website</label>
                                    <div class="col-md-6">
                                        <input type="text" name="otel_website" id="otel_website" class="form-control" value="{{old('otel_website', $data['otel_website'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">E - SAHARA Group Contact Person Details</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label font-red">1. Contact Person</label>
                                    <div class="col-md-8">
                                        <select name="kontak_kisi_id" id="kontak_kisi_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($sorumlular as $row)
                                                <option value="{{$row->kullanici_id}}" {{old('kontak_kisi_id', $data['kontak_kisi_id']) == $row->kullanici_id ? " selected" : ""}}>{{$row->kullanicilar->adi_soyadi." / ".$row->roller->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mobile</label>
                                    <div class="col-md-2">
                                        <input type="text" name="kontak_cep" id="kontak_cep" class="form-control" value="{{old('kontak_cep', $data['kontak_cep'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email</label>
                                    <div class="col-md-4">
                                        <input type="text" name="kontak_email" id="kontak_email" class="form-control" value="{{old('kontak_email', $data['kontak_email'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label font-red">2. Contact Person</label>
                                    <div class="col-md-8">
                                        <select name="kontak2_kisi_id" id="kontak2_kisi_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($sorumlular as $row)
                                                <option value="{{$row->kullanici_id}}" {{old('kontak2_kisi_id', $data['kontak2_kisi_id']) == $row->kullanici_id ? " selected" : ""}}>{{$row->kullanicilar->adi_soyadi." / ".$row->roller->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mobile</label>
                                    <div class="col-md-2">
                                        <input type="text" name="kontak2_cep" id="kontak2_cep" class="form-control" value="{{old('kontak2_cep', $data['kontak2_cep'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email</label>
                                    <div class="col-md-4">
                                        <input type="text" name="kontak2_email" id="kontak2_email" class="form-control" value="{{old('kontak2_email', $data['kontak2_email'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Additional Contact Person</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" name="kontak3_isim" id="kontak3_isim" class="form-control" value="{{old('kontak3_isim', $data['kontak3_isim'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mobile</label>
                                    <div class="col-md-2">
                                        <input type="text" name="kontak3_cep" id="kontak3_cep" class="form-control" value="{{old('kontak3_cep', $data['kontak3_cep'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email</label>
                                    <div class="col-md-4">
                                        <input type="text" name="kontak3_email" id="kontak3_email" class="form-control" value="{{old('kontak3_email', $data['kontak3_email'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">F - Airport Transfer Company Details</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Airport > Hotel Transfer Company</label>
                                    <div class="col-md-8">
                                        <select name="gelis_tasima_firma_id" id="gelis_tasima_firma_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($tasima_firmalar[0]->firmalarListesi as $row)
                                                <option value="{{$row->id}}" {{old('gelis_tasima_firma_id', $data['gelis_tasima_firma_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Arrival Transfer Fee</label>
                                    <div class="col-md-2">
                                        <input type="text" name="gelis_tasima_ucreti" id="gelis_tasima_ucreti" class="form-control" value="{{old('gelis_tasima_ucreti', $data['gelis_tasima_ucreti'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Additional Notes (Arrival)</label>
                                    <div class="col-md-6">
                                        <textarea name="gelis_ek_notlar" id="gelis_ek_notlar" rows="3" class="form-control">{{old('gelis_ek_notlar', $data['gelis_ek_notlar'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Hotel > Airport Transfer Company</label>
                                    <div class="col-md-8">
                                        <select name="gidis_tasima_firma_id" id="gidis_tasima_firma_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($tasima_firmalar[0]->firmalarListesi as $row)
                                                <option value="{{$row->id}}" {{old('gidis_tasima_firma_id', $data['gidis_tasima_firma_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Departure Transfer Fee</label>
                                    <div class="col-md-2">
                                        <input type="text" name="gidis_tasima_ucreti" id="gidis_tasima_ucreti" class="form-control" value="{{old('gidis_tasima_ucreti', $data['gidis_tasima_ucreti'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Additional Notes (Departure)</label>
                                    <div class="col-md-6">
                                        <textarea name="gidis_ek_notlar" id="gidis_ek_notlar" rows="3" class="form-control">{{old('gidis_ek_notlar', $data['gidis_ek_notlar'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Status</label>
                                    <div class="col-md-2">
                                        <select name="durum" id="durum" class="form-control">
                                            <option value="1" {{old('durum', $data['durum']) == 1 ? " selected" : ""}}>Request </option>
                                            <option value="2" {{old('durum', $data['durum']) == 2 ? " selected" : ""}}>Confirmed </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-2 control-label">Send Email</label>
                                    <div class="col-md-2">
                                        <input type="checkbox" name="tekrar_mail_gonder" id="tekrar_mail_gonder" class="form-control" value="1" checked> Yes
                                    </div>
                                </div>

                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Submit</button>
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
            $("#gelis_saat, #gidis_saat").inputmask({mask:"99:99"});
        });
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
