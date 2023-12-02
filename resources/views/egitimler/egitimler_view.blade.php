@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('to_oas_add')) ? "" : "hidden"}}">
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
        <h3 class="page-title">Training Operations
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
                <form method="post" action="/{{$prefix}}/search">
                    @csrf
                    <div class="m-heading-1 border-green m-bordered col-md-6">
                        <h3>Filters</h3>
                        <p class="col-md-9">
                            <select id="kategori_id" name="kategori_id" class="form-control select2">
                                <option value="">Select Category</option>
                                @foreach($kategori_listesi as $row)
                                    <option value="{{$row->id}}"{{$row->id == $kategori_id ? " selected" : ""}}>{{$row->adi}}</option>
                                @endforeach
                            </select>
                        </p>

                        <p class="col-md-3">
                            <button type="submit" class="btn green">Search</button>
                            <button type="button" class="btn red" onclick="javascript:window.location.href='/{{$prefix}}'">Reset</button>
                        </p>

                    </div>
                </form>
            </div>
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
                                        <th> Status </th>
                                        <th> Code </th>
                                        <th> Course Title</th>
                                        <th> REC </th>
                                        <th> # of Parts </th>
                                        <th> Date </th>
                                        <th> Process </th>
                                        <th> Updated by </th>
                                        <th> Suggested by </th>
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
                                                @if($row->flg_aktif == 1)
                                                    <div class="font-green">Active</div>
                                                    @else
                                                    <div class="font-red">Passive</div>
                                                @endif
                                            </td>
                                            <td>{{$row->kodu}}</td>
                                            <td>{{$row->adi}}</td>
                                            <td></td>
                                            <td>{{$row->egitimProgram->count()}}</td>
                                            <td>
                                                @foreach($row->egitimGelecekTarihler as $tarih_row)
                                                    <div>{{date('d.m.Y', strtotime($tarih_row->baslama_tarihi))}}</div>
                                                    @endforeach
                                            </td>
                                            <td>
                                                <div>
                                                    @if(Auth::user()->isAllow('to_oas_edit'))
                                                        <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->adi}}'"><i class="fa fa-pencil"></i></a>
                                                    @endif
                                                    @if(Auth::user()->isAllow('to_oas_del'))
                                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </div>
                                                @if(Auth::user()->isAllow('to_oas_edit'))
                                                    <div><a href="/{{$prefix}}/{{$row->id}}/outline_edit">Outline</a></div>
                                                    <div><a href="/{{$prefix}}/{{$row->id}}/schedule_edit">Schedule</a></div>
                                                @endif
                                                <div>&nbsp;</div>
                                                <div><a href="/{{$prefix}}/outlinePdfCreate/{{$row->id}}" target="_blank">OUTL-Create PDF </a></div>
                                                <div><a href="/{{$prefix}}/outlinePdfCreate/{{$row->id}}/sch" target="_blank">OUTL-Create PDF with Schedule</a></div>
                                            </td>
                                            <td>
                                                <div>{{$row->sonGuncelleyen->adi_soyadi}}</div>
                                                @if($row->updated_at != "")
                                                    <div>{{date('d.m.Y H:i', strtotime($row->updated_at))}}</div>
                                                    @endif
                                            </td>
                                            <td>{{$row->teklifEden->adi_soyadi}}</td>
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
    </script>
@endsection
