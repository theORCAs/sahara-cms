@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Hotel Reservation
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
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Form Filling Detail</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Date</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{date("d.m.Y", strtotime($data->created_at))}}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">IP Address</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{$data->kayit_ip}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Course Detail</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Title</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{$data->teklif->egitimKayit->egitimler->kodu." ".$data->teklif->egitimKayit->egitimler->adi}}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Course Start Date</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{date('d.m.Y', strtotime($data->teklif->egitimKayit->egitimTarihi->baslama_tarihi))}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Company Detail</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Company</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{$data->teklif->egitimKayit->sirketReferans->adi}}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Country</label>
                                    <div class="col-sm-10">
                                        <label class="control-label font-purple">{{$data->teklif->egitimKayit->sirketUlke->adi}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Details of Person Making Reservation</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="adi_soyadi" name="adi_soyadi" class="form-control" value="{{old('adi_soyadi', $data->adi_soyadi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="email" name="email" class="form-control" value="{{old('email', $data->email)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Mobile Phone (GSM)</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="cep" name="cep" class="form-control" value="{{old('cep', $data->cep)}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Reservation Details</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Check-in Date</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="tarih_giris" name="tarih_giris" class="form-control date-picker"
                                               value="{{old('tarih_giris', $data->tarih_giris) != "" ? date("d.m.Y", strtotime(old('tarih_giris', $data->tarih_giris))) : ""}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Check-out Date</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="tarih_cikis" name="tarih_cikis" class="form-control date-picker"
                                               value="{{old('tarih_cikis', $data->tarih_cikis) != "" ? date("d.m.Y", strtotime(old('tarih_cikis', $data->tarih_cikis))) : ""}}">
                                    </div>
                                </div>
                                @if($data->otelRezervasyonOda()->count() > 0)
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">-</label>
                                        <div class="col-sm-5 font-red">Hotel Name</div>
                                        <div class="col-sm-2 font-red">Room Type</div>
                                        <div class="col-sm-2 font-red"># of Room</div>
                                        <div class="col-sm-2 font-red">Per At Night</div>
                                    </div>
                                    @endif
                                @foreach($data->otelRezervasyonOda() as $row)
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">DEL</label>
                                        <div class="col-sm-5">{{$row->otel_adi}}</div>
                                        <div class="col-sm-2">{{$row->oda_tipi_adi}}</div>
                                        <div class="col-sm-2">{{$row->oda_sayisi}}</div>
                                        <div class="col-sm-2">{{$row->gecelik_ucret}} &euro;</div>
                                    </div>
                                    @endforeach
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">View Option</label>
                                    <div class="col-sm-8">
                                        <select id="manzara_id" name="manzara_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($manzaralar as $row)
                                                <option value="{{$row->id}}" @if($row->id == $data->manzara_id) selected @endif>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Guest Detail</label>
                                    </div>
                                </div>
                                @if($data->kisiler->count() > 0)
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"></label>
                                        <div class="col-sm-5 font-red">Name</div>
                                        <div class="col-sm-1 font-red">Age</div>
                                        <div class="col-sm-2 font-red">Gender</div>
                                        <div class="col-sm-2"></div>
                                    </div>
                                    @endif
                                @foreach($data->kisiler as $key => $row)
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">
                                            <input type="hidden" name="ki_id[]" id="ki_id" value="{{$row->id}}">
                                        </label>
                                        <div class="col-sm-5"><input type="text" name="ki_adi[]" id="ki_adi" class="form-control" value="{{old('ki_adi.'.$key, $row->adi)}}"></div>
                                        <div class="col-sm-1"><input type="text" name="ki_yas[]" id="ki_yas" class="form-control" value="{{old('ki_yas.'.$key, $row->yas)}}"></div>
                                        <div class="col-sm-2">
                                            <select name="ki_cinsiyet[]" id="ki_cinsiyet" class="form-control">
                                                <option value="">Select</option>
                                                <option value="Male" {{old('ki_cinsiyet.'.$key, $row->cinsiyet) == "Male" ? " selected" : ""}}>Male</option>
                                                <option value="Female" {{old('ki_cinsiyet.'.$key, $row->cinsiyet) == "Female" ? " selected" : ""}}>Female</option>
                                                <option value="Child" {{old('ki_cinsiyet.'.$key, $row->cinsiyet) == "Child" ? " selected" : ""}}>Child</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">-</div>
                                    </div>
                                    @endforeach

                                <div class="form-group">
                                    <label class="col-sm-1 control-label"></label>
                                    <div class="col-sm-11">
                                        <label class="control-label font-red">Process Details</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Additional Info</label>
                                    <div class="col-sm-6"><textarea name="ek_notlar" id="ek_notlar" rows="3" class="form-control">{{old('ek_notlar', $data->ek_notlar)}}</textarea></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Related person</label>
                                    <div class="col-sm-8">
                                        <select name="ilgili_kisi" id="ilgili_kisi" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach($ilgili_kisiler as $row)
                                                <option value="{{$row->id}}">{{$row->rol_adi." / ".$row->adi_soyadi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Transaction Message</label>
                                    <div class="col-sm-6"><textarea name="islem_mesaji" id="islem_mesaji" rows="3" class="form-control">{{old('islem_mesaji', $data->islem_mesaji)}}</textarea></div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-2">
                                        <select name="durum" id="durum" class="form-control select2">
                                            <option value="">Select</option>
                                            <option value="1" {{old('durum', $data->durum) == 1 ? " selected" : ""}}>Request</option>
                                            <option value="4" {{old('durum', $data->durum) == 4 ? " selected" : ""}}>Processing</option>
                                            <option value="2" {{old('durum', $data->durum) == 2 ? " selected" : ""}}>Confirmed</option>
                                            <option value="3" {{old('durum', $data->durum) == 3 ? " selected" : ""}}>Denied</option>
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
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });

        });
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
