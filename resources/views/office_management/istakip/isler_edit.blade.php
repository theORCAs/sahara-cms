@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Job Follow-up Module
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
                                    <label class="col-sm-2 control-label">Job Category</label>
                                    <div class="col-sm-6">
                                        <select name="kategori_id" id="kategori_id" class="select2 form-control" onchange="isturleriGetir()">
                                            <option value="">Select</option>
                                            @foreach($kategori_listesi as $row)
                                                <option value="{{$row->id}}" {{old('kategori_id', $data->kategori_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Job Type / Frequency</label>
                                    <div class="col-md-6">
                                        <select name="isturu_id" id="isturu_id" class="select2 form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label font-red">Requester/Inquirer</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" id="adi_soyadi" name="adi_soyadi" class="form-control" value="{{old('adi_soyadi', $data["adi_soyadi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email</label>
                                    <div class="col-md-6">
                                        <input type="text" id="email" name="email" class="form-control" value="{{old('email', $data["email"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Phone</label>
                                    <div class="col-md-3">
                                        <input type="text" id="telefon" name="telefon" class="form-control" value="{{old('telefon', $data["telefon"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Country</label>
                                    <div class="col-md-4">
                                        <select name="ulke_id" id="ulke_id" class="select2 form-control" onchange="sirketListeGetir()">
                                            <option value="">Select</option>
                                            @foreach($ulke_listesi as $row)
                                                <option value="{{$row->id}}" {{old('ulke_id', $data->ulke_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Company</label>
                                    <div class="col-md-8">
                                        <select name="ref_sirket_id" id="ref_sirket_id" class="select2 form-control" onchange="sirketYokDurumDegistir()">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group sirket-yok-container {{$data->ref_sirket_id == "" && $data->id != "" ? " " : " hidden"}}">
                                    <label class="col-md-2 control-label">Company Name</label>
                                    <div class="col-md-6">
                                        <input type="text" id="sirket_adi" name="sirket_adi" class="form-control" value="{{old('sirket_adi', $data["sirket_adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label font-red">Reported/Registered by</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Name</label>
                                    <div class="col-md-6">
                                        <select name="istek_yapan" id="istek_yapan" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($istek_yapan_liste as $row)
                                                <option value="{{$row->id}}" {{old('istek_yapan', $data->istek_yapan) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email</label>
                                    <div class="col-md-6">
                                        <input type="text" id="iy_email" name="iy_email" class="form-control" value="{{old('iy_email', $data["iy_email"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Phone</label>
                                    <div class="col-md-3">
                                        <input type="text" id="iy_telefon" name="iy_telefon" class="form-control" value="{{old('iy_telefon', $data["iy_telefon"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Date</label>
                                    <div class="col-md-2">
                                        <input type="text" id="is_tarihi" name="is_tarihi" class="date-picker form-control" value="{{old('is_tarihi', ($data->is_tarihi != "" ? date('d.m.Y', strtotime($data->is_tarihi)) : "") )}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Additional Notes</label>
                                    <div class="col-md-8">
                                        <textarea id="is_tanimi" name="is_tanimi" class="ckeditor">{{old('is_tanimi', $data->is_tanimi)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Action by</label>
                                    <div class="col-md-6">
                                        <select name="ilgili_kisi" id="ilgili_kisi" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($istek_yapan_liste as $row)
                                                <option value="{{$row->id}}" {{old('ilgili_kisi', $data->ilgili_kisi) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-2 control-label">Status</label>
                                    <div class="col-md-3">
                                        <select name="durum" id="durum" class="form-control">
                                            <option value="0" {{old('durum', $data->durum) == "0" ? " selected" : ""}}>Request Recorded</option>
                                            <option value="1" {{old('durum', $data->durum) == "1" ? " selected" : ""}}>Request Completed</option>
                                        </select>
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
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
            CKEDITOR.replace('is_tanimi', {
                height: 500
            });

            isturleriGetir();
            sirketListeGetir();
        });
        function formuKaydet() {
            showLoading('', '');
        }

        function isturleriGetir() {
            var data = {
                '_token' : "{{csrf_token()}}",
                'kategori_id' : $("#kategori_id").val()
            }
            var tmp_isturu_id = "{{old('isturu_id', $data->isturu_id)}}";

            $.post('/jfu_waiting/isTurleriGetirJson', data, function (cevap) {
                $("#isturu_id option:gt(0)").remove();
                $.each(cevap, function(i, row) {
                    $("#isturu_id").append("<option value='" + row.id + "' " + (row.id == tmp_isturu_id ? " selected" : "" ) + " >" + row.adi + " / " + row.isturu_adi + "</option>");
                });
            }, "json").done(function () {
                $("#isturu_id").trigger('change');
            });
        }

        function sirketListeGetir() {
            var data = {
                '_token' : "{{csrf_token()}}",
                'ulke_id' : $("#ulke_id").val()
            }
            var tmp_ref_sirket_id = "{{old('ref_sirket_id', $data->ref_sirket_id)}}";
            $.post('/jfu_waiting/sirketListeGetirJson', data, function (cevap) {
                $("#ref_sirket_id option:gt(0)").remove();
                $.each(cevap, function(i, row) {
                    $("#ref_sirket_id").append("<option value='" + row.id + "' " + (tmp_ref_sirket_id == row.id ? " selected" : '') + ">" + row.adi + "</option>");
                });
                $("#ref_sirket_id").append("<option value='-1' " + ( tmp_ref_sirket_id == '-1' ? 'selected' : '') + ">Company NOT in the List</option>");
            }, "json").done(function () {
                $("#ref_sirket_id").trigger('change');
            });
        }

        function sirketYokDurumDegistir() {
            if($("#ref_sirket_id").val() == "-1")
                $(".sirket-yok-container").removeClass('hidden');
            else
                $(".sirket-yok-container").addClass('hidden');
        }
    </script>
@endsection
