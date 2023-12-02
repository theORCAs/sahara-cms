@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Course Assignment Email
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
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
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}/courseAssignMailSend/{{$hid_egitim_hoca_id}}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">From email (Reply to)</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="from_email" name="from_email" class="form-control" value="{{old('from_email', $data["from_email"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">To receive</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="to_email" name="to_email" class="form-control" value="{{old('to_email', $data["to_email"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Subject</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="konu" name="konu" class="form-control" value="{{old('konu', $data["konu"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">CC</label>
                                    <div class="col-sm-6">
                                        <textarea id="cc" name="cc" class="form-control" rows="2">{{old('cc', $data['cc'])}}</textarea>
                                        <p class="help-block">(Use comma between addresses, such as a@a.com,b@b.com)</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">BCC</label>
                                    <div class="col-sm-6">
                                        <textarea id="bcc" name="bcc" class="form-control" rows="2">{{old('bcc', $data['bcc'])}}</textarea>
                                        <p class="help-block">(Use comma between addresses, such as a@a.com,b@b.com)</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Content</label>
                                    <div class="col-sm-6">
                                        <textarea id="icerik" name="icerik" class="ckeditor">{!! old('icerik', $data['icerik']) !!}</textarea>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <div class="row">
                                        <label class="control-label col-sm-2">Attachment 1</label>
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
                                                        <input type="file" name="ekler1"> </span>
                                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <div class="row">
                                        <label class="control-label col-sm-2">Attachment 2</label>
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
                                                        <input type="file" name="ekler2"> </span>
                                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
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
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            CKEDITOR.replace('icerik', {
                height : 500
            })
        });

        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
