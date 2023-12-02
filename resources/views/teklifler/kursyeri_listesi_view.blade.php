@extends('layouts.main')

@section('content')
    <!-- icerik buraya girilir -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Meeting Room Reservation
            <small>{{$alt_baslik}} / Year {{$yil}}
                <select id="select_hafta" onchange="haftaDegistir()">
                    @for($i = $hafta - 2; $i < $hafta + 5; $i++)
                        @if($i > 52)
                            @php($yil+=1)
                            @endif
                        <option value="{{date("Y-m-d", strtotime($yil."W".sprintf('%02d', $i)." +5 days"))}}" {{$i == $hafta ? ' selected' : ''}}>{{$i." - ".date("d.m.Y", strtotime($yil."W".sprintf('%02d', $i)." +5 days"))}}</option>
                    @endfor
                </select>
            </small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
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
        <div class="row">
            <div class="col-sm-12">
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        @if($data["id"] > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/meetingRoomReservationSave/{{$data['id']}}">
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}/meetingRoomReservationSave">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">City</label>
                                        <div class="col-sm-9">
                                            <select id="sehir_id" name="sehir_id" class="form-control select2" onchange="bolgeGetirJson()">
                                                <option value="">Select</option>
                                                @foreach($sehirler as $sehir)
                                                    <option value="{{$sehir->id}}" {{old('sehir_id', $data['sehir_id']) == $sehir->id ? ' selected' : ''}}>{{$sehir->adi}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Region</label>
                                        <div class="col-sm-9">
                                            <select id="bolge_id" name="bolge_id" class="form-control select2" onchange="otelleriGetirJson()">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Hotel</label>
                                        <div class="col-sm-9">
                                            <select id="otel_id" name="otel_id" class="form-control select2">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Room/Hall Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="oda_adi" name="oda_adi" class="form-control" value="{{old('oda_adi', $data['oda_adi'])}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"># of Person</label>
                                        <div class="col-sm-2">
                                            <input type="text" id="kisi_sayisi" name="kisi_sayisi" class="form-control" value="{{old('kisi_sayisi', $data['kisi_sayisi'])}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"># of Days</label>
                                        <div class="col-sm-2">
                                            <input type="text" id="kac_gun" name="kac_gun" class="form-control" value="{{old('kac_gun', $data['kac_gun'])}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Start Date</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="baslama_tarihi" name="baslama_tarihi" class="form-control date-picker"
                                                   value="{{old('baslama_tarihi', $data['baslama_tarihi']) != '' ? date('d.m.Y', strtotime(old('baslama_tarihi', $data['baslama_tarihi']))) : ''}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Ucret</label>
                                        <div class="col-sm-2">
                                            <input type="text" id="ucret" name="ucret" class="form-control" value="{{old('ucret', $data['ucret'])}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-4 col-md-12">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Save</button>
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                        @if($kurs_yeri_id > 0) <a class="btn blue" href="/{{$prefix}}/meetingRoomReservationView/{{$tarih}}">Add New Empty Form</a>@endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i>Week: {{$hafta}} - List of Meeting Room </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th scope="col"> # </th>
                                    <th scope="col"> Training Location/Course Title </th>
                                    <th scope="col"> Room/Hall Name </th>
                                    <th scope="col"> City </th>
                                    <th scope="col"> Region </th>
                                    <th scope="col"> Person </th>
                                    <th scope="col"> Start </th>
                                    <th scope="col"> Day </th>
                                    <th scope="col"> Fee </th>
                                    <th scope="col"> Status </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($liste as $key => $row)
                                    <tr>
                                        <td>{{$key + 1}}
                                            <div>{{$row->hafta}}</div>
                                        </td>
                                        <td>
                                            <div>{{$row->otel_adi}}</div>
                                            <div class="font-red">{{$row->egitim_adi}}</div>
                                        </td>
                                        <td>{{$row->oda_adi}}</td>
                                        <td>{{$row->sehir_adi}}</td>
                                        <td>{{$row->bolge_adi}}</td>
                                        <td>{{$row->kisi_sayisi}}</td>
                                        <td>W:{{date('W', strtotime($row->baslama_tarihi))}}-{{date('d.m.Y', strtotime($row->baslama_tarihi))}}</td>
                                        <td>{{$row->kac_gun}}</td>
                                        <td>{{$row->ucret > 0 ? $row->ucret : ''}}</td>
                                        <td class="text-center">
                                            @if($row->teklif_id == "")
                                                <div class="font-green">Available</div>
                                                <div>
                                                    <a href="/{{$prefix}}/meetingRoomReservationView/{{$tarih}}/{{$row->id}}" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->otel_adi}}'"><i class="fa fa-pencil"></i></a>
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->otel_adi}}'" onclick="silmeUyari('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                                                </div>
                                            @else
                                                <div class="font-red">Assigned</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyları yuklenir -->
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <!-- js dosyları yuklenir -->
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $("document").ready(function () {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
            $('.date-picker').datepicker('setStartDate', "{{$tmp_tarih_bas}}");
            $('.date-picker').datepicker('setEndDate', "{{$tmp_tarih_bit}}");

            bolgeGetirJson();
        });

        function bolgeGetirJson() {
            var secili_bolge_id = "{{old('bolge_id', $data["bolge_id"])}}";
            var data = {
                "_token" : "{{csrf_token()}}",
                "sehir_id" : $("#sehir_id").val(),
            }
            showLoading('', '');
            $.post("/{{$prefix}}/meetingRoomReservationBolgeGetirJson", data, function (cevap) {
                $("#bolge_id option:first").prop("selected", true);
                $("#bolge_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#bolge_id").append("<option value='" + row.id + "' " + (row.id == secili_bolge_id ? ' selected' : '')+ ">" + row.adi + "</option>");
                })
            }, "json").done(function () {
                $("#bolge_id").trigger("change");
                hideLoading('');
            });
        }

        function otelleriGetirJson() {
            var secili_otel_id = "{{old('otel_id', $data["otel_id"])}}";
            var data = {
                "_token" : "{{csrf_token()}}",
                "bolge_id" : $("#bolge_id").val(),
            }
            showLoading('', '');
            $.post("/{{$prefix}}/meetingRoomReservationOtelGetirJson", data, function (cevap) {
                $("#otel_id option:first").prop("selected", true);
                $("#otel_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#otel_id").append("<option value='" + row.id + "' " + (row.id == secili_otel_id ? ' selected' : '')+ ">" + row.adi + "</option>");
                })
            }, "json").done(function () {
                $("#otel_id").trigger("change");
                hideLoading('');
            });
        }

        function silmeUyari(id) {
            bootbox.confirm("Do yo want to delete?", function (result) {
                if(result) {
                    var data = {
                        "_token" : "{{csrf_token()}}",
                        "id" : id
                    };
                    showLoading('', '');
                    $.post("/{{$prefix}}/meetingRoomReservationDelJson", data, function (cevap) {
                        if(cevap.cvp == "1") {
                            window.location.href="/{{$prefix}}/meetingRoomReservationView/{{$tarih}}";
                        }
                    }).done(function () {
                        hideLoading('');
                    })
                }
            })
        }

        function haftaDegistir() {
            var hafta = $("#select_hafta").val()
            window.location.href = "/{{$prefix}}/meetingRoomReservationView/" + hafta;
        }
    </script>
@endsection
