@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('hrm_hl_add')) ? "" : "hidden"}}">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="/{{$prefix}}/create"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Hotels
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
        <form method="post" action="/{{$prefix}}/search">
            @csrf
        <div class="m-heading-1 border-green m-bordered col-md-12">
            <h3>Filters</h3>
            <p class="col-md-3">
                <select id="sehir_id" name="sehir_id" class="form-control select2" onchange="bolgeGetirJson()">
                    <option value="/">Select City</option>
                    @foreach($sehirler as $row)
                        <option value="{{$row->id}}" {{old('sehir_id', $s_sehir_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                        @endforeach
                </select>
            </p>
            <p class="col-md-3">
                <select id="bolge_id" name="bolge_id" class="form-control select2" onchange="dereceGetirJson()">
                    <option value="/">Select Region</option>

                </select>
            </p>
            <p class="col-md-3">
                <select id="derece_id" name="derece_id" class="form-control select2">
                    <option value="/">Select Star</option>

                </select>
            </p>
            <p class="col-md-3">
                <button type="submit" class="btn green" onclick="formuKaydet()">Search</button>
                <a href="javascript:;" class="btn btn-danger">Reset Sequence to Default</a>
            </p>

        </div>
        </form>
        <div class="row">
            <div class="col-md-12">

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
                            <table class="table table-striped table-bordered table-hover table-checkable order-column hidden" id="sample_2">
                                <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <div>#</div>
                                        <div><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></div>
                                    </th>
                                    <th style="width: 150px;">
                                        <div>Action</div>
                                        <div>Reg Date</div>
                                        <div>Upd Date</div>
                                    </th>
                                    <th style="width: 100px;"> City </th>
                                    <th style="width: 100px;"> Region </th>
                                    <th style="width: 40px;"> <div>Star</br>Rate</div> </th>
                                    <th>
                                        <div>Hotel Name</div>
                                        <div>Tel</div>
                                        <div>Email</div>
                                    </th>
                                    <th style="width: 70px;">
                                        <div># Rooms</div>
                                        <div>Show Rates</div>
                                    </th>
                                    <th style="width: 70px;">Meeting Room</th>
                                    <th style="width: 120px;">Contact Person</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($liste as $key => $row)
                                    <tr>
                                        <td>
                                            <div>{{$key + 1}}</div>
                                            <div><input type="checkbox" class="checkboxes" value="{{$row->id}}" /></div>
                                        </td>
                                        <td>
                                            <div>
                                                @if(Auth::user()->isAllow('hrm_hl_edit'))
                                                    <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->adi}}'"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(Auth::user()->isAllow('hrm_hl_del'))
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                @endif
                                            </div>
                                            <div>Reg D: {{date('d.m.Y', strtotime($row->created_at))}}</div>
                                            <div>Upd D: @if($row->updated_at != ''){{date('d.m.Y', strtotime($row->updated_at))}}@endif</div>
                                            @if($row->sonGuncelleyen['adi_soyadi'] != '')
                                                <div class="small">{{$row->sonGuncelleyen['adi_soyadi']}}</div>
                                                @endif
                                        </td>
                                        <td>{{$row->sehir['adi']}}</td>
                                        <td>{{$row->bolge['adi']}}</td>
                                        <td class="text-center">{{$row->derece['adi']}}</td>
                                        <td>
                                            <div>{{$row->adi}}</div>
                                            @if($row->web_adresi != '') <div class="small" style="margin-bottom: 5px;"><a href="{{$row->web_adresi}}" target="_blank" title="{{$row->web_adresi}}">Goto Web Address</a></div> @endif
                                            <div>{{$row->telefon}}</div>
                                            <div>{{$row->email}}</div>
                                            <div class="small">Distance to SAHARA HQ: <span class="font-red">{{$row->ofise_uzaklik}}</span></div>
                                        </td>
                                        <td data-sort="{{intval($row->oda_sayisi)}}" class="text-center">
                                            <div># Rooms: {{$row->oda_sayisi}}</div>
                                            <div>
                                                <input type="checkbox" id="flg_derece_goster" value="1" class="checkboxes" @if($row->flg_derece_goster == 1) checked @endif>
                                            </div>
                                            <div>
                                                @foreach($row->otelOdaTipleri() as $ot_row)
                                                    <div>{{$ot_row->oda_tipi.": ".($ot_row->flg_na == 1 ? 'NA' : $ot_row->ucret_satis)}}</div>
                                                    @endforeach
                                            </div>
                                        </td>
                                        <td class="text-center" data-sort="{{intval($row->toplanti_oda_sayisi)}}">
                                            @if(intval($row->toplanti_oda_sayisi) > 0)
                                                <div>Meeting #: {{$row->toplanti_oda_sayisi}}</div>
                                                @if(floatval($row->yarim_ucret) > 0) <div>Half-Day: {{$row->yarim_ucret}}</div> @endif
                                                @if(floatval($row->tam_ucret) > 0) <div>Full-Day: {{$row->tam_ucret}}</div> @endif
                                                @if(intval($row->min_katilimci) > 0) <div>Min Pax: {{$row->min_katilimci}}</div> @endif
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($row->iletisimListesi as $i_key => $i_row)
                                                <div>{{($i_key + 1).". ".$i_row['adi_soyadi']}}</div>
                                                <div>{{$i_row['email']}}</div>
                                                @if($i_row['telefon'] != '') <div>{{$i_row['telefon']}}</div> @endif
                                                @if($i_row['cep'] != '') <div>{{$i_row['cep']}}</div> @endif
                                            @endforeach
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
        $(document).ready(function () {
            $(".select2").select2();
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
                    'targets': [0, 1]
                }, {
                    "searchable": false,
                    "targets": [0]
                }],
                "order": [
                    [2, "asc"]
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

            bolgeGetirJson();
        });

        function bolgeGetirJson() {
            return false;
            $("#bolge_id option:gt(0)").remove();
            if($("#sehir_id").val() == "") {
                $("#bolge_id option:first").prop('selected', true);
                $("#bolge_id").trigger('change');
                return;
            }
            var data = {
                '_token' : "{{csrf_token()}}",
                'sehir_id' : $("#sehir_id").val()
            };
            showLoading('', '');
            $.post("/{{$prefix}}/bolgeGetirJson", data, function(cevap) {
                $("#bolge_id option:first").prop('selected', true);
                $.each(cevap, function (i, row) {
                    $("#bolge_id").append("<option value='" + row.id + "' " + ( row.id == "{{$s_bolge_id}}" ? " selected" : "" ) + ">" + row.adi + "</option>");
                });
            }, "json").done(function () {
                hideLoading();
                $("#bolge_id").trigger('change');

            });
        }

        function dereceGetirJson() {
            $("#derece_id option:gt(0)").remove();
            if($("#sehir_id").val() == "" && $("#bolge_id").val() == "") {
                $("#derece_id option:first").prop('selected', true);
                $("#derece_id").trigger('change');
                return;
            }
            var data = {
                '_token' : "{{csrf_token()}}",
                'sehir_id' : $("#sehir_id").val(),
                'bolge_id' : $("#bolge_id").val()
            };
            showLoading('', '');
            $.post("/{{$prefix}}/dereceGetirJson", data, function(cevap) {
                $("#derece_id option:first").prop('selected', true);
                $.each(cevap, function (i, row) {
                    $("#derece_id").append("<option value='" + row.id + "' " + (row.id == "{{$s_derece_id}}" ? " selected" : "") + ">" + row.adi + "</option>");
                });
            }, "json").done(function () {
                hideLoading();
                $("#derece_id").trigger('change');

            });
        }
    </script>
@endsection
