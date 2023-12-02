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
                        @if($data["id"] > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category</label>
                                    <div class="col-sm-10">
                                        <select id="kategori_id" name="kategori_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach($kategori_liste as $row)
                                                <option value="{{$row->id}}" {{old('kategori_id', $data->kategori_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Code</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="kodu" name="kodu" class="form-control" value="{{old('kodu', $data["kodu"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Active</label>
                                    <div class="col-sm-2">
                                        <select id="flg_aktif" name="flg_aktif" class="form-control">
                                            <option value="1" {{old('flg_aktif', $data->flg_aktif) == "1" ? ' selected' : ''}}>Yes</option>
                                            <option value="0" {{old('flg_aktif', $data->flg_aktif) == "0" ? ' selected' : ''}}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Order</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="sira" name="sira" class="form-control" value="{{old('sira', $data["sira"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Fee</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="ucret" name="ucret" value="{{old('ucret', $data->ucret)}}" class="form-control">
                                    </div>
                                    <div class="col-sm-2">
                                        <select id="pb_id" name="pb_id" class="form-control">
                                            <option value="1">$</option>
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
        })
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
