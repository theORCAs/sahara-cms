@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Send Email
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
                                    <label class="col-sm-2 control-label">Template Category</label>
                                    <div class="col-sm-4">
                                        <select id="template_tur_id" name="template_tur_id" class="select2 form-control" onchange="sablonGetirJson()">
                                            <option value="">Select</option>
                                            @foreach($sablon_turleri as $row)
                                                <option value="{{$row->id}}" {{old('template_tur_id', $data->template_tur_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Message to be sent</label>
                                    <div class="col-md-8">
                                        <select id="template_id" name="template_id" class="select2 form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">CC eMail(s)</label>
                                    <div class="col-md-6">
                                        <textarea id="cc_mails" name="cc_mails" rows="2" class="form-control">{{old('cc_mails', $data->cc_mails)}}</textarea>
                                        <span class="help-inline"> Separate multiple emails by comma ( , ) </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Dynamic Groups - Customers</label>
                                    <div class="col-md-10">
                                        <select id="grup1" name="grup1[]" class="select2 form-control" multiple="true">
                                            @foreach($grup1_listesi as $key => $row)
                                                <option value="{{$row->id}}" {{old('grup1') != '' && in_array($row->id, old('grup1')) ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Dynamic Groups - Instructors</label>
                                    <div class="col-md-10">
                                        <select id="grup2" name="grup2[]" class="select2 form-control" multiple="true">
                                            @foreach($grup2_listesi as $key => $row)
                                                <option value="{{$row->id}}" {{old('grup2') != '' && in_array($row->id, old('grup2')) ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Static Groups</label>
                                    <div class="col-md-10">
                                        <select id="grup3" name="grup3[]" class="select2 form-control" multiple="true">
                                            @foreach($grup3_listesi as $row)
                                                <option value="{{$row->id}}" {{old('grup3') != '' && in_array($row->id, old('grup3')) ? ' selected' : '' }}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Country</label>
                                    <div class="col-md-3">
                                        <select id="ulke_id" name="ulke_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($ulke_listesi as $row)
                                                <option value="{{$row->id}}" {{old('ulke_id', $data->ulke_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-2 control-label">Send Date</label>
                                    <div class="col-md-2">
                                        <input type="text" id="gonderim_tarihi" name="gonderim_tarihi"
                                               class="date-picker form-control" value="{{old('gonderim_tarihi', ($data->gonderim_tarihi != '' ? date('d.m.Y', strtotime($data->gonderim_tarihi)) : '' ) )}}">
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

    <script type="text/javascript">
        $(document).ready(function () {
            var tmp_date = ("{{$data->gonderim_tarihi}}" != "" ? "{{$data->gonderim_tarihi}}" : "{{date('Y-m-d')}}");
            $(".select2").select2();

            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy',
                startDate: new Date(tmp_date)
            });

            sablonGetirJson();

            $("#grup1").trigger('change');
        });
        function sablonGetirJson() {
            var tmp_template_id = "{{old('template_id', $data->template_id)}}";
            var data = {
                '_token' : "{{csrf_token()}}",
                'template_tur_id' : $("#template_tur_id").val()
            };
            $("#template_id option:first").prop('selected', true);
            $("#template_id option:gt(0)").remove();
            $.post('/em_sendemail/sablonGetirJson', data, function (cevap) {
                $.each(cevap, function (i, row) {
                    $("#template_id").append("<option value='" + row.id + "' " + (tmp_template_id == row.id ? ' selected' : '') + ">" + row.adi + "</option>");
                });
            }, "json").done(function () {
                $("#template_id").trigger('change');
            });
        }
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
