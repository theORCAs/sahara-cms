@extends('layouts.main')

@section('content')
    <!-- icerik buraya girilir -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Visa Form Filled View
            <small>{{$bilgi->egitimKayit->egitimler["kodu"]." ".$bilgi->egitimKayit->egitimler["adi"]}}</small>
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
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Information</div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">HR Admin</label>
                                <div class="col-sm-9 help-block">{{$bilgi->egitimKayit->ct_adi_soyadi}}</div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Course Title</label>
                                <div class="col-sm-9 help-block">{{$bilgi->egitimKayit->egitimler->kodu." ".$bilgi->egitimKayit->egitimler->adi}}</div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Course Date</label>
                                <div class="col-sm-9 help-block">{{date('d.m.Y', strtotime($bilgi->egitimKayit->egitimTarihi->baslama_tarihi))}}</div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Organization Company</label>
                                <div class="col-sm-9 help-block">{{$bilgi->egitimKayit->sirket_adi}}</div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Country</label>
                                <div class="col-sm-9 help-block">{{$bilgi->egitimKayit->sirketUlke->adi}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i>List of Participants </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}/visaFormFilledSave/{{$hid_teklif_id}}" enctype="multipart/form-data">
                                @csrf
                                <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col"> Action </th>
                                        <th scope="col">  </th>
                                        <th scope="col"> Name </th>
                                        <th scope="col"> Passport No </th>
                                        <th scope="col"> D.O.B </th>
                                        <th scope="col"> Issuing Authority </th>
                                        <th scope="col"> Date of Issue </th>
                                        <th scope="col"> Expiry Date </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($katilimcilar as $key => $row)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="katilimci_id[]" value="{{$row->id}}">
                                            </td>
                                            <td>
                                                <select id="unvan_id" name="unvan_id[]" class="form-control">
                                                    @foreach($unvanlar as $unvan)
                                                        <option value="{{$unvan->id}}" {{old('unvan_id.'.$key, $row->unvan_id) == $unvan->id ? ' selected' : ''}}>{{$unvan->adi}}</option>
                                                        @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="adi_soyadi[]" id="adi_soyadi" value="{{old('adi_soyadi.'.$key, $row->adi_soyadi)}}" class="form-control">
                                            </td>
                                            <td><input type="text" name="vf_pasaport[]" id="vf_pasaport" value="{{old('vf_pasaport.'.$key, $row->vf_pasaport)}}" class="form-control"></td>
                                            <td><input type="text" name="vf_dogum_tarihi[]" id="vf_dogum_tarihi"
                                                       value="{{old('vf_dogum_tarihi.'.$key, $row->vf_dogum_tarihi) != '' ? date('d.m.Y', strtotime(old('vf_dogum_tarihi.'.$key, $row->vf_dogum_tarihi))) : ''}}"
                                                       class="form-control date-picker">
                                            </td>
                                            <td><input type="text" name="vf_duzenleyen_makam[]" id="vf_duzenleyen_makam" value="{{old('vf_duzenleyen_makam.'.$key, $row->vf_duzenleyen_makam)}}" class="form-control"></td>
                                            <td><input type="text" name="vf_verilis_tarihi[]" id="vf_verilis_tarihi"
                                                       value="{{old('vf_verilis_tarihi.'.$key, $row->vf_verilis_tarihi) != '' ? date('d.m.Y', strtotime(old('vf_verilis_tarihi.'.$key, $row->vf_verilis_tarihi))) : ''}}"
                                                       class="form-control date-picker">
                                            </td>
                                            <td><input type="text" name="vf_sonkullanma_tarihi[]" id="vf_sonkullanma_tarihi"
                                                       value="{{old('vf_sonkullanma_tarihi.'.$key, $row->vf_sonkullanma_tarihi) != '' ? date('d.m.Y', strtotime(old('vf_sonkullanma_tarihi.'.$key, $row->vf_sonkullanma_tarihi))) : ''}}"
                                                       class="form-control date-picker">
                                            </td>
                                        </tr>
                                    @endforeach
                                    @for($i = $key; $i < 10; $i++)
                                        <tr id="satir_{{$i}}">
                                            <td>
                                                <input type="hidden" name="katilimci_id[]" value="">
                                            </td>
                                            <td>
                                                <select id="unvan_id" name="unvan_id[]" class="form-control">
                                                    @foreach($unvanlar as $unvan)
                                                        <option value="{{$unvan->id}}" {{old('unvan_id.'.$key) == $unvan->id ? ' selected' : ''}}>{{$unvan->adi}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="adi_soyadi[]" id="adi_soyadi" value="{{old('adi_soyadi.'.$i)}}" class="form-control"></td>
                                            <td><input type="text" name="vf_pasaport[]" id="vf_pasaport" value="{{old('vf_pasaport.'.$i)}}" class="form-control"></td>
                                            <td><input type="text" name="vf_dogum_tarihi[]" id="vf_dogum_tarihi"
                                                       value="{{old('vf_dogum_tarihi.'.$i) != '' ? date('d.m.Y', strtotime(old('vf_dogum_tarihi.'.$i))) : ''}}"
                                                       class="form-control date-picker">
                                            </td>
                                            <td><input type="text" name="vf_duzenleyen_makam[]" id="vf_duzenleyen_makam" value="{{old('vf_duzenleyen_makam.'.$i)}}" class="form-control"></td>
                                            <td><input type="text" name="vf_verilis_tarihi[]" id="vf_verilis_tarihi"
                                                       value="{{old('vf_verilis_tarihi.'.$i) != '' ? date('d.m.Y', strtotime(old('vf_verilis_tarihi.'.$i))) : ''}}"
                                                       class="form-control date-picker">
                                            </td>
                                            <td><input type="text" name="vf_sonkullanma_tarihi[]" id="vf_sonkullanma_tarihi"
                                                       value="{{old('vf_sonkullanma_tarihi.'.$i) != '' ? date('d.m.Y', strtotime(old('vf_sonkullanma_tarihi.'.$i))) : ''}}"
                                                       class="form-control date-picker">
                                            </td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-center">
                                            <button type="submit" class="btn green" onclick="formuKaydet()">Submit</button>
                                            <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyları yuklenir -->
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <!-- js dosyları yuklenir -->
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // $('#ders_tarihi').datepicker();

            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy',

            });
        });
    </script>
@endsection
