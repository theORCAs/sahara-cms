@extends('layouts.main')

@section('content')
    <!-- icerik buraya girilir -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Training Location Assignment
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
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i>Week: {{$hafta}} - List of Meeting Room </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th scope="col"> # </th>
                                    <th scope="col"> Training Location/Course Title </th>
                                    <th scope="col"> Room/Hall Name </th>
                                    <th scope="col"> City </th>
                                    <th scope="col"> Region </th>
                                    <th scope="col"> Person </th>
                                    <th scope="col"> Start </th>
                                    <th scope="col"> Day </th>
                                    <th scope="col"> Fee </th>
                                    <th scope="col"> Status </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($liste as $key => $row)
                                    <tr>
                                        <td>{{$key + 1}}
                                            <div>{{$row->hafta}}</div>
                                        </td>
                                        <td>
                                            <div>{{$row->otel_adi}}</div>
                                            <div class="font-red">{{$row->egitim_adi}}</div>
                                        </td>
                                        <td>{{$row->oda_adi}}</td>
                                        <td>{{$row->sehir_adi}}</td>
                                        <td>{{$row->bolge_adi}}</td>
                                        <td>{{$row->kisi_sayisi}}</td>
                                        <td>W:{{date('W', strtotime($row->baslama_tarihi))}}-{{date('d.m.Y', strtotime($row->baslama_tarihi))}}</td>
                                        <td>{{$row->kac_gun}}</td>
                                        <td>{{$row->ucret > 0 ? $row->ucret : ''}}</td>
                                        <td>
                                            @if($row->teklif_id == $teklif_id)
                                                <div><a href="/{{$prefix}}/unsetAssignTrainingLocation/{{$row->id}}/{{$teklif_id}}">Unset Assignment</a></div>
                                                @else
                                                @if($row->teklif_id != "")
                                                    <div class="font-red">Assigned</div>
                                                    @else
                                                    <div><a href="/{{$prefix}}/setAssignTrainingLocation/{{$row->id}}/{{$teklif_id}}">Set assign</a></div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="10" class="text-center">
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyları yuklenir -->
@endsection
@section("js")
    <!-- js dosyları yuklenir -->
@endsection
