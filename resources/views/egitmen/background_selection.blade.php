@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Background Selection (NON-confirmed list)
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
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

                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">A - Courses you can teach (deliver from our list)</div>
                        <div class="tools hidden">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form class="form-horizontal form-row-seperated" role="form" method="post" action="/{{$prefix}}">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Select Category</label>
                                    <div class="col-md-10">
                                        <select id="kategori_id" name="kategori_id" onchange="egitimleriGetir()">
                                            <option value="">Select</option>
                                            @foreach($kategori_liste as $kat_row)
                                                <option value="{{$kat_row->id}}">{{$kat_row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Courses in the selected category that can be selected</label>
                                    <div class="col-md-10">
                                        <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green">
                                            <i class="fa fa-check"></i> Submit</button>
                                        <button type="button" class="btn grey-salsa btn-outline">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">B - Courses Selected and experience Rated</div>
                        <div class="tools hidden">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            @if(sizeof($background_liste) == 0)
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Course Category </th>
                                        <th> Course Title </th>
                                        <th class="text-center"> Experience Rating </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($background_liste as $key => $row)
                                        <tr>
                                            <td>{{$key}}</td>
                                            <td>{{$row->egitim->egitimKategori->adi}}</td>
                                            <td>{{$row->egitim->adi}}</td>
                                            <td class="text-center">
                                                <select id="deneyim_select" data-id="{{$row->id}}" onchange="oylamaYap('{{$row->id}}')">
                                                    <option value="">Select</option>
                                                    @for($i = 0; $i<= 10; $i++)
                                                        <option value="{{$i}}" {{$row->rate == "$i" ? " selected" : ""}}>{{$i == "0" ? "No idea" : $i}}</option>
                                                        @endfor
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
@endsection
@section("css")
    <!--  css dosyaları yuklenir -->
    <link href="{{url('/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('/assets/global/plugins/jquery-multi-select/css/multi-select.css')}}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .ms-container {
            width: 840px;
        }
    </style>
@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script src="{{url('/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/global/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#kategori_id").select2();
            $('#my_multi_select1').multiSelect();
        });

        function egitimleriGetir() {
            var data = {
                "_token" : "{{csrf_token()}}",
                "kategori_id": $("#kategori_id").val()
            };

            $.post("/{{$prefix}}/kategoriGetirJson", data, function(cevap) {
                $("#my_multi_select1 option").remove();
                $.each(cevap, function(i, row) {
                    $("#my_multi_select1").append("<option value='" + row.id + "'>" + row.kodu + " " + row.adi + "</option>");
                });
            }, "json").done(function () {
                $("#my_multi_select1").multiSelect('refresh');
            });
        }

        function oylamaYap(id) {
            var data = {
                "_token" : "{{csrf_token()}}",
                "id" : id,
                "rate" : $("#deneyim_select[data-id='" + id + "']").val()
            };
            showLoading('', '');
            $.post("/{{$prefix}}/egitimOylamaYap", data, function (cevap) {
                if(cevap.cvp == 0) {
                    toastr['error'](cevap.msj, cevap.msj);
                } else {
                    toastr['success']("{{config('messages.islem_basarili')}}", "");
                }
            }, "json").done(function () {
                hideLoading();
            });
        }
    </script>
@endsection
