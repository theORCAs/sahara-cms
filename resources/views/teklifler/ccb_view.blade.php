@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Confirmed Courses-Brief
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
            <div class="col-md-6">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-search"></i>Filters </div>
                        <div class="tools">
                            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" style="display: none;">
                        <form id="filtre_form" class="form-horizontal" method="post" action="{{$prefix}}/searchCCB">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Year</label>
                                    <div class="col-md-4">
                                        <select id="filtre_yil" name="filtre_yil" class="form-control" onchange="filtreUlkeGetir()">
                                            @foreach($filtre_yil_liste as $fy_row)
                                                <option value="{{$fy_row->yil}}"@if($filtre_yil == $fy_row->yil) selected @endif>{{$fy_row->yil." (".$fy_row->sayi.")"}}</option>
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
                                                <option value="{{$fu_row->id}}"@if($filtre_ulke_id == $fu_row->id) selected @endif>{{$fu_row->adi." (" . $fu_row->sayi . ")"}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Company Name</label>
                                    <div class="col-md-8">
                                        <select id="filtre_sirket_id" name="filtre_sirket_id" class="" onchange="hocaOdemeGetir()">
                                            <option value="0">Select</option>
                                            @foreach($filtre_ref_sirket_liste as $sl_row)
                                                <option value="{{$sl_row->id}}" {{$filtre_ref_sirket_id == $sl_row->id ? ' selected' : ''}}>{{$sl_row->adi." ($sl_row->sayi)"}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Instructor Payment Status</label>
                                    <div class="col-md-4">
                                        <select id="filtre_hoca_odeme" name="filtre_hoca_odeme" class="form-control" onchange="kursOdemeGetir()">
                                            <option value="0">Select</option>
                                            <option value="1" {{$filtre_hoca_odeme == "1" ? ' selected' : ''}}>dddPaid ({{$filtre_hoca_odeme_liste->paid_sayi}})</option>
                                            <option value="2" {{$filtre_hoca_odeme == "2" ? ' selected' : ''}}>Unpaid ({{$filtre_hoca_odeme_liste->unpaid_sayi}})</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-4 control-label">Course Payment Status</label>
                                    <div class="col-md-4">
                                        <select id="filtre_egitim_odeme" name="filtre_egitim_odeme" class="form-control">
                                            <option value="0">Select</option>
                                            <option value="1" {{$filtre_egitim_odeme == "1" ? ' selected' : ''}}>Paid ({{$filtre_egitim_odeme_liste->paid_sayi}})</option>
                                            <option value="2" {{$filtre_egitim_odeme == "2" ? ' selected' : ''}}>Unpaid ({{$filtre_egitim_odeme_liste->unpaid_sayi}})</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-4 col-md-9">
                                        <button type="submit" class="btn green"><i class="fa fa-search"></i> Search</button>
                                        <button type="button" class="btn default"><i class="fa fa-times"></i> Reset sorting sequence below table to default setting</button>
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
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div>#</div>
                                            <div>ID</div>
                                            <div>W:Week</div>
                                        </th>
                                        <th> Instructor(s) </th>
                                        <th>
                                            <div>COURSE TITLE</div>
                                            <div>Date</div>
                                        </th>
                                        <th> Day </th>
                                        <th> Training Location </th>
                                        <th> Name/Email </th>
                                        <th> Participant Hotel </th>
                                        <th> Company name </th>
                                        <th> Country </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>ID: {{$row->id}}</div>
                                                <div class="font-red">W: {{date('W', strtotime($row->egitimKayit->egitimTarihi['baslama_tarihi']))}}</div>
                                            </td>
                                            <td>
                                                @foreach($row->egitimHocalar as $hoca_row)
                                                    <div>{{trim($hoca_row->hocaBilgi->unvani['adi']." ".$hoca_row->hocaBilgi["adi_soyadi"])}}</div>
                                                    @endforeach
                                            </td>
                                            <td>
                                                <div>{{$row->egitimKayit->egitimler->kodu." ".$row->egitimKayit->egitimler->adi}}</div>
                                                <div>&nbsp;</div>
                                                <div>(Start Date: <span class="font-red">{{date('d.m.Y', strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi))}}</span>)</div>
                                            </td>
                                            <td>{{$row->egitimKayit->egitimTarihi->egitim_suresi." ".$row->egitimKayit->egitimTarihi->egitimPart->adi}}</td>
                                            <td>
                                                @if($row->kursYeri['otel_id'] > 0)
                                                    <a href="javascript:;" class="font-green">{{$row->kursYeri->otelBilgi['adi']}}</a>
                                                @else
                                                    <a href="javascript:;" class="font-red">Training Location</a>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{trim($row->egitimKayit->kontakKisiUnvan['adi']." ".$row->egitimKayit['ct_adi_soyadi'])}}</div>
                                                <div>{{$row->egitimKayit->ct_sirket_email}}</div>
                                                <div>&nbsp;</div>
                                                <div>Pax: <span class="font-red">{{$row->egitimKayit->katilimcilar->count()}}</span></div>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <div>{{$row->egitimKayit['sirket_adi']}}</div>
                                            </td>
                                            <td>
                                                <div class="font-purple">{{$row->egitimKayit->sirketUlke['adi']}}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="col-md-12 text-center @if($liste->lastPage() == 1) hidden @endif">
                                    <div class="pagination-panel">
                                        Page <a href="{{ $liste->previousPageUrl() }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-left"></i></a>
                                        <input type="text" class="pagination-panel-input form-control input-sm input-inline input-mini" maxlenght="5" style="text-align:center; margin: 0 5px;" value="{{$liste->currentPage()}}">
                                        <a href="{{ $liste->nextPageUrl() }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-right"></i></a>
                                        of <span class="pagination-panel-total">{{$liste->lastPage()}}</span>
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
            $.post("/cc_past/filtreUlkeGetirJSON", data, function (cevap) {
                $("#filtre_ulke_id option:first").prop('selected', true);
                $("#filtre_ulke_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_ulke_id").append("<option value='" + row.id + "' " + ("{{$filtre_ulke_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_ulke_id").trigger('change')
            });
        }

        function filtreSirketGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val()
            };

            $.post("/cc_past/filtreSirketGetirJSON", data, function (cevap) {
                $("#filtre_sirket_id option:first").prop('selected', true);
                $("#filtre_sirket_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_sirket_id").append("<option value='" + row.id + "' " + ("{{$filtre_ref_sirket_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                $("#filtre_sirket_id").trigger('change')
            });
        }

        function hocaOdemeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_sirket_id" : $("#filtre_sirket_id").val()
            };

            $.post("/cc_past/filtreHocaOdemeGetirJSON", data, function (row) {
                $("#filtre_hoca_odeme option:first").prop('selected', true);
                $("#filtre_hoca_odeme option:gt(0)").remove();

                $("#filtre_hoca_odeme").append("<option value='1' " + ("{{$filtre_hoca_odeme}}" == 1 ? " selected" : "") + ">Paid (" + row.paid_sayi + ")</option>");
                $("#filtre_hoca_odeme").append("<option value='2' " + ("{{$filtre_hoca_odeme}}" == 2 ? " selected" : "") + ">Unpaid (" + row.unpaid_sayi + ")</option>");

            }, "json").done(function () {
                $("#filtre_hoca_odeme").trigger('change')
            });
        }

        function kursOdemeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_sirket_id" : $("#filtre_sirket_id").val()
            };

            $.post("/cc_past/filtreKursOdemeGetirJSON", data, function (row) {
                $("#filtre_egitim_odeme option:first").prop('selected', true);
                $("#filtre_egitim_odeme option:gt(0)").remove();

                $("#filtre_egitim_odeme").append("<option value='1' " + ("{{$filtre_egitim_odeme}}" == 1 ? " selected" : "") + ">Paid (" + row.paid_sayi + ")</option>");
                $("#filtre_egitim_odeme").append("<option value='2' " + ("{{$filtre_egitim_odeme}}" == 2 ? " selected" : "") + ">Unpaid (" + row.unpaid_sayi + ")</option>");

            }, "json").done(function () {
                $("#filtre_egitim_odeme").trigger('change')
            });
        }
    </script>

@endsection
