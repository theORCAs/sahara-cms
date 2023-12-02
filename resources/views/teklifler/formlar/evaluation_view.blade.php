@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">{{$ust_baslik}}
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
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        @if($data["id"] > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}" enctype="multipart/form-data">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}/{{$teklif_id}}" enctype="multipart/form-data">
                        @endif
                            @csrf
                                <input type="hidden" name="hid_tur_id" value="{{$tur_id}}">
                                <input type="hidden" name="hid_egitim_kayit_id" value="{{$egitim_kayit_id}}">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Explanation</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="aciklama" name="aciklama" class="form-control" value="{{old('aciklama', $data["aciklama"])}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="control-label col-sm-2">Form File</label>
                                    <div class="col-sm-10">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="dosya"> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Submit</button>
                                        <a href="/{{$prefix}}/{{$teklif_id}}/{{$tur_id}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="portlet box red">
                    <div class="portlet-title">

                        <div class="tools hidden">
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
                                        <th> Explanation </th>
                                        <th> Form File </th>
                                        <th> Update Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>{{$key + 1}}</td>
                                            <td>
                                                <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                                <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->aciklama}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                            </td>
                                            <td>{{$row->aciklama}}</td>
                                            <td><a href="{{Storage::url($row->dosya)}}" target="_blank">Download</a></td>
                                            <td>{{date('d.m.Y H:i', strtotime($row->updated_at))}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
    <link href="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>
@endsection
