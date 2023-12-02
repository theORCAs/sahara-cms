@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Web Users
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
                                <input type="hidden" name="hid_guncelleme" value="1">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}">
                                <input type="hidden" name="hid_guncelleme" value="0">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">User Type</label>
                                    <div class="col-md-6">
                                        <select id="rol_id" name="rol_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($roller as $row)
                                                <option value="{{$row->id}}" {{old('rol_id', $data->rol_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="adi_soyadi" name="adi_soyadi" class="form-control" value="{{old('adi_soyadi', $data["adi_soyadi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="email" name="email" class="form-control" value="{{old('email', $data["email"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-4">
                                        <input type="password" id="sifre" name="sifre" class="form-control" value="{{old('sifre')}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Re Password</label>
                                    <div class="col-sm-4">
                                        <input type="password" id="sifre_tekrar" name="sifre_tekrar" class="form-control" value="{{old('sifre_tekrar')}}">
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-sm-2 control-label">Active?</label>
                                    <div class="col-sm-2">
                                        <select id="flg_durum" name="flg_durum" class="form-control">
                                            <option value="1" {{old('flg_durum', $data->flg_durum) == '1' || old('flg_durum', $data->flg_durum) == '' ? ' selected' : ''}}>Yes</option>
                                            <option value="0" {{old('flg_durum', $data->flg_durum) == '0' ? ' selected' : ''}}>No</option>
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
