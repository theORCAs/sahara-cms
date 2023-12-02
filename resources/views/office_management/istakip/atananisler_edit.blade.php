@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Jobs Assigned to me
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
                        @if($data->id > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data->id}}">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Work Category</label>
                                    <div class="col-sm-8 form-control-static">
                                        {{$data->kategori_adi}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Work Type</label>
                                    <div class="col-sm-8 form-control-static">
                                        {{$data->is_turu_adi}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Reporter Name</label>
                                    <div class="col-sm-8 form-control-static">
                                        {{$data->istek_yapan_adi." / ".$data->iy_email." / ".$data->iy_telefon}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Report/Request Date</label>
                                    <div class="col-sm-8 form-control-static">
                                        {{date("d.m.Y", strtotime($data->is_tarihi))}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Additional Notes</label>
                                    <div class="col-md-10">
                                        <textarea name="is_tanimi" id="is_tanimi" class="ckeditor">{{old('is_tanimi', $data->is_tanimi)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-2 control-label">Status?</label>
                                    <div class="col-md-2">
                                        <select name="durum" id="durum" class="form-control">
                                            <option value="0" {{old('durum', $data->durum) == "0" ? " selected" : ""}}>Continue</option>
                                            <option value="1" {{old('durum', $data->durum) == "1" ? " selected" : ""}}>Completed</option>
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
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            CKEDITOR.replace('is_tanimi', {
                height : 500
            })
        });

        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
