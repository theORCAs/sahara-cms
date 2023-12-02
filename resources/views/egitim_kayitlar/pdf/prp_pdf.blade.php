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
        <h3 class="page-title"> Invoice PDF
            <small>Create Invoice PDF</small>
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
                        <i class="fa fa-file-pdf-o"></i> PDF Form </div>
                    <div class="tools hidden">
                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                        <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                        <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form id="pdfForm" action="" class="form-horizontal" method="post">
                        <input type="hidden" name="egitim_kayit_id" value="{{$egitim_kayit_id}}">
                        @csrf
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Subject</label>
                                <div class="col-md-10">
                                    <input type="text" id="konu" name="konu" class="form-control" value="{{$konu}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Reference</label>
                                <div class="col-md-10">
                                    <input type="text" id="referans" name="referans" class="form-control" value="{{$referans}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Content</label>
                                <div class="col-md-10">
                                    <textarea id="icerik" name="icerik" class="ckeditor">{{$icerik}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Additional Explanation</label>
                                <div class="col-md-10">
                                    <textarea id="alt_bilgi" name="alt_bilgi" class="form-control" rows="3">{{$alt_bilgi}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Add header image to PDF</label>
                                <div class="col-md-3">
                                    <div class="mt-checkbox-list">
                                        <input type="checkbox" id="header_ekle" name="header_ekle" value="1" checked class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Add signature image to PDF</label>
                                <div class="col-md-3">
                                    <div class="mt-checkbox-list">
                                        <input type="checkbox" id="imza_ekle" name="imza_ekle" value="1" checked class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="button" class="btn green" onclick="kaydetKontrol()">Submit</button>
                                    <a href="{{URL::previous()}}" type="button" class="btn default">Cancel</a>
                                    <button type="button" class="btn blue" onclick="pdfOlustur()"><i class="fa fa-file-pdf-o"></i> Create PDF</button>
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
    <!--link href="{{url('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" type="text/css" /-->
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <!-- js dosyalari yuklenir -->
    <!--script src="{{url('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script-->
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd/mm/yyyy'
            });

            CKEDITOR.replace('icerik', {
                height: 500
            });

            //CKEDITOR.config.height = 500;
        });

        function kaydetKontrol() {
            showLoading('', '');
            $("#pdfForm").attr('action', '/pm_wait/prp_pdf/save/{{$egitim_kayit_id}}');
            $("#pdfForm").submit();
        }
        function pdfOlustur() {
            // showLoading('', '');
            $("#pdfForm").attr('action', '/pm_wait/prp_pdf/prp_pdf_create/{{$egitim_kayit_id}}');
            $("#pdfForm").submit();
        }
    </script>
@endsection
