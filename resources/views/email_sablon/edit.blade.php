@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title hidden"> User Types
            <small>Define system user types</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-6">
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-warning"></i> Error</h4>
                        {{ $error }}
                    </div>
                @endforeach
                @if(Session::has("msj"))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> Success</h4>
                        {{Session::get("msj")}}
                    </div>
                @endif
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-settings font-dark"></i>
                            <span class="caption-subject bold">Form Setups</span>
                        </div>
                        <div class="tools">
                            <div class="btn-group hidden">
                                <a class="btn red btn-outline btn-circle" href="javascript:;" data-toggle="dropdown">
                                    <i class="fa fa-share"></i>
                                    <span class="hidden-xs"> Trigger Tools </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right" id="sample_3_tools">
                                    <li>
                                        <a href="javascript:;" data-action="0" class="tool-action">
                                            <i class="icon-printer"></i> Print</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-action="1" class="tool-action">
                                            <i class="icon-check"></i> Copy</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-action="2" class="tool-action">
                                            <i class="icon-doc"></i> PDF</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-action="3" class="tool-action">
                                            <i class="icon-paper-clip"></i> Excel</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form role="form" id="kayitForm" method="post" action="/form_setup/{{$data['id']}}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="form-group">
                                    <label>Templete Name</label>
                                    <input type="text" id="aciklama" name="aciklama" class="form-control" value="{{$data["aciklama"]}}">
                                </div>
                                <div class="form-group">
                                    <label>Content</label>
                                    <textarea id="alan2" name="alan2" class="ckeditor">{{$data["alan2"]}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Sub Content</label>
                                    <textarea id="alan3" name="alan3" class="form-control" rows="3">{{$data["alan3"]}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Bank Detail</label>
                                    <textarea id="alan7" name="alan7" class="form-control" rows="9">{{$data["alan7"]}}</textarea>
                                </div>
                                <div class="form-group last">
                                    <div class="row">
                                        <label class="control-label col-sm-2">Signature IMG</label>
                                        <div class="control-group col-sm-10">

                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                    @if($data['alan1'] != "")
                                                        <img src="{{\Illuminate\Support\Facades\Storage::url($data['alan1'])}}">
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="btn red btn-outline btn-file">
                                                        <span class="fileinput-new"> Select image </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="alan1">
                                                    </span>
                                                    <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Submit</button>
                                        <a href="{{URL::previous()}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php
                            //print_r($data);
                        ?>
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
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
