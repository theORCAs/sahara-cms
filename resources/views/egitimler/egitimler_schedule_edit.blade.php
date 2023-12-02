@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Training Operations
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
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$egitim_id}}/schedule_edit_save_form1">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <div class="col-sm-6 control-label font-red">Generate Yearly Schedule Automatically</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Schedule start date</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="baslama_tarihi" name="baslama_tarihi"
                                               class="form-control date-picker" value="{{old('baslama_tarihi')}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Number of dates per year</label>
                                    <div class="col-sm-2">
                                        <select id="tekrar_haftasi" name="tekrar_haftasi" class="form-control">
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{$i}}" {{old('tekrar_haftasi') == $i ? ' selected' : ''}}>{{$i}}</option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Number of weeks between each date</label>
                                    <div class="col-sm-3">
                                        @php($atanacak_ara = [4,5,6,7,8,12,16])
                                        <select id="atlanacak_ara" name="atlanacak_ara" class="form-control">
                                            @foreach($atanacak_ara as $val)
                                                <option value="{{$val}}" {{old('atlanacak_ara') == $val ? ' selected' : ''}}>{{$val}} week</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Training location</label>
                                    <div class="col-sm-3">
                                        <select id="fre_egitim_yeri" name="fre_egitim_yeri" class="form-control select2">
                                            @foreach($egitim_yerleri as $row)
                                                <option value="{{$row->id}}" {{old('fre_egitim_yeri') == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Duration</label>
                                    <div class="col-sm-1">
                                        <input type="text" id="egitim_suresi" name="egitim_suresi"
                                               class="form-control" value="{{old('egitim_suresi', $data['egitim_suresi'])}}">
                                    </div>
                                    <div class="col-sm-2">
                                        <select id="egitim_part_id" name="egitim_part_id" class="form-control">
                                            @foreach($egitim_part_listesi as $row)
                                                <option value="{{$row->id}}" {{old('egitim_part_id') == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Create Yearly Schedule</button>
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$egitim_id}}/schedule_edit_save_form2">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <div class="col-sm-6 control-label font-red">Add Individual Training Dates</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Course Start date</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="baslama_tarihi" name="baslama_tarihi"
                                               class="form-control date-picker" value="{{old('baslama_tarihi')}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Training location</label>
                                    <div class="col-sm-3">
                                        <select id="fre_egitim_yeri" name="fre_egitim_yeri" class="form-control select2">
                                            @foreach($egitim_yerleri as $row)
                                                <option value="{{$row->id}}" {{old('fre_egitim_yeri') == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Duration</label>
                                    <div class="col-sm-1">
                                        <input type="text" id="egitim_suresi" name="egitim_suresi" class="form-control" value="{{old('egitim_suresi', $data['egitim_suresi'])}}">
                                    </div>
                                    <div class="col-sm-2">
                                        <select id="egitim_part_id" name="egitim_part_id" class="form-control">
                                            @foreach($egitim_part_listesi as $row)
                                                <option value="{{$row->id}}" {{old('egitim_part_id') == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Add Individual Date</button>
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <p>Yearly Schedule of the Course</p>
                <div class="table-scrollable">
                    @if(sizeof($liste) == 0)
                        <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ config('messages.listelenecek_kayit_yok')}}</div>
                    @else
                        <table class="table table-condensed table-hover">
                            <thead>
                            <tr>
                                <th>
                                    <div>#</div>
                                    <div>ID</div>
                                </th>
                                <th> Start Date </th>
                                <th> Duration</th>
                                <th> Part </th>
                                <th> Training Location </th>
                                <th> Price </th>
                                <th> Action </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($liste as $key => $row)
                                <tr id="satir_{{$row->id}}">
                                    <td>
                                        <div>{{$key + 1}}</div>
                                        <div>ID: {{$row->id}}</div>
                                    </td>
                                    <td>
                                        <input type="text" id="baslama_tarihi_{{$row->id}}" class="form-control date-picker" value="{{date('d.m.Y', strtotime($row->baslama_tarihi))}}">
                                    </td>
                                    <td>
                                        <input type="text" id="egitim_suresi_{{$row->id}}" class="form-control" value="{{$row->egitim_suresi}}">
                                    </td>
                                    <td>
                                        <select id="egitim_part_id_{{$row->id}}" class="form-control">
                                            @foreach($egitim_part_listesi as $p_row)
                                                <option value="{{$p_row->id}}" {{$row->egitim_part_id == $p_row->id ? ' selected' : ''}}>{{$p_row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="egitim_yeri_id_{{$row->id}}" class="form-control select2">
                                            @foreach($egitim_yerleri as $y_row)
                                                <option value="{{$y_row->id}}" {{$row->egitim_yeri_id == $y_row->id ? ' selected' : ''}}>{{$y_row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="col-sm-6">
                                            <input type="text" id="ucret_{{$row->id}}" class="form-control" value="{{$row->ucret}}">
                                        </div>
                                        <div class="col-sm-4">
                                            <select id="ucret_para_birimi_{{$row->id}}" class="form-control">
                                                <option value="1" selected>$</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-xs btn-primary" onclick="return egitimTarihKaydet('{{$row->id}}')"><i class="fa fa-floppy-o"></i></a>
                                        <a href="javascript:;" class="btn btn-xs btn-danger" onclick="egitimTarihSil('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();

            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
        })
        function formuKaydet() {
            showLoading('', '');
        }

        function egitimTarihKaydet(id) {
            var data = {
                '_token' : "{{csrf_token()}}",
                'egitim_yeri_id' : $("#egitim_yeri_id_" + id).val(),
                'baslama_tarihi' : $("#baslama_tarihi_" + id).val(),
                'egitim_suresi' : $("#egitim_suresi_" + id).val(),
                'egitim_part_id' : $('#egitim_part_id_' + id).val(),
                'ucret' : $("#ucret_" + id).val(),
                'ucret_para_birimi': $('#ucret_para_birimi_' + id).val()
            };

            showLoading('', '');
            $.post('/to_outline/egitimTarihSaveJson/' + id, data, function(cevap) {
                if(cevap.cvp == "1") {
                    toastr['success']('{{config('messages.islem_basarili')}}', '');
                } else {
                    toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                }
            }, "json").done(function () {
                hideLoading('');
            });
        }

        function egitimTarihSil(id) {
            bootbox.confirm("Are you sure?", function(result) {
                if(result) {
                    var data = {
                        '_token' : "{{csrf_token()}}",
                    };

                    showLoading('', '');
                    $.post('/to_outline/egitimTarihDelJson/' + id, data, function(cevap) {
                        if(cevap.cvp == "1") {
                            toastr['success']('{{config('messages.islem_basarili')}}', '');
                            $("#satir_" + id).remove();
                        } else {
                            toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                        }
                    }, "json").done(function () {
                        hideLoading('');
                    });
                }
            });
        }

    </script>
@endsection
