@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Instructor Application Module
            <small>Instructor Evaluations</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <form method="post" action="/ia_evaluation">
            @csrf
            <div class="m-heading-1 border-green m-bordered col-md-12">
                <h3>Filters</h3>
                <p class="col-md-3">
                    <select id="filtre_hoca_id" name="filtre_hoca_id" class="form-control select2">
                        <option value="-1">All Instructor</option>
                        @foreach($egitmen_lişte as $row)
                            <option value="{{$row->id}}" {{session('FILTRE_HOCA_ID') == $row->id ? ' selected' : ''}}>{{$row->adi_soyadi." ($row->sayisi)"}}</option>
                            @endforeach
                    </select>
                </p>
                <p class="col-md-3">
                    <select id="filtre_yil" name="filtre_yil" class="form-control select2">
                        <option value="-1">All Year</option>
                        @foreach($yil_liste as $row)
                            <option value="{{$row->yil}}" {{session('FILTRE_YIL') == $row->yil ? ' selected' : ''}}>{{$row->yil." ($row->sayisi)"}}</option>
                            @endforeach
                    </select>
                </p>
                <p class="col-md-3">
                    <button type="submit" class="btn green" onclick="">Search</button>
                </p>

            </div>
        </form>
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
                                        <th> Instructor Name </th>
                                        <th> Course Date </th>
                                        <th> Course Title </th>
                                        <th>
                                            <div class="text-center">Points</div>
                                            <div>
                                                <div class="col-md-4">Pax Name</div>
                                                <div class="col-md-2">Field Knowledge</div>
                                                <div class="col-md-2">Presentation Skills</div>
                                                <div class="col-md-2">Course Material</div>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>{{$liste->firstItem() + $key}}</td>
                                            <td>{{$row->hocaBilgi['adi_soyadi']}}</td>
                                            <td>{{date('d.m.Y', strtotime($row->teklif->egitimKayit->egitimTarihi['baslama_tarihi']))}}</td>
                                            <td>{{$row->teklif->egitimKayit->egitimler['adi']}}</td>
                                            <td>
                                                @foreach($row->egitmenDegerlendirme as $k_row)
                                                    <div class="col-md-4">{{$k_row->katilimci['adi_soyadi']}}</div>
                                                    <div class="col-md-2">{{$k_row->soru1}}</div>
                                                    <div class="col-md-2">{{$k_row->soru2}}</div>
                                                    <div class="col-md-2">{{$k_row->soru3}}</div>
                                                    @endforeach
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
    </script>
@endsection
