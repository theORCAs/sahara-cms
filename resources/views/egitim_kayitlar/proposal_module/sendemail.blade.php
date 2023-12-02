@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar hidden">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="javascript:;"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Proposal Module
            <small>Waiting to be Sent</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row col-md-8">
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
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-pdf-o"></i> Email Form </div>
                    <div class="tools hidden">
                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                        <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                        <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form id="pdfForm" action="/pm_wait/sendEmail" class="form-horizontal" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="egitim_kayit_id" value="{{$egitim_kayit_id}}">
                        @csrf
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Subject</label>
                                <div class="col-md-9">
                                    <input type="text" id="konu" name="konu" class="form-control" value="{{$konu}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">From Email (Reply to)</label>
                                <div class="col-md-9">
                                    <input type="text" id="from_email" name="from_email" class="form-control" value="{{$from_email}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">To (Training Department Contact Person)</label>
                                <div class="col-md-9">
                                    <input type="text" id="to_email" name="to_email" class="form-control" value="{{$to_email}}">
                                    <span class="help-block font-red"> (Please use ONLY one email address. If you want multiple recipients, please use CC field. ) </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">CC (Company Representatives)</label>
                                <div class="col-md-9">
                                    <input type="checkbox" class="checkbox"> ADD Company representatives
                                    <textarea class="form-control" rows="3" id="cc_sirket_yetkili" name="cc_sirket_yetkili"></textarea>
                                    <span class="help-block"> (Use comma between addresses, such as a@a.com,b@b.com) </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">CC (Participants)</label>
                                <div class="col-md-9">
                                    <input type="checkbox" class="checkbox"> Send Email to Participants
                                    <textarea class="form-control" rows="3" id="cc_participant" name="cc_participant">{{$katilimcilar_mails}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">CC (Our Team)</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" rows="3" id="cc_ourteam" name="cc_ourteam">{{$sahara_team_email}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">BCC</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" rows="3" id="bcc" name="bcc">{{$bcc_email}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Email Content</label>
                                <div class="col-md-9">
                                    <textarea id="icerik" name="icerik" class="ckeditor" rows="3">{{$email_icerik}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Attachments</label>
                                <div class="col-md-9">
                                    @if($pdf_invoice != "") <div><input type="checkbox" class="checkbox checked" checked id="pdf_invoice" name="pdf_invoice" value="{{$pdf_invoice}}"> Invoice PDF </div>@endif
                                    @if($pdf_proposal != "") <div><input type="checkbox" class="checkbox checked" checked id="pdf_proposal" name="pdf_proposal" value="{{$pdf_proposal}}"> Proposal PDF </div>@endif
                                    @if($pdf_confirmation != "") <div><input type="checkbox" class="checkbox checked" checked id="pdf_confirmation" name="pdf_confirmation" value="{{$pdf_confirmation}}"> Confirmation Letter PDF </div>@endif
                                    @if($pdf_outline != "") <div><input type="checkbox" class="checkbox checked" checked id="pdf_outline" name="pdf_outline" value="{{$pdf_outline}}"> Course Outline PDF </div>@endif
                                        <div class="row">
                                            <label class="control-label col-md-3">New Attachment 1</label>
                                            <div class="col-md-3">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                            <span class="fileinput-filename"></span>
                                                        </div>
                                                        <span class="input-group-addon btn default btn-file">
                                                                        <span class="fileinput-new"> Select file </span>
                                                                        <span class="fileinput-exists"> Change </span>
                                                                        <input type="hidden" value="" name="..."><input type="file" name="ek_dosya1"> </span>
                                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label col-md-3">New Attachment 2</label>
                                            <div class="col-md-3">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                            <span class="fileinput-filename"></span>
                                                        </div>
                                                        <span class="input-group-addon btn default btn-file">
                                                                        <span class="fileinput-new"> Select file </span>
                                                                        <span class="fileinput-exists"> Change </span>
                                                                        <input type="hidden" value="" name="..."><input type="file" name="ek_dosya2"> </span>
                                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="button" class="btn green" onclick="sendMail()">Send</button>
                                    <a href="{{URL::previous()}}" type="button" class="btn default">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyalari yuklenir -->
    <link href="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <!-- js dosyalari yuklenir -->
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            CKEDITOR.replace('icerik', {
                height : 500
            })
        });

        function sendMail() {
            showLoading('', '');
            $("#pdfForm").submit();
        }

    </script>
@endsection
