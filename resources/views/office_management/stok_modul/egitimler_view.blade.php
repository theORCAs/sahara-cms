@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->

        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Stock Module
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
                                        </th>
                                        <th> Course Title / Date </th>
                                        <th> Day </th>
                                        <th> Name / Email </th>
                                        <th> Company Name</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>ID: {{$row->id}}</div>
                                            </td>
                                            <td>
                                                <div>{{$row->egitimKayit->egitimler->kodu." ".$row->egitimKayit->egitimler->adi}}</div>
                                                <div>&nbsp;</div>
                                                <div>Start Date: <span class="font-red">{{date("d.m.Y", strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi))}}</span></div>
                                            </td>
                                            <td>{{$row->egitimKayit->egitimTarihi->egitim_suresi." ".$row->egitimKayit->egitimTarihi->egitimPart->adi}}</td>
                                            <td>
                                                <div>HR/Admin: {{trim($row->egitimKayit->kontakKisiUnvan['adi']." ".$row->egitimKayit['ct_adi_soyadi'])}}</div>
                                                <div>{{$row->egitimKayit['ct_sirket_email']}}</div>
                                                <div>&nbsp;</div>
                                                <div>PAX: <span class="font-red">{{$row->egitimKayit->katilimcilar->count()}}</span></div>
                                            </td>
                                            <td>
                                                <div>{{$row->egitimKayit['sirket_adi']}}</div>
                                                <div class="font-purple">{{$row->egitimKayit->sirketUlke['adi']}}</div>
                                            </td>
                                            <td>

                                                @if(intval($row->verilenStokSayisi->toplam_cikis) > 0)
                                                    <a href="javascript:;" class="font-green">Inventory Out - Uses</a>
                                                    @else
                                                    <a href="javascript:;" class="font-red">Inventory Out - Not Yet</a>
                                                @endif
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
