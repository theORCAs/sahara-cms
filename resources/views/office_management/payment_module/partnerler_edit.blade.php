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
                                    <label class="col-md-3 control-label">Payment Category</label>
                                    <div class="col-md-6">
                                        <select id="kategori_id" name="kategori_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($kategori_listesi as $row)
                                                <option value="{{$row->id}}" {{old('kategori_id', $data->kategori_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label font-red">Company General Information</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Company/Person Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Service Provided</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="servis" name="servis" class="form-control" value="{{old('servis', $data->servis)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Website</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="website" name="website" class="form-control" value="{{old('website', $data->website)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">City</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="sehir_adi" name="sehir_adi" class="form-control" value="{{old('sehir_adi', $data->sehir_adi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Country</label>
                                    <div class="col-sm-4">
                                        <select id="ulke_id" name="ulke_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($ulke_listesi as $row)
                                                <option value="{{$row->id}}" {{old('ulke_id', $data->ulke_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label font-red">Contact Person Details</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="ilgili_kisi" name="ilgili_kisi" class="form-control" value="{{old('ilgili_kisi', $data->ilgili_kisi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Email Address 1</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="ilgili_email" name="ilgili_email" class="form-control" value="{{old('ilgili_email', $data->ilgili_email)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Email Address 2</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="ilgili_email1" name="ilgili_email1" class="form-control" value="{{old('ilgili_email1', $data->ilgili_email1)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Telephone (Land Line)</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="ilgili_cep" name="ilgili_cep" class="form-control" value="{{old('ilgili_cep', $data->ilgili_cep)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Telephone (Mobile)</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="ilgili_cep1" name="ilgili_cep1" class="form-control" value="{{old('ilgili_cep1', $data->ilgili_cep1)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label font-red">Bank Details for Payment</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Bank Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="banka_adi" name="banka_adi" class="form-control" value="{{old('banka_adi', $data->banka_adi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Branch Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="banka_sube" name="banka_sube" class="form-control" value="{{old('banka_sube', $data->banka_sube)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Account Owner</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="hesap_sahibi" name="hesap_sahibi" class="form-control" value="{{old('hesap_sahibi', $data->hesap_sahibi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Account No</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="hesap_no" name="hesap_no" class="form-control" value="{{old('hesap_no', $data->hesap_no)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Account IBAN</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="iban" name="iban" class="form-control" value="{{old('iban', $data->iban)}}">
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
