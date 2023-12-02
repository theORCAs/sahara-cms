@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('spm_wait_add')) ? "" : "hidden"}}">
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
        <h3 class="page-title">Suppliers & Payment Module
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
                                        <th>
                                            <div>#</div>
                                            <div>ID</div>
                                        </th>
                                        <th>
                                            <div>Action</div>
                                            <div>Registration Date/By</div>
                                        </th>
                                        <th> Company / Person Name </th>
                                        <th> Bank Name /IBAN </th>
                                        <th> Payment Reason </th>
                                        <th> Amount </th>
                                        <th> INVO Date </th>
                                        <th>Status</th>
                                        <th> Pay Date </th>
                                        <th> Additional Notes</th>
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
                                                <div>
                                                    @if(Auth::user()->isAllow('spm_wait_edit'))
                                                        <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->partner->adi}}'"><i class="fa fa-pencil"></i></a>
                                                    @endif
                                                    @if(Auth::user()->isAllow('spm_wait_del'))
                                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->partner->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </div>
                                                <div>R.Date: {{date('d.m.Y', strtotime($row->created_at))}}</div>
                                                <div>R.By: {{$row->ekleyenKisi->adi_soyadi}}</div>
                                                <div>U.Date: {{date('d.m.Y', strtotime($row->updated_at))}}</div>
                                                <div>U.By: {{$row->guncelleyenKisi->adi_soyadi}}</div>
                                            </td>
                                            <td>{{$row->partner->adi}}</td>
                                            <td>
                                                <div>{{$row->banka}}</div>
                                                <div>{{$row->iban}}</div>
                                            </td>
                                            <td>{{$row->partner->kategori->adi}}</td>
                                            <td class="text-right">@money_format($row->tutar)</td>
                                            <td>
                                                @if($row->dekont_tarihi != "")
                                                    {{date('d.m.Y', strtotime($row->dekont_tarihi))}}
                                                    @endif
                                            </td>
                                            <td>{{$row->odemeBeklemeTurleri->adi}}</td>
                                            <td>
                                                @if($row->odeme_tarihi != "")
                                                    {{date('d.m.Y', strtotime($row->odeme_tarihi))}}
                                                    @endif
                                            </td>
                                            <td>{{$row->aciklama}}</td>
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
