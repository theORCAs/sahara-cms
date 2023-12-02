@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="user_type/create"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> User Authorization
            <small>Define system user authorization</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                @if(Session::has("msj"))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> Success</h4>
                        {{Session::get("msj")}}
                    </div>
                @endif
                <div class="portlet light bordered">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="table-scrollable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>
                                            <select id="filtre_modul" class="form-control" data-tags="true" data-placeholder="Search Modules" data-allow-clear="true">
                                                <option value=""></option>
                                                @foreach($modul_listesi as $row)
                                                    <option value="{{$row->id}}">{{$row->adi}}</option>
                                                    @endforeach
                                            </select>
                                        </th>
                                        <th>Structure #</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($modul_listesi as $key => $row)
                                        @php
                                            $left_margin = 0;
                                            if($row->level1 == 1) {
                                                $left_margin = 15;
                                            } else if($row->level2 == 1) {
                                                $left_margin = 30;
                                            }
                                        @endphp
                                        <tr onclick="return modulYapiGetir('{{$row->id}}')">
                                            <td><div style="margin-left: {{$left_margin}}px">{{$row->adi}}</div></td>
                                            <td class="text-center">{{$row->yapi_sayisi}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-4" id="yapiContainer">
                            Yapı Listesi
                        </div>
                        <div class="col-sm-4" id="yetkiliContainer">
                            Yetkililer listesi
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
@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script type="text/javascript">
        $(document).ready(function () {
            $("#filtre_modul").select2();
            modulYapiGetir('');
        });

        function modulYapiGetir(modul_id) {
            showLoading('', '#yapiContainer');
            var data = {
                "_token" : '{{csrf_token()}}',
                "modul_id" : modul_id
            };
            $.post("/authorization/yapiListesi", data, function (cevap) {
                $("#yetkiliContainer").html('');
                $("#yapiContainer").html(cevap);
            }).done(function() {
                hideLoading('#yapiContainer');
            })
        }

        function yapiYetkiliGetir(yapi_id) {
            showLoading('', '#yetkiliContainer');
            var data = {
                "_token" : '{{csrf_token()}}',
                "yapi_id" : yapi_id
            };
            $.post("/authorization/yetkiliListesi", data, function (cevap) {
                $("#yetkiliContainer").html(cevap);
            }).done(function() {
                hideLoading('#yetkiliContainer');
            })
        }

        function yapiEkleModal(modul_id, yapi_id) {
            showLoading('', '');
            var data = {
                '_token' : '{{csrf_token()}}',
                'modul_id' : modul_id,
                'yapi_id' : yapi_id
            }
            $.post("authorization/yapiEkle", data, function (cevap) {
                //$("#stack1").data('width', '400').html(cevap).modal("show");
                $("#stack1").data('width', '800').html(cevap).modal("show");
            }).done(function () {
                hideLoading('');
            });

        }
    </script>
@endsection
