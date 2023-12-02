@extends('layouts.main')

@section('content')
    <!-- icerik buraya girilir -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Instructor X Setup
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
                <div class="portlet box red ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gift"></i> Add Instructor </div>
                        <div class="tools">
                            <a href="" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="" class="reload" data-original-title="" title=""> </a>
                            <a href="" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body form" style="display: block;">
                        @if($data["id"] > 0)
                            <form class="form-horizontal"  role="form" id="kayitForm" method="post" action="/egitim_hocalar/{{$data['id']}}">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/egitim_hocalar">
                        @endif
                            <input type="hidden" id="egitim_kayit_id" name="egitim_kayit_id" value="{{$bilgi->egitim_kayit_id}}">
                            <input type="hidden" id="teklif_id" name="teklif_id" value="{{$bilgi->id}}">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Date Delivery</label>
                                    <div class="col-md-2">
                                        <input type="text" name="ders_tarihi" id="ders_tarihi" class="form-control" value="{{old('ders_tarihi', ($data->ders_tarihi != '' ? date('d.m.Y', strtotime($data->ders_tarihi)) : '') )}}">
                                    </div>
                                    <label class="col-md-2 control-label">Start Time:</label>
                                    <div class="col-md-2">
                                        <input type="text" name="baslama_saati" id="baslama_saati" class="form-control" value="{{old('baslama_saati', $data->baslama_saati)}}">
                                    </div>
                                    <label class="col-md-2 control-label">Finish Time:</label>
                                    <div class="col-md-2">
                                        <input type="text" name="bitis_saati" id="bitis_saati" class="form-control" value="{{old('bitis_saati', $data->bitis_saati)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Instructor Name</label>
                                    <div class="col-md-9">
                                        <select name="hoca_id" id="hoca_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($hocalar as $row)
                                                <option value="{{$row->kullanici_id}}" {{old('hoca_id', $data->hoca_id) == $row->kullanici_id ? ' selected' : ''}}>{{trim($row->unvani['adi']." ".$row->adi_soyadi)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Abbrevitation</label>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" id="hoca_kisa_adi" name="hoca_kisa_adi" value="{{old('hoca_kisa_adi', $data->hoca_kisa_adi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Which Day Topics</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="kisa_tanim" name="kisa_tanim" value="{{old('kisa_tanim', $data->kisa_tanim)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"> Fee</label>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" id="ucret" name="ucret" value="{{old('ucret', floatval($data->ucret))}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn green">Save</button>
                                <a href="/{{$prefix}}" class="btn default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i>List of Instructors </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th scope="col"> # </th>
                                    <th scope="col"> Date of Delivery </th>
                                    <th scope="col"> Start Time </th>
                                    <th scope="col"> Instructor Name </th>
                                    <th scope="col"> Abbrevitation </th>
                                    <th scope="col"> Which Day Topics </th>
                                    <th scope="col"> Fee </th>
                                    <th scope="col"> Action </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($atanan_liste as $key => $row)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{date('d.m.Y', strtotime($row->ders_tarihi))}}</td>
                                        <td>{{date('H:s', strtotime($row->baslama_saati))}} - {{date('H:s', strtotime($row->bitis_saati))}}</td>
                                        <td>{{trim($row->hocaBilgi->unvani['adi'].' '.$row->hocaBilgi['adi_soyadi'])}}</td>
                                        <td>{{$row->hoca_kisa_adi}}</td>
                                        <td>{{$row->kisa_tanim}}</td>
                                        <td>{{$row->ucret}}</td>
                                        <td>
                                            <a href="/cc_now/insxsetup/{{$bilgi->id}}/edit/{{$row->id}}" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->hocaBilgi->adi_soyadi}}'"><i class="fa fa-pencil"></i></a>
                                            @if(Auth::user()->isAllow('cca_egitimhoca_del'))
                                                <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->hocaBilgi->adi_soyadi}}'" onclick="silmeKontrol('{{$row->id}}', '/egitim_hocalar')"><i class="fa fa-trash"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
            $('#ders_tarihi').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy',
                startDate: '{{date('d.m.Y', strtotime($baslama_tarihi))}}',
                endDate: '{{date('d.m.Y', strtotime($bitis_tarihi))}}'
            });
            $('#baslama_saati, #bitis_saati').inputmask('99:99', {clearIncomplete: true});
            $("#hoca_id").select2();
        });
    </script>
@endsection
