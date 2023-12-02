@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Instructor Application Module
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
                    <div class="portlet-body" style="display: none;">
                        <form id="filtre_form" class="form-horizontal" method="post" action="/{{$prefix}}/ec_search">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Registration Year</label>
                                    <div class="col-md-2">
                                        <select id="filtre_yil" name="filtre_yil" class="form-control" onchange="filtreUlkeGetir()">
                                            <option value="">All Year</option>
                                            @foreach($filtre_yil as $yil_row)
                                                <option value="{{$yil_row->yil}}" {{$uti_filtre_yil == $yil_row->yil ? ' selected' : ''}}>{{$yil_row->yil}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Country Living</label>
                                    <div class="col-md-4">
                                        <select id="filtre_ulke_id" name="filtre_ulke_id" class="form-control select2 w-100" onchange="filtreDillerGetir()" style="width: 100%;">
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Spoken Language</label>
                                    <div class="col-md-4">
                                        <select id="filtre_dil_id" name="filtre_dil_id" class="form-control select2" onchange="filtreEgitimKategori()" style="width: 100%;">
                                            <option value="0">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Training Category</label>
                                    <div class="col-md-8">
                                        <select id="filtre_kategori_id" name="filtre_kategori_id" class="form-control select2" onchange="filtreEgitimAdi()" style="width: 100%;">
                                            <option value="0">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Course Title</label>
                                    <div class="col-md-8">
                                        <select id="filtre_egitim_id" name="filtre_egitim_id" class="form-control select2" onchange="filtreHocaAdi()" style="width: 100%;">
                                            <option value="0">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Instructor Name</label>
                                    <div class="col-md-8">
                                        <select id="filtre_egitmen_id" name="filtre_egitmen_id" class="form-control select2" style="width: 100%;">
                                            <option value="0">Select</option>

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
                                        <th> # </th>
                                        <th class="text-center"> Registration and Updates </th>
                                        <th> Last Login D/T </th>
                                        <th>
                                            <div>Full Name, Email, Mobile(GSM)</div>
                                            <div>Company/Institution, Country</div>
                                        </th>
                                        <th>
                                            <div>Training Categories Interested</div>
                                            <div>Course Titles Interested to Deliver</div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div><input type="checkbox" class="checkbox"></div>
                                            </td>
                                            <td>
                                                <div>Reg D: {{date('d.m.Y', strtotime($row->created_at))}}</div>
                                                <div>Upd D: {{date('d.m.Y', strtotime($row->updated_at))}}</div>
                                                <div>&nbsp;</div>

                                                @if($row->cv_dosya != '')
                                                    <div><a class="font-green" href="{{Storage::url($row->cv_dosya)}}" target="_blank">CV1 (FreeFormat)</a></div>
                                                    @else
                                                    <div class="font-red">CV1 (FreeFormat) Not Uploaded</div>
                                                @endif
                                                @if($row->cv_dosya2 != '')
                                                    <div><a class="font-green" href="{{Storage::url($row->cv_dosya2)}}" target="_blank">CV2 (TemplateFormat)</a></div>
                                                @else
                                                    <div class="font-red">CV2 (TemplateFormat) Not Uploaded</div>
                                                @endif
                                                <div>&nbsp;</div>
                                                <div>
                                                    <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->adi_soyadi}}'"><i class="fa fa-pencil"></i></a>
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi_soyadi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach($row->girisLoglar as $g_row)
                                                    <div class="small">{{$g_row['ip']}} --> {{date('d.m.Y', strtotime($g_row['created_at']))}}</div>
                                                    @endforeach
                                            </td>
                                            <td>
                                                <div>{{$row->unvani['adi']." ".$row->adi_soyadi}}</div>
                                                <div><span class="font-red">Personel E: </span>{{$row->sahsi_email}}
                                                    @if($row->sahsi_email != '')
                                                        @if($row->ekBilgiler->sahsi_mail_gon_tarih != '')
                                                            <a href="/{{$prefix}}/personelEmailView/{{$row->id}}" class="font-green">[Sent Email]</a>
                                                            <span>{{date('d.m.Y', strtotime($row->ekBilgiler->sahsi_mail_gon_tarih))}}</span>
                                                            @else
                                                            <a href="/{{$prefix}}/personelEmailView/{{$row->id}}" class="font-red">[Email Send]</a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div><span class="font-red">Corporate E: </span>{{$row->sirket_email}}
                                                    @if($row->sirket_email != '')
                                                        @if($row->ekBilgiler->sirket_mail_gon_tarih != '')
                                                            <a href="/{{$prefix}}/corporateEmailView/{{$row->id}}" class="font-green">[Sent Email]</a>
                                                            <span>{{date('d.m.Y', strtotime($row->ekBilgiler->sirket_mail_gon_tarih))}}</span>
                                                        @else
                                                            <a href="/{{$prefix}}/corporateEmailView/{{$row->id}}" class="font-red">[Email Send]</a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div><span class="font-red">M: </span>{{trim($row->cep_tel_kod." ".$row->cep_tel)}}</div>
                                                <div><span class="font-red">T: </span>{{trim($row->tel_kod." ".$row->tel)}}</div>
                                                <div>&nbsp;</div>
                                                @if($row->tc_kimlik != "")
                                                    <div class="font-green">TC No: {{$row->tc_kimlik}}</div>
                                                    @endif
                                                <div>{{$row->sirket_adi}}</div>
                                                <div><span class="font-purple">{{$row->yasadigiUlke['adi']}}</span> / {{$row->yasadigi_sehir}}</div>
                                            </td>
                                            <td>
                                                <div class="font-red">A) Training Categories Interested/Selected</div>
                                                <div>
                                                    @foreach($row->sectigiKategoriler as $kat_row)
                                                        {{$kat_row->adi}}<br>
                                                        @endforeach
                                                </div>
                                                <div class="font-red">B) Course Titles Interested/Selected to Deliver</div>
                                                <div>
                                                    @php($sec_egitim_key = -1)
                                                    @foreach($row->sectigiEgitimler as $sec_egitim_key => $kat_row)
                                                        {{"[$kat_row->kodu] ".$kat_row->adi}}<br>
                                                    @endforeach
                                                    @if($sec_egitim_key < 0)
                                                        <span class="font-red small">No Course Selected for Delivery</span>
                                                        @endif
                                                </div>
                                            </td>
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
            filtreUlkeGetir();
        });

        function filtreUlkeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "prefix" : "{{$prefix}}",
                "filtre_yil" : $("#filtre_yil").val()
            };

            showLoading('', '');
            $.post("/ia_utilized/filtreUlkeGetirJSON", data, function (cevap) {
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

        function filtreDillerGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "prefix" : "{{$prefix}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
            };

            showLoading('', '');
            $.post("/ia_utilized/filtreDilGetirJSON", data, function (cevap) {
                $("#filtre_dil_id option:first").prop('selected', true);
                $("#filtre_dil_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_dil_id").append("<option value='" + row.id + "' " + ("{{$filtre_dil_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_dil_id").trigger('change')
            });
        }

        function filtreEgitimKategori() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "prefix" : "{{$prefix}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_dil_id" : $("#filtre_dil_id").val(),
            };

            showLoading('', '');
            $.post("/ia_utilized/filtreEgitimKategoriGetirJSON", data, function (cevap) {
                $("#filtre_kategori_id option:first").prop('selected', true);
                $("#filtre_kategori_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_kategori_id").append("<option value='" + row.id + "' " + ("{{$filtre_kategori_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_kategori_id").trigger('change')
            });
        }

        function filtreEgitimAdi() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "prefix" : "{{$prefix}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_dil_id" : $("#filtre_dil_id").val(),
                "filtre_kategori_id" : $("#filtre_kategori_id").val(),
            };

            showLoading('', '');
            $.post("/ia_utilized/filtreEgitimGetirJSON", data, function (cevap) {
                $("#filtre_egitim_id option:first").prop('selected', true);
                $("#filtre_egitim_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_egitim_id").append("<option value='" + row.id + "' " + ("{{$filtre_egitim_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_egitim_id").trigger('change')
            });
        }

        function filtreHocaAdi() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "prefix" : "{{$prefix}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_dil_id" : $("#filtre_dil_id").val(),
                "filtre_kategori_id" : $("#filtre_kategori_id").val(),
                "filtre_egitim_id" : $("#filtre_egitim_id").val(),
            };

            showLoading('', '');
            $.post("/ia_utilized/filtreHocaAdiGetirJson", data, function (cevap) {
                $("#filtre_egitmen_id option:first").prop('selected', true);
                $("#filtre_egitmen_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_egitmen_id").append("<option value='" + row.id + "' " + ("{{$filtre_egitmen_id}}" == row.id ? ' selected' : '') + ">" + row.adi + "</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_egitmen_id").trigger('change')
            });
        }
    </script>
@endsection
