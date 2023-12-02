@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="user_type/create"><i class="fa fa-plus"></i> Add new</a>
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
        <div class="row">
            <div class="col-md-12">
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
                            <span class="caption-subject bold uppercase">{{$etiket}}</span>
                        </div>
                        <div class="tools">
                            <div class="btn-group">
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
                        <div class="alert alert-info" id="donenLoading"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                        <table class="table table-striped table-bordered table-hover table-checkable order-column hidden" id="sample_2">
                            <thead>
                            <tr>
                                <th class="table-checkbox">
                                    <input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" />
                                </th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>GSM</th>
                                <th>Email</th>
                                <th>Last Login</th>
                                <th class="notexport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($kullanicilar as $key => $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkboxes" value="{{$row->id}}" />
                                    </td>
                                    <td>{{$row->rolu}}</td>
                                    <td>{{$row->adi_soyadi}}</td>
                                    <td class="text-center">{{$row->cep_tel}}</td>
                                    <td>{{$row->email}}</td>
                                    <td class="text-center"><label class="@if($row->last_login == "Never Logged in") label label-danger @endif">{{$row->last_login}}</label></td>
                                    <td class="text-center">
                                        <a href="/user_type/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->adi_soyadi}}'"><i class="fa fa-pencil"></i></a>
                                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->adi_soyadi}}'" onclick="silmeKontrol({{$row->id}})"><i class="fa fa-trash"></i></a>
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
        $(document).ready(function () {
            var table = $('#sample_2');

            var oTable = table.dataTable({


                buttons: [
                    { extend: 'print', className: 'btn dark btn-outline', exportOptions: { columns: ':not(.notexport)'} },
                    { extend: 'copy', className: 'btn red btn-outline', exportOptions: { columns: ':not(.notexport)'} },
                    { extend: 'pdf', className: 'btn green btn-outline', exportOptions: { columns: ':not(.notexport)'} },
                    { extend: 'excel', className: 'btn yellow btn-outline ', exportOptions: { columns: ':not(.notexport)'} },
                    // { extend: 'csv', className: 'btn purple btn-outline ', exportOptions: { columns: ':not(.notexport)'} },
                    // { extend: 'colvis', className: 'btn dark btn-outline', text: 'Columns'}
                ],


                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "pagingType": "bootstrap_extended",

                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                "columnDefs": [{  // set default column settings
                    'orderable': false,
                    'targets': [0]
                }, {
                    "searchable": false,
                    "targets": [0]
                }],
                "order": [
                    [1, "asc"]
                ] // set first column as a default sort by asc
            });

            var tableWrapper = jQuery('#sample_2_wrapper');

            table.find('.group-checkable').change(function () {
                var set = jQuery(this).attr("data-set");
                var checked = jQuery(this).is(":checked");
                jQuery(set).each(function () {
                    if (checked) {
                        $(this).prop("checked", true);
                    } else {
                        $(this).prop("checked", false);
                    }
                });
                jQuery.uniform.update(set);
            });

            table.removeClass("hidden");
            $("#donenLoading").remove();

            $('#sample_3_tools > li > a.tool-action').on('click', function() {
                var action = $(this).attr('data-action');
                oTable.DataTable().button(action).trigger();
            });
        });
    </script>
@endsection
