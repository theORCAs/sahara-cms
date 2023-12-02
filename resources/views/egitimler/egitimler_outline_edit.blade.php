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
            <div class="col-md-10">
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}/outline_edit_save" enctype="multipart/form-data">
                            <input type="hidden" name="hid_geri_don" id="hid_geri_don" value="0">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category</label>
                                    <div class="col-sm-10">{{$data->egitimKategori->adi ?? ''}}</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Suggested by</label>
                                    <div class="col-sm-4">
                                        <select id="teklif_eden_kisi" name="teklif_eden_kisi" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach($ilgili_kisiler as $row)
                                                <option value="{{$row->id}}" {{old('teklif_eden_kisi', $data->teklif_eden_kisi) == $row->id ? ' selected' : ''}}>{{$row->adi_soyadi." / ".$row->rol_adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">SEO Keyword</label>
                                    <div class="col-sm-8">
                                        <textarea id="keyword" name="keyword" class="form-control" rows="2">{{old('keyword', $data->keyword)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Title for SEO</label>
                                    <div class="col-sm-8">
                                        <textarea id="aciklama" name="aciklama" class="form-control" rows="2">{{old('aciklama', $data->aciklama)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Description </label>
                                    <div class="col-sm-10">
                                        <textarea id="icerik" name="icerik" class="ckeditor">{{old('icerik', $data->icerik)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Objectives</label>
                                    <div class="col-sm-10">
                                        <textarea id="objective" name="objective" class="ckeditor">{{old('objective', $data->objective)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Overview </label>
                                    <div class="col-sm-10">
                                        <textarea id="onsoz" name="onsoz" class="ckeditor">{{old('onsoz', $data->onsoz)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Who Should Attend ?</label>
                                    <div class="col-sm-10">
                                        <textarea id="attend" name="attend" class="ckeditor">{{old('attend', $data->attend)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Show Course Schedule</label>
                                    <div class="col-sm-3">
                                        <select id="flg_kisitli" name="flg_kisitli" class="form-control">
                                            <option value="0" {{old('flg_kisitli', $data->flg_kisitli) == "0" ? ' selected' : ''}}>Yes</option>
                                            <option value="1" {{old('flg_kisitli', $data->flg_kisitli) == "1" ? ' selected' : ''}}>No</option>
                                            <option value="2" {{old('flg_kisitli', $data->flg_kisitli) == "2" ? ' selected' : ''}}>Force to show</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Topics in Text Boxes</label>
                                    <div class="col-sm-2">
                                        <select id="egitim_part_id" name="egitim_part_id" class="form-control">
                                            @foreach($egitim_part_listesi as $row)
                                                <option value="{{$row->id}}" {{old('egitim_part_id', $data->egitim_part_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="text" id="egitim_suresi" name="egitim_suresi" class="form-control" value="{{old('egitim_suresi', $data->egitim_suresi)}}">
                                    </div>
                                    <div class="col-sm-7">
                                        <a href="javascript:;" onclick="kaydetGeriDon()" class="btn btn-primary">Create Parts Text Boxes/Fields</a>
                                    </div>
                                </div>
                                @foreach($egitim_program as $key => $row)
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">{{$key + 1}} part</label>
                                        <div class="col-sm-10">
                                            <input type="hidden" id="p_gun" name="p_gun[]" value="{{$key + 1}}">
                                            <textarea id="p_icerik" name="p_icerik[]" class="ckeditor">{{old('p_icerik.'.$key, $row->icerik)}}</textarea>
                                            <div class="clearfix margin-top-10 font-red">
                                                <input type="checkbox" id="p_flg_gosterme" name="p_flg_gosterme[]" value="{{$key + 1}}" class="form-control"
                                                    {{old('p_flg_gosterme.'.$key) == ($key + 1) || $row->flg_gosterme == "1" ? ' checked' : ''}}> Do not list on the web page Part {{($key + 1)}}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
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
    <link href="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
        })
        function formuKaydet() {
            showLoading('', '');
        }

        function kaydetGeriDon() {
            $("#hid_geri_don").val('1');
            $("#kayitForm").submit();
        }
    </script>
@endsection
