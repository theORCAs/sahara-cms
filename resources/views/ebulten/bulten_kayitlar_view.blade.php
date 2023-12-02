@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('em_gl_add')) ? "" : "hidden"}}">
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
        <h3 class="page-title">Email Module
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
        <div class="m-heading-1 border-green m-bordered col-md-4">
            <h3>Email Groups:</h3>
            <p>
                <select id="kategori_id" class="form-control select2" onchange="egitimListesiGetir()">
                    <option value="">Select Group</option>
                    @foreach($grup_listesi as $row)
                        <option value="{{$row->id}}"{{$row->id == $grup_id ? " selected" : ""}}>{{$row->adi." / ".$row->statikEmailSayisi->sayisi}}</option>
                    @endforeach
                </select>
            </p>

        </div>
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
                                        <th> Group Name </th>
                                        <th> Name </th>
                                        <th> Organization</th>
                                        <th> Country </th>
                                        <th> City </th>
                                        <th> Phone </th>
                                        <th> Email </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>ID: {{$row->id}}</div>
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    @if(Auth::user()->isAllow('em_gl_edit'))
                                                        <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->adi_soyadi}}'"><i class="fa fa-pencil"></i></a>
                                                    @endif
                                                    @if(Auth::user()->isAllow('em_gl_del'))
                                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi_soyadi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{$row->grup->adi}}</td>
                                            <td>{{$row->adi_soyadi}}</td>
                                            <td>{{$row->referans_sirket_id != "" ? $row->referansSirket->adi : $row->sirket_adi}}</td>
                                            <td>{{$row->ulke->adi}}</td>
                                            <td>{{$row->sehir}}</td>
                                            <td>
                                                <div>{{$row->telefon}}</div>
                                                <div>{{$row->cep}}</div>
                                            </td>
                                            <td>
                                                <div>{{$row->email}}</div>
                                                <div>{{$row->email2}}</div>
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
            $(".select2").select2();
        });

        function egitimListesiGetir() {
            window.location.href="/{{$prefix}}/search/" + $("#kategori_id").val();
        }
    </script>
@endsection
