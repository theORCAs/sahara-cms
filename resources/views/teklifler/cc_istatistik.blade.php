@extends('layouts.main')

@section('content')
    <!-- icerik buraya girilir -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar hidden">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="javascript:;"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Confirmed Courses - Statistics
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
        <div class="row">
            <div class="col-md-8">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-search"></i>Filters </div>
                        <div class="tools">
                            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" style="">
                        <form id="filtre_form" class="form-horizontal" method="post" action="/{{$prefix}}">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Year</label>
                                    <div class="col-md-4">
                                        <select id="filtre_yil" name="filtre_yil" class="form-control" onchange="filtreUlkeGetir()">
                                            @foreach($filtre_yil_liste as $fy_row)
                                                <option value="{{$fy_row->yil}}"@if($filtre_yil == $fy_row->yil) selected @endif>{{$fy_row->yil}} ({{$fy_row->sayi}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Country</label>
                                    <div class="col-md-4">
                                        <select id="filtre_ulke_id" name="filtre_ulke_id" class="form-control" onchange="filtreSirketGetir()">
                                            <option value="0">Select</option>
                                            @foreach($filtre_ulke_liste as $fu_row)
                                                <option value="{{$fu_row->id}}"@if($filtre_ulke_id == $fu_row->id) selected @endif>{{$fu_row->adi}} ({{$fu_row->sayi}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Company Name</label>
                                    <div class="col-md-8">
                                        <select id="filtre_sirket_id" name="filtre_sirket_id" class="">
                                            <option value="0">Select</option>
                                            @foreach($filtre_ref_sirket_liste as $sl_row)
                                                <option value="{{$sl_row->id}}" {{$filtre_ref_sirket_id == $sl_row->id ? ' selected' : ''}}>{{$sl_row->adi}} ({{$sl_row->sayi}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-4 col-md-9">
                                        <button type="submit" class="btn green"><i class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Confirmed Courses Statistics </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-responsive">
                            @if(sizeof($liste) == 0)
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th> Country </th>
                                        <th> # of Record</th>
                                        <th> # of Participant </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($top_teklif_sayi = 0)
                                    @php($top_katilimci_sayisi = 0)
                                    @foreach($liste as $key => $row)
                                        @php($top_teklif_sayi += (int) $row->teklif_sayi)
                                        @php($top_katilimci_sayisi += (int) $row->katilimci_sayisi)
                                        <tr>
                                            <td>{{$row->adi}}</td>
                                            <td>{{$row->teklif_sayi}}</td>
                                            <td>{{$row->katilimci_sayisi}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Total: {{$key+1}}</th>
                                        <th>{{$top_teklif_sayi}}</th>
                                        <th>{{$top_katilimci_sayisi}}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyları yuklenir -->

@endsection
@section("js")
    <script type="text/javascript">
        $(document).ready(function () {
            $("#filtre_sirket_id").select2();
        });
        function filtreUlkeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val()
            };

            showLoading('', '');
            $.post("/ccs/filtreUlkeGetirJSON", data, function (cevap) {
                $("#filtre_ulke_id option:first").prop('selected', true);
                $("#filtre_ulke_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_ulke_id").append("<option value='" + row.id + "' " + ("{{$filtre_ulke_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_ulke_id").trigger('change');
            });
        }

        function filtreSirketGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val()
            };

            $.post("/ccs/filtreSirketGetirJSON", data, function (cevap) {
                $("#filtre_sirket_id option:first").prop('selected', true);
                $("#filtre_sirket_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_sirket_id").append("<option value='" + row.id + "' " + ("{{$filtre_ref_sirket_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                $("#filtre_sirket_id").trigger('change');
            });
        }
    </script>

@endsection
