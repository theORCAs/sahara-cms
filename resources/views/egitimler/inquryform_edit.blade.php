@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Airline Entry
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
                        <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}">
                            @method("put")
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Form Registration Date</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{date('d.m.Y', strtotime($data->created_at))}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Form Registration IP</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->kayit_ip}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Full Name</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->unvan->adi." ".$data->adi_soyadi}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Department & Job Title</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->departman}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Country</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->ulke->adi}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">City</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->sehir}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Company</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->referansSirket->adi." ".$data->sirket_adi}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Company Website</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->sirket_web}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Mobile (GSM)</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{trim($data->telefon_kodu." ".$data->telefon)}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Fax</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{trim($data->faks_kodu." ".$data->faks)}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Email</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->email}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Subject</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{$data->egitim->adi == "" ? 'General Inqury' : $data->egitim->adi}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Inquiry</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{!! nl2br($data->aciklama) !!}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Status</label>
                                    <div class="col-sm-2">
                                        <select id="flg_durum" name="flg_durum" class="form-control">
                                            <option value="1" {{old('flg_durum', $data->flg_durum) == "1" ? ' selected' : ''}}>Active</option>
                                            <option value="0" {{old('flg_durum', $data->flg_durum) == "0" ? ' selected' : ''}}>Passive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-sm-4 control-label">Answered</label>
                                    <div class="col-sm-2">
                                        <select id="flg_cevaplandi" name="flg_cevaplandi" class="form-control">
                                            <option value="1" {{old('flg_cevaplandi', $data->flg_cevaplandi) == "1" ? ' selected' : ''}}>Yes</option>
                                            <option value="0" {{old('flg_cevaplandi', $data->flg_cevaplandi) == "0" ? ' selected' : ''}}>No</option>
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
