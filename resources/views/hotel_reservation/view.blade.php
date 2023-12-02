@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('hrm_rr_add')) ? "" : "hidden"}}">
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
        <h3 class="page-title">Hotel Reservation
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
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                            <table class="table table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th> # </th>
                                    <th class="text-center"> Action </th>
                                    <th>
                                        <div>Course Title</div>
                                        <div>Start Date</div>
                                        <div>Company Name</div>
                                        <div>Country</div>
                                    </th>
                                    <th> Details of Person<br>Making Reservation </th>
                                    <th> Guest Details </th>
                                    <th> Reservation Details </th>
                                    <th> İlgili Kişi </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($liste as $key => $row)
                                    <tr>
                                        <td>{{$liste->firstItem() + $key}}</td>
                                        <td class="text-center">
                                            <div>
                                                @if(Auth::user()->isAllow('hrm_rr_edit'))
                                                    <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->teklif->egitimKayit->egitimler->adi}}'"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(Auth::user()->isAllow('hrm_rr_del'))
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->teklif->egitimKayit->egitimler->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                @endif
                                            </div>
                                            <div>Registration D/T: {{date('d.m.Y', strtotime($row->created_at))}}</div>
                                            <div style="margin-top: 5px;">
                                                @if($row->teyit_mail_tarih == "")
                                                    <a href="\{{$prefix}}\teyitMaili\{{$row->id}}" class="font-red">Teyit Mail</a>
                                                @else
                                                    <a href="\{{$prefix}}\teyitMaili\{{$row->id}}" class="font-green">Teyit Mail</a> {{date('d.m.Y', strtotime($row->teyit_mail_tarih))}}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{$row->teklif->egitimKayit->egitimler['kodu']." ".$row->teklif->egitimKayit->egitimler['adi']}}</div>
                                            <div style="margin-top: 10px;">Start D: <span class="font-red">{{date('d.m.Y', strtotime($row->teklif->egitimKayit->egitimTarihi['baslama_tarihi']))}}</span></div>
                                            <div style="margin-top: 10px;"><b>Company Name/Country:</b></div>
                                            <div>{{$row->teklif->egitimKayit['sirket_adi']}}</div>
                                            <div class="font-purple">{{$row->teklif->egitimKayit->sirketUlke['adi']}}</div>
                                        </td>
                                        <td>
                                            <div>{{$row['adi_soyadi']}}</div>
                                            <div>{{$row->email}}</div>
                                            <div>{{$row->cep}}</div>
                                        </td>
                                        <td>
                                            @foreach($row->kisiler as $kisi_row)
                                                <div>{{$kisi_row->adi.", ".$kisi_row->cinsiyet.", ".$kisi_row->yas}}</div>
                                                @endforeach
                                        </td>
                                        <td>
                                            <div><b>Check-in D: </b><span class="font-red">@if($row->tarih_giris != '') {{date('d.m.Y', strtotime($row->tarih_giris))}} @endif</span></div>
                                            <div><b>Check-out D: </b><span class="font-red">@if($row->tarih_cikis != '') {{date('d.m.Y', strtotime($row->tarih_cikis))}} @endif</span></div>
                                            <div>&nbsp</div>
                                            <div><b>View Option: </b>{{$row->manzara->adi}}</div>
                                            <div>&nbsp</div>
                                            @foreach($row->otelRezervasyonOda() as $r_key => $r_row)
                                                @if($r_key > 0) <div>&nbsp;</div> @endif
                                                <div>{{$r_row->otel_adi}}</div>
                                                <div>Room #: {{$r_row->oda_sayisi}}</div>
                                                <div>Room Type: {{$r_row->oda_tipi_adi}}</div>
                                                <div>Rate per night: {{$r_row->gecelik_ucret}}</div>
                                                <div>
                                                    @if($r_row->rm_tarih == "")
                                                        <a href="/{{$prefix}}/emailToHotel/{{$r_row->id}}" class="font-red">Email to Hotel (Reservation Request)</a>
                                                    @else
                                                        <a href="/{{$prefix}}/emailToHotel/{{$r_row->id}}" class="font-green">Email to Hotel (Reservation Request)</a> {{date('d.m.Y', strtotime($r_row->rm_tarih))}}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div>{{$row->ilgiliPersonel->adi_soyadi}}</div>
                                            <div>{{$row->islem_mesaji}}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="col-md-12 text-center">
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
