@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('jfm_wait_add')) ? "" : "hidden"}}">
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
        <h3 class="page-title">Job Follow-up Module
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
                                        <th> Action </th>
                                        <th>
                                            <div>Requester</div>
                                            <div>Name/Email/Phone</div>
                                        </th>
                                        <th>
                                            <div>Job Category</div>
                                            <div>Job Type</div>
                                            <div>Reporter Name/Email/Phone</div>
                                        </th>
                                        <th>
                                            <div>Request Details</div>
                                            <div>Additional Notes</div>
                                        </th>
                                        <th>Repeat Sequence</th>
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
                                                    @if(Auth::user()->isAllow('jfm_wait_edit'))
                                                        <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->isTuru->adi}}'"><i class="fa fa-pencil"></i></a>
                                                    @endif
                                                    @if(Auth::user()->isAllow('jfm_wait_del'))
                                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->isTuru->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </div>
                                                <div>R.Date: {{date('d.m.Y', strtotime($row->created_at))}}</div>
                                            </td>
                                            <td>
                                                <div>{{$row->adi_soyadi}}</div>
                                                <div>{{$row->email}}</div>
                                                <div>
                                                    @if($row->sirket_adi == "")
                                                        {{$row->referansSirket->adi}}
                                                        @else
                                                        {{$row->sirket_adi}}
                                                    @endif
                                                </div>
                                                <div class="font-purple">{{$row->ulke->adi}}</div>
                                            </td>
                                            <td>
                                                <div>{{$row->isTuru->kategori->adi}}</div>
                                                <div>{{$row->isTuru->adi}}</div>
                                                <div>&nbsp;</div>
                                                <div><b>Reported/Registered by: </b></div>
                                                <div>{{$row->istekYapan->adi_soyadi}}</div>
                                                <div>{{$row->iy_email}}</div>
                                                <div>{{$row->iy_telefon}}</div>
                                            </td>
                                            <td>
                                                <div><b>Requester Name:</b> {{$row->atananKisi->adi_soyadi}}</div>
                                                <div><b>Additional Notes/Explanation:</b></div>
                                                <div>{!! $row->is_tanimi !!}</div>
                                            </td>
                                            <td>
                                                <div>
                                                    <select>
                                                        @foreach($onem_listesi as $onem_row)
                                                            <option value="{{$onem_row->id}}"@if($onem_row->id == $row->oncelik_id) selected @endif>{{$onem_row->adi}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                                <div class="font-green-dark">{{$row->isTuru->tekrarTuru->adi}}</div>
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
