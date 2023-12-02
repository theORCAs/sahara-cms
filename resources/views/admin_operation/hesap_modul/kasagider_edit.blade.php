@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Account Module (Admin)
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
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Date</label>
                                    <div class="col-md-3">
                                        <input type="text" id="tarih" name="tarih" class="form-control date-picker" value="{{old('tarih', ($data->tarih != "" ? date("d.m.Y", strtotime($data->tarih)) : "") )}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Related Person</label>
                                    <div class="col-sm-8">
                                        <select name="ilgili_kisi" id="ilgili_kisi" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($personel_listesi as $row)
                                                <option value="{{$row->id}}" {{old('ilgili_kisi', $data->ilgili_kisi) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Expense Type</label>
                                    <div class="col-sm-6">
                                        <select name="gider_kalem_id" id="gider_kalem_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($gider_turleri as $row)
                                                <option value="{{$row->id}}" {{old('gider_kalem_id', $data->gider_kalem_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label font-green">Given</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="gider_tl" name="gider_tl" class="form-control" value="{{old('gider_tl', $data["gider_tl"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label font-red">Spent</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="gider_tl" name="per_gider_tl" class="form-control" value="{{old('per_gider_tl', $data["per_gider_tl"])}}">
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-sm-2 control-label">Explanation</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="aciklama" id="aciklama">{{old('aciklama', $data->aciklama)}}</textarea>
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
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
        });
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
