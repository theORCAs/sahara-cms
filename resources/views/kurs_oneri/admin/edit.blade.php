@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Outlines Suggested (by you)
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
                                    <label class="col-sm-3 control-label">Suggested Course Category</label>
                                    <div class="col-sm-9">
                                        <select id="kategori_id" name="kategori_id" class="select2">
                                            <option value="">Select</option>
                                            @foreach($kategori_liste as $row)
                                                <option value="{{$row->id}}"{{old('kategori_id', $data['kategori_id']) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Suggested Course Title</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Expertise in the field</label>
                                    <div class="col-sm-9">
                                        <textarea id="deneyim_aciklama" name="deneyim_aciklama" class="form-control">{{old('deneyim_aciklama', $data['deneyim_aciklama'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Action</label>
                                    <div class="col-sm-3">
                                        <select id="durum" name="durum" class="form-control" onchange="apGetir()">
                                            <option value="0" {{old('durum', $data->durum) == '0' ? " selected" : ''}}>New Proposal</option>
                                            <option value="2" {{old('durum', $data->durum) == '2' ? " selected" : ''}}>Edited/Modified</option>
                                            <option value="1" {{old('durum', $data->durum) == '1' ? " selected" : ''}}>Accepted/Published</option>
                                            <option value="4" {{old('durum', $data->durum) == '4' ? " selected" : ''}}>Passive/Unpublished</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ap-class {{$row->durum == '1' ? '' : ' hidden'}}">
                                    <label class="col-sm-3 control-label">Category</label>
                                    <div class="col-sm-3">
                                    </div>
                                </div>
                                <div class="form-group ap-class {{$row->durum == '1' ? '' : ' hidden'}}">
                                    <label class="col-sm-3 control-label">Code</label>
                                    <div class="col-sm-3">
                                    </div>
                                </div>
                                <div class="form-group ap-class {{$row->durum == '1' ? '' : ' hidden'}}">
                                    <label class="col-sm-3 control-label">Order</label>
                                    <div class="col-sm-3">
                                    </div>
                                </div>
                                <div class="form-group ap-class {{$row->durum == '1' ? '' : ' hidden'}}">
                                    <label class="col-sm-3 control-label">Fee</label>
                                    <div class="col-sm-3">
                                        <select id="ucret_secenek" name="ucret_secenek" class="form-control" onchange="ucretAlanGoster()">
                                            <option value="1" {{old('ucret_secenek', $data->ucret_secenek) == '1' || old('ucret_secenek', $data->ucret_secenek) == '' ? ' selected' : ''}}>Assign Category Fee</option>
                                            <option value="2" {{old('flg_kisitli', $data->flg_kisitli) == '2' ? ' selected' : ''}}>Set a New/Different Fee</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 ucret-alan {{old('ucret_secenek', $data->ucret_secenek) == 2 ? '' : ' hidden'}}">
                                        <input type="text" id="ucret" name="ucret" class="form-control" value="{{old('ucret', $data->ucret)}}">
                                    </div>
                                </div>
                                <div class="form-group ap-class {{$row->durum == '1' ? '' : ' hidden'}}">
                                    <label class="col-sm-3 control-label">Show Course Schedule?</label>
                                    <div class="col-sm-4">
                                        <select id="flg_kisitli" name="flg_kisitli" class="form-control">
                                            <option value="0" {{old('flg_kisitli', $data->flg_kisitli) == '0' ? ' selected' : ''}}>Yes</option>
                                            <option value="1" {{old('flg_kisitli', $data->flg_kisitli) == '1' ? ' selected' : ''}}>No</option>
                                            <option value="2" {{old('flg_kisitli', $data->flg_kisitli) == '2' ? ' selected' : ''}}>Force to show</option>
                                        </select>
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
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Course Description</label>
                                    <div class="col-sm-9">
                                        <textarea id="icerik" name="icerik" class="ckeditor">{{old('icerik', $data['icerik'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Course Objective</label>
                                    <div class="col-sm-9">
                                        <textarea id="objective" name="objective" class="ckeditor">{{old('objective', $data['objective'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Course Overview</label>
                                    <div class="col-sm-9">
                                        <textarea id="aciklama" name="aciklama" class="ckeditor">{{old('aciklama', $data['aciklama'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Who Should Attend</label>
                                    <div class="col-sm-9">
                                        <textarea id="attend" name="attend" class="ckeditor">{{old('attend', $data['attend'])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Topics in Text Boxes</label>
                                    <div class="col-sm-2">
                                        <select id="kac_gun" name="kac_gun" class="form-control" onchange="gunleriGoster()">
                                            <option value="5"{{old('kac_gun', $data['kac_gun']) == 5 ? " selected" : ""}}>5 Days</option>
                                            <option value="10"{{old('kac_gun', $data['kac_gun']) == 10 ? " selected" : ""}}>10 Days</option>
                                        </select>
                                    </div>
                                </div>
                                @for($gun = 1; $gun <= 10; $gun++)
                                    <div class="form-group kurs-icerik-container hidden" data-gun="{{$gun}}">
                                        <label class="col-sm-3 control-label">{{$gun}}. day</label>
                                        <div class="col-sm-9">
                                            <textarea id="kurs_icerik" name="kurs_icerik[]" class="ckeditor">{{old('kurs_icerik.'.($gun-1), (isset($icerik_data[$gun-1]) ? $icerik_data[($gun-1)]->icerik : "" ) )}}</textarea>
                                        </div>
                                    </div>
                                @endfor
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
            gunleriGoster();
            apGetir();
            ucretAlanGoster();
        });

        function gunleriGoster() {
            $(".kurs-icerik-container").addClass('hidden')
            for(var i = 1; i <= $('#kac_gun').val(); i++) {
                $(".kurs-icerik-container[data-gun='" + i + "']").removeClass('hidden');
            }
        }
        function formuKaydet() {
            showLoading('', '');
        }

        function apGetir() {
            if($("#durum").val() == 1) {
                $(".ap-class").removeClass('hidden');
            } else {
                $(".ap-class").addClass('hidden');
            }
        }
        function ucretAlanGoster() {
            if($("#ucret_secenek").val() == "2") {
                $(".ucret-alan").removeClass('hidden');
            } else {
                $(".ucret-alan").addClass('hidden');
            }
        }
    </script>
@endsection
