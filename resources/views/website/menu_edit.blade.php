@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Website Operations
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
                                    <label class="col-md-2 control-label">Navigation ?</label>
                                    <div class="col-md-10">
                                        <input type="checkbox" name="flg_navigasyon" id="flg_navigasyon" value="1" {{old('flg_navigasyon', $data['flg_navigasyon']) ? " checked" : ""}}
                                            onclick="navigasyonMu()"> Yes
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Parent Menu</label>
                                    <div class="col-md-6">
                                        <select name="ana_menu_id" id="ana_menu_id" class="select2 form-control">
                                            <option value="">Root</option>
                                            @foreach($menuler_listesi as $row)
                                                <option value="{{$row->id}}" {{old('ana_menu_id', $data['ana_menu_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Active ?</label>
                                    <div class="col-md-10">
                                        <input type="checkbox" name="flg_aktif" id="flg_aktif" value="1" {{old('flg_aktif', $data['flg_aktif']) ? " checked" : ""}}> Yes
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Order</label>
                                    <div class="col-md-2">
                                        <input type="text" id="sira" name="sira" class="form-control" value="{{old('sira', $data['sira'])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Shortcut</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="shortcut" name="shortcut" class="form-control" value="{{old('shortcut', $data["shortcut"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Page Content</label>
                                    <div class="col-sm-10">
                                        <textarea name="icerik" id="icerik" class="ckeditor">{!! old('icerik', $data['icerik']) !!}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Link</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="link" name="link" class="form-control" value="{{old('link', $data["link"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Link Target</label>
                                    <div class="col-sm-4">
                                        <select name="link_target" id="link_target" class="form-control">
                                            <option value="_self" {{old('link_target', $data['link_target']) == "_self" ? " selected" : ""}}>Open same page</option>
                                            <option value="_blank" {{old('link_target', $data['link_target']) == "_blank" ? " selected" : ""}}>Open new page</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Setup File</label>
                                    <div class="col-sm-8">
                                        <select name="ws_modul_id" id="ws_modul_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach($moduller_listesi as $row)
                                                <option value="{{$row->id}}" {{old('ws_modul_id', $data['ws_modul_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">In Mainpage ?</label>
                                    <div class="col-md-10">
                                        <input type="checkbox" name="flg_inmain" id="flg_inmain" value="1" {{old('flg_inmain', $data['flg_inmain']) ? " checked" : ""}}> Yes
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">In Menu ?</label>
                                    <div class="col-md-10">
                                        <input type="checkbox" name="flg_inmenu" id="flg_inmenu" value="1" {{old('flg_inmenu', $data['flg_inmenu']) ? " checked" : ""}}> Yes
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-sm-2 control-label">Ony Member?</label>
                                    <div class="col-sm-10">
                                        <input type="checkbox" id="flg_uyeozel" name="flg_uyeozel" class="form-control" value="1" {{old('flg_uyeozel', $data["flg_uyeozel"]) ? " checked" : ""}}> Yes
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
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
        });
        function formuKaydet() {
            showLoading('', '');
        }

        function navigasyonMu() {
            if($("#flg_navigasyon").is(":checked")) {
                $("#ana_menu_id option:first").prop("selected", true);
                $("#ana_menu_id").trigger("change");
                $("#ana_menu_id").prop("disabled", true);
            } else {
                $("#ana_menu_id").prop("disabled", false);
            }
        }
    </script>
@endsection
