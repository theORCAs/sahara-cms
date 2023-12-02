@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->

        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Outlines Suggested (by you)
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
                                        <th> Action </th>
                                        <th> Prepared by </th>
                                        <th> Record Date </th>
                                        <th> Course Category </th>
                                        <th> Course Title </th>
                                        <th> Day </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>{{$liste->firstItem() + $key}}</td>
                                            <td>
                                                <div>
                                                    @if(Auth::user()->isAllow('im_os_np_edit'))
                                                        <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->adi}}'"><i class="fa fa-pencil"></i></a>
                                                    @endif
                                                    @if(Auth::user()->isAllow('im_os_np_del'))
                                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </div>
                                                <div style="padding-top: 5px;">
                                                    @if($row->durum == 0)
                                                        <label class="label-warning">Waiting</label>
                                                    @elseif($row->durum == 1)
                                                        <label class="label-success">Accepted/Published</label>
                                                    @elseif($row->durum == 2)
                                                        <label class="label label-danger">Edited/Modified</label>
                                                    @elseif($row->durum == 4)
                                                        <label class="label-default">Passive/Unpublished</label>
                                                    @elseif($row->durum == 3)
                                                        Silindi
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{$row->egitmen->adi_soyadi}}</div>
                                                <div><span class="font-red">Personnel E: </span>{{$row->egitmen->sahsi_email}}</div>
                                                <div><span class="font-red">Corporate E: </span>{{$row->egitmen->sirket_email}}</div>
                                                <div><span class="font-red">M: </span>{{trim($row->egitmen->cep_tel_kod.' '.$row->egitmen->cep_tel)}}</div>
                                                <div><span class="font-red">T: </span>{{trim($row->egitmen->tel_kod.' '.$row->egitmen->tel)}}</div>
                                                <div>&nbsp;</div>
                                                <div class="font-green">TC NO: {{$row->egitmen->tc_kimlik}}</div>
                                                <div>Comment: </div>
                                                <div>{{$row->egitmen->sirket_adi}}</div>
                                                <div><span class="font-purple">{{$row->egitmen->yasadigiUlke->adi}}</span> / {{$row->egitmen->yasadigi_sehir}}</div>
                                            </td>
                                            <td>{{date('d.m.Y H:i', strtotime($row->created_at))}}</td>
                                            <td>{{$row->kategori->adi}}</td>
                                            <td>{{$row->adi}}</td>
                                            <td>{{$row->kac_gun}}</td>
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

@endsection
