@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Homepage Pictures
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-6">
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
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        @if($data["id"] > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}" enctype="multipart/form-data">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}" enctype="multipart/form-data">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="control-label col-sm-2">Image</label>
                                        <div class="control-group col-sm-10">

                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                    @if(old('resim', $data["resim"]) != "")
                                                        <img src="{{Storage::url(old('resim', $data["resim"]))}}">
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="btn red btn-outline btn-file">
                                                        <span class="fileinput-new"> Select image </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="resim" value="">
                                                    </span>
                                                    <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Order</label>
                                    <div class="col-md-2">
                                        <input type="text" id="sira" name="sira" class="form-control" value="{{old('sira', $data['sira'])}}">
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-2 control-label">Active?</label>
                                    <div class="col-md-2">
                                        <input type="checkbox" id="flg_aktif" name="flg_aktif" class="form-control" value="1" {{old('flg_aktif', $data['flg_aktif']) == 1 ? " checked" : ""}}> Yes
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Submit</button>
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <link href="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
