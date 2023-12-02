@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('wu_as_add')) ? "" : "hidden"}}">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="/{{$prefix}}/create"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Web Users
            <small>{{$alt_baslik}}</small>
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
        <form method="post" action="/{{$prefix}}/userSearch">
            @csrf
            <div class="m-heading-1 border-green m-bordered col-md-6">
                <h3>Filters</h3>
                <p class="col-md-4">
                    <select id="filtre_rol_id" name="filtre_rol_id" class="form-control select2" onchange="getKullanicilar()">
                        <option value="">Select User Type</option>
                        @foreach($roller_listesi as $rol_id)
                            <option value="{{$rol_id->id}}" {{$filtre_rol_id == $rol_id->id ? ' selected' : ''}}>{{$rol_id->adi}}</option>
                            @endforeach
                    </select>
                </p>
                <p class="col-md-5">
                    <select id="filtre_kullanici_id" name="filtre_kullanici_id" class="form-control select2">
                        <option value="">Select User</option>

                    </select>
                </p>

                <p class="col-md-3">
                    <button type="submit" class="btn green">Search</button>
                    <button type="button" class="btn red" onclick="javascript:window.location.href='/{{$prefix}}'">Reset</button>
                </p>

            </div>
        </form>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box red">
                    <div class="portlet-title">

                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            @if(sizeof($liste) == 0)
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th> # </th>
                                        <th class="text-center"> Action </th>
                                        <th> Type </th>
                                        <th> Name  </th>
                                        <th> GSM </th>
                                        <th> Email </th>
                                        <th> Last Login </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>{{$liste->firstItem() + $key}}</td>
                                            <td class="text-center">
                                                @if(Auth::user()->isAllow('wu_as_edit'))
                                                    <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->adi_soyadi}}'"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(Auth::user()->isAllow('wu_as_del'))
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi_soyadi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                @endif
                                            </td>
                                            <td>{{$row->rol_adi}}</td>
                                            <td>{{$row->adi_soyadi}}</td>
                                            <td>{{$row->cep_tel}}</td>
                                            <td>{{$row->email}}</td>
                                            <td>{{$row->last_login}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="col-md-12 text-center @if($liste->lastPage() == 1) hidden @endif">
                                    <div class="pagination-panel">
                                        <a href="{{ $liste->url(1) }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-double-left"></i></a>
                                        <a href="{{ $liste->previousPageUrl() }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-left"></i></a>
                                        Page <input type="text" class="pagination-panel-input form-control input-sm input-inline input-mini" maxlenght="5" style="text-align:center; margin: 0 5px;" value="{{$liste->currentPage()}}">
                                        of <span class="pagination-panel-total">{{$liste->lastPage()}}</span>
                                        <a href="{{ $liste->nextPageUrl() }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-right"></i></a>
                                        <a href="{{ $liste->url($liste->lastPage()) }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-double-right"></i></a>
                                        (# of records: {{$liste->total()}})
                                    </div>
                                </div>

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

@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script type="text/javascript">
        $(document).ready(function () {
            getKullanicilar();
        });
        
        function getKullanicilar() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "prefix" : "{{$prefix}}",
                "filtre_rol_id" : $("#filtre_rol_id").val()
            };

            showLoading('', '');
            $.post("/{{$prefix}}/kullanicilarGetirJson", data, function (cevap) {
                $("#filtre_kullanici_id option:first").prop('selected', true);
                $("#filtre_kullanici_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_kullanici_id").append("<option value='" + row.id + "' " + ("{{$filtre_kullanici_id}}" == row.id ? ' selected' : '') + ">" + row.adi_soyadi + "</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_kullanici_id").trigger('change')
            });
        }
    </script>
@endsection
