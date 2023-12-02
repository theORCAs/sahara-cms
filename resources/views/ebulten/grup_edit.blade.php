@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Email Groups
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-8">
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
                                    <label class="col-md-2 control-label">Order</label>
                                    <div class="col-md-2">
                                        <input type="text" id="sira" name="sira" class="form-control" value="{{old('sira', $data->sira)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Dynamic Group</label>
                                    <div class="col-sm-8">
                                        <select name="dinamik_grup_id" id="dinamik_grup_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($dinamik_grup_liste as $row)
                                                <option value="{{$row->id}}" {{old('dinamik_grup_id', $data->dinamik_grup_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Access Permission 1</label>
                                    <div class="col-sm-8">
                                        <select name="yetkili_1" id="yetkili_1" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($yetkili_liste as $row)
                                                <option value="{{$row->id}}" {{old('yetkili_1', $data->yetkili_1) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Access Permission 2</label>
                                    <div class="col-sm-8">
                                        <select name="yetkili_2" id="yetkili_2" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($yetkili_liste as $row)
                                                <option value="{{$row->id}}" {{old('yetkili_2', $data->yetkili_2) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-sm-2 control-label">Access Permission 3</label>
                                    <div class="col-sm-8">
                                        <select name="yetkili_3" id="yetkili_3" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($yetkili_liste as $row)
                                                <option value="{{$row->id}}" {{old('yetkili_3', $data->yetkili_3) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                                @endforeach
                                        </select>
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

@endsection
@section("js")
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
        });
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
