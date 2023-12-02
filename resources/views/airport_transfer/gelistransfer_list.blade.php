@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('at_ca_add')) ? "" : "hidden"}}">
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
        <h3 class="page-title">AirPort Trasnfer List
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
                @if(session()->has("err_msj"))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-warning"></i> Error</h4>
                        {{Session::get("err_msj")}}
                    </div>
                @endif
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
                                        <th> Action </th>
                                        <th>
                                            <div>Course Title</div>
                                            <div>Start Date</div>
                                            <div>Company Name</div>
                                            <div>Country</div>
                                        </th>
                                        <th>Guests Name/ Email/ Mobile (GSM)</th>
                                        <th>
                                            <div>Arrival Flight</div>
                                            <div>Flight No</div>
                                            <div>Date</div>
                                            <div>Time</div>
                                        </th>
                                        <th>
                                            <div>Departure Flight</div>
                                            <div>Flight No</div>
                                            <div>Date</div>
                                            <div>Time</div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>ID: {{$row->teklif_id}}</div>
                                                <div class="font-red">W: {{date('W', strtotime($row->gelis_tarih))}}</div>
                                            </td>
                                            <td>
                                                <div>
                                                    @if(Auth::user()->isAllow('at_ca_edit'))
                                                        <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->teklif->egitimKayit->egitimler['kodu']." ".$row->teklif->egitimKayit->egitimler['adi']}}'"><i class="fa fa-pencil"></i></a>
                                                    @endif
                                                    @if(Auth::user()->isAllow('at_ca_del'))
                                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->teklif->egitimKayit->egitimler['kodu']." ".$row->teklif->egitimKayit->egitimler['adi']}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </div>
                                                <div>Registration D: {{date('d.m.Y', strtotime($row->created_at))}}</div>
                                                <div><b>Guest Hotel Name:</b></div>
                                                <div>
                                                    @if($row->otel_adi != '')
                                                        <a href="{{$row->otel_website != '' ? $row->otel_website : 'javascript:;'}}" target="_blank">{{$row->otel_adi}}</a>
                                                    @else
                                                        <a href="{{$row->otel['web_adresi'] != '' ? $row->otel['web_adresi'] : 'javascript:;'}}" target="_blank">{{$row->otel['adi']}}</a>
                                                    @endif
                                                </div>
                                                <div>&nbsp;</div>
                                                @if($row->gelisOnaylanBilgi->id != "")
                                                    <div class="font-red">Confirmed by: {{$row->gelisOnaylanBilgi->adi_soyadi}}</div>
                                                    @endif
                                            </td>
                                            <td>
                                                <div>{{$row->teklif->egitimKayit->egitimler['kodu']." ".$row->teklif->egitimKayit->egitimler['adi']}}</div>
                                                <div>&nbsp;</div>
                                                <div><b>Start D:</b> {{date('d.m.Y', strtotime($row->teklif->egitimKayit->egitimTarihi['baslama_tarihi']))}}</div>
                                                <div>&nbsp;</div>
                                                <div><b>Company Name/Country:</b></div>
                                                <div>{{$row->teklif->egitimKayit['sirket_adi']}}</div>
                                                <div class="font-purple">{{$row->teklif->egitimKayit->sirketUlke->adi}}</div>
                                            </td>
                                            <td>
                                                <div>Number of Guest: <span class="font-red">{{$row->kisiler->count()}}</span></div>
                                                <div>&nbsp;</div>
                                                @foreach($row->kisiler as $kisi_row)
                                                    <div style="border-bottom: 2px solid #1b1e21;">
                                                        <div>{{$kisi_row->adi}}</div>
                                                        @if($kisi_row->email != "")<div>E: {{$kisi_row->email}}</div>@endif
                                                        @if($kisi_row->gsm != "")<div>M: {{$kisi_row->gsm_kodu." ".$kisi_row->gsm}}</div>@endif
                                                    </div>
                                                    @endforeach
                                                <div>&nbsp;</div>
                                                <div><a href="/{{$prefix}}/airportsign/{{$row->id}}" target="_blank">Airport Sign</a></div>
                                            </td>
                                            <td>
                                                <div><b>Airlines:</b> {{$row->gelisHavayolu['adi']}}</div>
                                                <div><b>Flight No:</b> {{$row->gelis_ucus_no}}</div>
                                                <div><b>Arrival D:</b> <span class="font-red">{{date('d.m.Y', strtotime($row->gelis_tarih))}}</span></div>
                                                <div><b>Arrival T:</b> {{date('H:s', strtotime($row->gelis_saat))}}</div>
                                                <div><b>Arrival Airport:</b> {{$row->gelisHavaalani['adi']}}</div>
                                                <div><b>Arrival Transfer Company:</b> {{$row->gelisTransferFirma->adi}}</div>
                                                <div>&nbsp;</div>
                                                @if($row->gelis_tasima_onay_id != "")
                                                    <div><a href="/{{$prefix}}/gtfOnaylama/{{$row->id}}" class="font-green" onclick="showLoading('', '')">Registered with Transfer Company</a></div>
                                                    <div>{{$row->gelisTransferFirmaOnayBilgi->adi_soyadi." / ".date("d.m.Y", strtotime($row->gelis_tasima_onay_tarih))}}</div>
                                                @else
                                                    <div><a href="/{{$prefix}}/gtfOnayla/{{$row->id}}" class="font-red" onclick="showLoading('', '')">NOT Registered with Transfer Company</a></div>
                                                @endif
                                            </td>
                                            <td>
                                                <div><b>Airlines:</b> {{$row->gidisHavayolu->adi}}</div>
                                                <div><b>Flight No:</b> {{$row->gidis_ucus_no}}</div>
                                                <div><b>Departure D:</b> @if($row->gidis_tarih != "")<span class="font-red">{{date('d.m.Y', strtotime($row->gidis_tarih))}}</span>@endif</div>
                                                <div><b>Departure T:</b> @if($row->gidis_saat != ""){{date('H:s', strtotime($row->gidis_saat))}}@endif</div>
                                                <div><b>Departure Airport:</b> {{$row->gidisHavaalani->adi}}</div>
                                                <div><b>Departure Transfer Company:</b> {{$row->gidisTransferFirma->adi}}</div>
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
    <!-- javascript yüklenir -->

@endsection
