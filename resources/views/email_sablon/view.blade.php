@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
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
        <h3 class="page-title hidden"> User Types
            <small>Define system user types</small>
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
                    <div class="portlet-body">
                        <div class="row">
                            <h4 class="text-danger">Document Templates</h4>
                            <div class="col-sm-6">
                        <table class="table table-striped table-bordered table-hover table-checkable order-column compact table-scrollable" id="sample_1">
                            <thead>
                            <tr class="bg-default">
                                <th>Order</th>
                                <th>Template Name</th>
                                <th class="notexport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($templete as $row)
                                <tr>
                                    <td>{{$row->sira}}</td>
                                    <td>{{$row->aciklama}}</td>
                                    <td>
                                        @if(Auth::user()->isAllow('fs_upd'))
                                            <a href="/form_setup/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                        @endif
                                        @if(Auth::user()->isAllow('fs_del'))
                                            <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->aciklama}}'" onclick="silmeKontrol({{$row->id}})"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        </div>
                        <h4 class="text-danger">Email Setup Participants/Customers</h4>
                        <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-hover table-checkable order-column compact" id="sample_2">
                            <thead>
                            <tr class="bg-default">
                                <th>Order</th>
                                <th>Template Name</th>
                                <th class="hidden-xs">Email Subject</th>
                                <th class="hidden-xs">CC Emails</th>
                                <th class="hidden-xs">BCC Emails</th>
                                <th class="notexport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($participant as $row)
                                <tr>
                                    <td>{{$row->sira}}</td>
                                    <td>{{$row->aciklama}}</td>
                                    <td class="hidden-xs">{{$row->alan1}}</td>
                                    <td class="hidden-xs">{{$row->alan6}}</td>
                                    <td class="hidden-xs">{{$row->alan7}}</td>
                                    <td>
                                        @if(Auth::user()->isAllow('fs_upd'))
                                            <a href="/form_setup/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                        @endif
                                        @if(Auth::user()->isAllow('fs_del'))
                                            <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->aciklama}}'" onclick="silmeKontrol({{$row->id}})"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <h4 class="text-danger">Email Setup Instructors</h4>
                        <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-hover table-checkable order-column compact" id="sample_2">
                            <thead>
                            <tr class="bg-default">
                                <th>Order</th>
                                <th>Template Name</th>
                                <th class="hidden-xs">Email Subject</th>
                                <th class="hidden-xs">CC Emails</th>
                                <th class="hidden-xs">BCC Emails</th>
                                <th class="notexport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($instructor as $row)
                                <tr>
                                    <td>{{$row->sira}}</td>
                                    <td>{{$row->aciklama}}</td>
                                    <td class="hidden-xs">{{$row->alan1}}</td>
                                    <td class="hidden-xs">{{$row->alan6}}</td>
                                    <td class="hidden-xs">{{$row->alan7}}</td>
                                    <td>
                                        @if(Auth::user()->isAllow('fs_upd'))
                                            <a href="/form_setup/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                        @endif
                                        @if(Auth::user()->isAllow('fs_del'))
                                            <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->aciklama}}'" onclick="silmeKontrol({{$row->id}})"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </div>
                        <h4 class="text-danger">Email Setup Guests</h4>
                        <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-hover table-checkable order-column compact" id="sample_2">
                            <thead>
                            <tr class="bg-default">
                                <th>Order</th>
                                <th>Template Name</th>
                                <th class="hidden-xs">Email Subject</th>
                                <th class="hidden-xs">CC Emails</th>
                                <th class="hidden-xs">BCC Emails</th>
                                <th class="notexport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($guest as $row)
                                <tr>
                                    <td>{{$row->sira}}</td>
                                    <td>{{$row->aciklama}}</td>
                                    <td class="hidden-xs">{{$row->alan1}}</td>
                                    <td class="hidden-xs">{{$row->alan6}}</td>
                                    <td class="hidden-xs">{{$row->alan7}}</td>
                                    <td>
                                        @if(Auth::user()->isAllow('fs_upd'))
                                            <a href="/form_setup/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                        @endif
                                        @if(Auth::user()->isAllow('fs_del'))
                                            <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->aciklama}}'" onclick="silmeKontrol({{$row->id}})"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </div>
                        <h4 class="text-danger">Email Setup for Hotels</h4>
                        <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-hover table-checkable order-column compact" id="sample_2">
                            <thead>
                            <tr class="bg-default">
                                <th>Order</th>
                                <th>Template Name</th>
                                <th class="hidden-xs">Email Subject</th>
                                <th class="hidden-xs">CC Emails</th>
                                <th class="hidden-xs">BCC Emails</th>
                                <th class="notexport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($hotels as $row)
                                <tr>
                                    <td>{{$row->sira}}</td>
                                    <td>{{$row->aciklama}}</td>
                                    <td class="hidden-xs">{{$row->alan1}}</td>
                                    <td class="hidden-xs">{{$row->alan6}}</td>
                                    <td class="hidden-xs">{{$row->alan7}}</td>
                                    <td>
                                        @if(Auth::user()->isAllow('fs_del'))
                                            <a href="/form_setup/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                        @endif
                                        @if(Auth::user()->isAllow('fs_del'))
                                            <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->aciklama}}'" onclick="
                                                    ({{$row->id}})"><i class="fa fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
    <link href="{{url('assets/global/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script src="{{url('assets/global/scripts/datatable.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        function silmeKontrol(id) {
            alert("aaa");
        }
    </script>
@endsection
