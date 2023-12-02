@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Suppliers & Payment Module
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
                                    <label class="col-md-2 control-label">Payment Category</label>
                                    <div class="col-md-6">
                                        <select id="kategori_id" name="kategori_id" class="select2 form-control" onchange="partnerGetir()">
                                            <option value="">Select</option>
                                            @foreach($odeme_kategorileri as $row)
                                                <option value="{{$row->id}}" {{old('kategori_id', $data->kategori_id) == $row->id ? 'selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Company/Person Name</label>
                                    <div class="col-md-10">
                                        <select id="partner_id" name="partner_id" class="select2 form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Document Date</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="dekont_tarihi" name="dekont_tarihi" class="date-picker form-control"
                                               value="{{old('dekont_tarihi', $data->dekont_tarihi) != '' ? date('d.m.Y', strtotime(old('dekont_tarihi', $data->dekont_tarihi))) : ''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="tutar" name="tutar" class="form-control" value="{{old('tutar', $data->tutar)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Bank Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="banka" name="banka" class="form-control" value="{{old('banka', $data->banka)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Account IBAN</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="iban" name="iban" class="form-control" value="{{old('iban', $data->iban)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Additional Notes</label>
                                    <div class="col-sm-6">
                                        <textarea name="aciklama" id="aciklama" rows="2" class="form-control">{{old('aciklama', $data->aciklama)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-2">
                                        <select name="durum" id="durum" class="form-control">
                                            <option value="0" {{old('durum', $data->durum) == "0" ? " selected" : ""}}>Not Paid</option>
                                            <option value="1" {{old('durum', $data->durum) == "1" ? " selected" : ""}}>Paid Close</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Payment Date</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="odeme_tarihi" id="odeme_tarihi"
                                               class="form-control date-picker" value="{{old('odeme_tarihi', ($data->odeme_tarihi != '' ? date('d.m.Y', strtotime($data->odeme_tarihi)) : "") )}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Document to be issued/received</label>
                                    <div class="col-sm-4">
                                        <select name="dekont_durum_id" id="dekont_durum_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($odeme_bekleme_turleri as $row)
                                                <option value="{{$row->id}}" {{old('dekont_durum_id', $data->dekont_durum_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Document</label>
                                    <div class="col-sm-10">
                                        <textarea id="dekont" name="dekont" class="ckeditor">{{old('dekont', $data->dekont)}}</textarea>
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
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });

            partnerGetir();
        });
        function partnerGetir() {
            var tmp_partner_id = "{{old('partner_id', $data->partner_id)}}";
            var data = {
                '_token' : "{{csrf_token()}}",
                'kategori_id' : $("#kategori_id").val()
            };
            $("#partner_id option:first").prop('selected', true);
            $.post("/spm_watingpayment/partnerGetirJson", data, function (cevap) {
                $("#partner_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#partner_id").append("<option value='" + row.id + "' " + (tmp_partner_id == row.id ? " selected" : "") + ">" + row.adi + "</option>");
                })
            }, "json").done(function () {
                $("#partner_id").trigger('change');
            });
        }
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
