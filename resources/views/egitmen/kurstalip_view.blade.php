@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Instructor Assigment Preference
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
                                        <th>
                                            <div>Course Title and Web Link</div>
                                            <div>Start Date / # Days</div>
                                        </th>
                                        <th>
                                            <div>Company / Country</div>
                                            <div># Pax</div>
                                        </th>
                                        <th>
                                            <div class="row">
                                                <div class="col-md-1">&nbsp;</div>
                                                <div class="col-md-5">Instructor</div>
                                                <div class="col-md-2">Days</div>
                                                <div class="col-md-4"> Selected Days in Table Format </div>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td title="teklif_id:{{$row->id}}">{{$liste->firstItem() + $key}}</td>
                                            <td>
                                                <div>{{$row->egitimKayit->egitimler->kodu." ".$row->egitimKayit->egitimler->adi}}</div>
                                                <div>Start D: {{date('d.m.Y', strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi))}} /
                                                    {{$row->egitimKayit->egitimTarihi->egitim_suresi." ".$row->egitimKayit->egitimTarihi->egitimPart->adi}}</div>
                                            </td>
                                            <td>
                                                <div>{{$row->egitimKayit->sirket_adi}}</div>
                                                <div>{{$row->egitimKayit->sirketUlke->adi}}</div>
                                                <div>&nbsp;</div>
                                                <div>PAX: {{$row->egitimKayit->katilimcilar->count()}}</div>
                                            </td>
                                            <td>
                                                @php($secim_yapan_hoca = 0)
                                                @foreach($row->kursaTalipler as $t_row)
                                                    @php($secim_yapan_hoca += 1)
                                                    <div class="row">
                                                        <div class="col-md-1"><input type="checkbox" class="checkbox" id="kurs_talip_id" data-teklifid="{{$row->id}}" value="{{$t_row->id}}"></div>
                                                        <div class="col-md-5">
                                                            <div>{{$t_row->egitmen->adi_soyadi}}</div>
                                                            @if($t_row->onay_mail_tarihi != "")
                                                                <a href="/{{$prefix}}/iasMail/{{$t_row->id}}/{{$row->id}}" class="font-green">[Email Sent]</a>
                                                                    <span title="{{$t_row->onayMailGonderenKisi->adi_soyadi}}">{{date('d.m.Y', strtotime($t_row->onay_mail_tarihi))}}</span>
                                                                @elseif($t_row->iptal_mail_tarihi != '')
                                                                <a href="" class="font-green">[Send Cancel Email]</a>
                                                                    <span title="{{$t_row->iptalMailGonderenKisi->adi_soyadi}}">{{date('d.m.Y', strtotime($t_row->iptal_mail_tarihi))}}</span>
                                                                @else
                                                                <a href="/{{$prefix}}/iasMail/{{$t_row->id}}/{{$row->id}}" class="font-red">[Send Email]</a>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-2">{{str_replace('|', ',', $t_row->secili_gun)}}</div>
                                                        <div class="col-md-4">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    @for($i = 1; $i <= $row->egitimKayit->egitimTarihi->egitim_suresi; $i++)
                                                                        <td>
                                                                            @if(strpos($t_row->secili_gun, "$i") === false)
                                                                                <div class="secili-degil-gun text-center">&nbsp;</div>
                                                                                @else
                                                                                <div class="secili-gun text-center">{{$i}}</div>
                                                                            @endif
                                                                        </td>
                                                                    @endfor
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                <div>
                                                    @if($secim_yapan_hoca > 0)
                                                        <a href="javascript:;" class="font-red" onclick="iptalMailGonderim('{{$row->id}}')">Send Email - CANCEL message</a>
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
    <style type="text/css">
        .secili-gun {
            background-color: #2fa360;
            width: 25px;
            height: 25px;
            margin: 1px;
        }
        .secili-degil-gun {
            background-color: #e7505a;
            width: 25px;
            height: 25px;
            margin: 1px;
        }
    </style>
@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script type="text/javascript">
        function iptalMailGonderim(teklif_id) {
            var secili_say = $("#kurs_talip_id[data-teklifid='" + teklif_id + "']:checked").length;
            if(secili_say == 0) {
                toastr['error']('Please select instructor for send cancel mail send', '');
                return false;
            }

            var kayit_ids = "";
            $("#kurs_talip_id[data-teklifid='" + teklif_id + "']:checked").each(function (i, row) {
                kayit_ids += (kayit_ids != "" ? "," : "") + $(row).val()
            });
            window.location.href = "/{{$prefix}}/cancelEmailStart/" + teklif_id + "/" + kayit_ids;
        }
    </script>
@endsection
