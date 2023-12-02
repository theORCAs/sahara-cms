@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Delivery CONFIRMATION (AFTER course assignment) & Material Upload
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
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
                    <div class="portlet-body">
                        <div class="form-body form">
                            <div class="form-group">
                                <label class="font-red"><input type="checkbox" id="ch_materyal" {{$data->ony_materyal == "1" ? " checked" : ""}} onclick="icerikGoster('materyal')"> 1) Course Material</label>
                                <div class="input-group">
                                    <div class="{{$data->ony_materyal == "1" ? "" : "hidden"}}" id="cnt_materyal">{!! $materyal_yazi->icerik !!}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-red"><input type="checkbox" id="ch_guideline" {{$data->ony_guideline == "1" ? " checked" : ""}} onclick="icerikGoster('guideline')"> 2) Course Delivery</label>
                                <div class="input-group">
                                    <div class="{{$data->ony_guideline == "1" ? "" : "hidden"}}" id="cnt_guideline">{!! $delivery_yazi->icerik !!}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-red"><input type="checkbox" id="ch_feerate" {{$data->ony_feerate == "1" ? " checked" : ""}} onclick="icerikGoster('feerate')"> 3) Instructor Fee Rates</label>
                                <div class="input-group">
                                    <div class="{{$data->ony_feerate == "1" ? "" : "hidden"}}" id="cnt_feerate">{!! $fee_yazi->icerik !!}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-red"><input type="checkbox" id="ch_feepay" {{$data->ony_feepay == "1" ? " checked" : ""}} onclick="icerikGoster('feepay')"> 4) Payments</label>
                                <div class="input-group">
                                    <div class="{{$data->ony_feepay == "1" ? "" : "hidden"}}" id="cnt_feepay">{!! $payment_yazi->icerik !!}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-red"><input type="checkbox" id="ch_confidentiality" {{$data->ony_confidentiality == "1" ? " checked" : ""}} onclick="icerikGoster('confidentiality')"> 5) Confidentiality</label>
                                <div class="input-group">
                                    <div class="{{$data->ony_confidentiality == "1" ? "" : "hidden"}}" id="cnt_confidentiality">{!! $conf_yazi->icerik !!}</div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
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

        function icerikGoster(tmp) {
            var tmp_islem = "0";
            if($("#ch_" + tmp).is(":checked")) {
                tmp_islem = "1";
                $("#cnt_" + tmp).removeClass('hidden');
            } else
                $("#cnt_" + tmp).addClass('hidden');

            var data = {
                '_token' : "{{csrf_token()}}",
                'id' : "{{$data->id}}",
                'alan_adi' : 'ony_' + tmp,
                'islem' : tmp_islem
            }
            $.post('/cm_readandconfirm_set', data, function (cevap) {

            }, "json");
        }
    </script>
@endsection
