@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Hotels
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
                                    <label class="col-sm-2 control-label">Hotel Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="adi" name="adi" class="form-control" value="{{old('adi', $data["adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">City</label>
                                    <div class="col-sm-4">
                                        <select id="sehir_id" name="sehir_id" class="select2 form-control" onchange="bolgeGetirJson()">
                                            <option value="">Select</option>
                                            @foreach($sehirler as $row)
                                                <option value="{{$row->id}}" {{old('sehir_id', $data->sehir_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Region-Semt</label>
                                    <div class="col-sm-4">
                                        <select id="bolge_id" name="bolge_id" class="select2 form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Star Rating</label>
                                    <div class="col-sm-4">
                                        <select id="derece_id" name="derece_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($dereceler as $row)
                                                <option value="{{$row->id}}" {{old('derece_id', $data->derece_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Website (Link)</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="web_adresi" name="web_adresi" class="form-control" value="{{old('web_adresi', $data["web_adresi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tel</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="telefon" name="telefon" class="form-control" value="{{old('telefon', $data["telefon"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="email" name="email" class="form-control" value="{{old('email', $data->email)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Address</label>
                                    <div class="col-sm-6">
                                        <textarea id="adres" name="adres" class="form-control" rows="3">{{old('adres', $data->adres)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Coordinates</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="koordinat_x" name="koordinat_x" class="form-control" value="{{old('koordinat_x', $data->koordinat_x)}}">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" id="koordinat_y" name="koordinat_y" class="form-control" value="{{old('koordinat_y', $data->koordinat_y)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Distance to 'SAHARA HQ (in Km)</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="ofise_uzaklik" name="ofise_uzaklik" class="form-control" value="{{old('ofise_uzaklik', $data->ofise_uzaklik)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"># of Rooms</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="oda_sayisi" name="oda_sayisi" class="form-control" value="{{old('oda_sayisi', $data->oda_sayisi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Room Types & Rates</label>
                                    <div class="col-sm-8">
                                        <div class="col-sm-12">
                                            <div class="col-sm-1"></div>
                                            <div class="col-sm-2 font-red">Room Type</div>
                                            <div class="col-sm-2 font-red">Buy</div>
                                            <div class="col-sm-2 font-red">Sell</div>
                                            <div class="col-sm-2 font-red">Na</div>
                                        </div>
                                        @foreach($odatipleri as $key => $row)
                                            <div class="col-sm-12">
                                                <div class="col-sm-1"><input type="checkbox" class="form-control" name="oda_tip_id[]" id="oda_tip_id" value="{{$row->id}}" {{$row->secilmis == 1 || old('oda_tip_id.'.$key) ? " checked" : ""}}></div>
                                                <div class="col-sm-2">{{$row->adi}}</div>
                                                <div class="col-sm-2"><input type="text" class="form-control" name="ucret_alis_{{$row->id}}" id="ucret_alis" value="{{old('ucret_alis_'.$row->id, $row->ucret_alis)}}"></div>
                                                <div class="col-sm-2"><input type="text" class="form-control" name="ucret_satis_{{$row->id}}" id="ucret_satis" value="{{old('ucret_satis_'.$row->id, $row->ucret_satis)}}"></div>
                                                <div class="col-sm-2"><input type="checkbox" class="form-control" name="flg_na_{{$row->id}}" id="flg_na" {{$row->flg_na == 1 || old('flg_na_'.$row->id) ? " checked" : ""}}></div>
                                            </div>
                                            @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"># of Meeting Rooms</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="toplanti_oda_sayisi" name="toplanti_oda_sayisi" class="form-control" value="{{old('toplanti_oda_sayisi', $data->toplanti_oda_sayisi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Meeting Room Rates</label>
                                    <div class="col-sm-8">
                                        <div class="col-sm-12">
                                            <div class="col-sm-3">Half-Day Rate</div>
                                            <div class="col-sm-2">
                                                <input type="text" id="yarim_ucret" name="yarim_ucret" class="form-control" value="{{old('yarim_ucret', $data->yarim_ucret)}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="col-sm-3">Full-Day Rate</div>
                                            <div class="col-sm-2">
                                                <input type="text" id="tam_ucret" name="tam_ucret" class="form-control" value="{{old('tam_ucret', $data->tam_ucret)}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="col-sm-3">Min Pax</div>
                                            <div class="col-sm-2">
                                                <input type="text" id="min_katilimci" name="min_katilimci" class="form-control" value="{{old('tam_ucret', $data->min_katilimci)}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Meeting Rooms</label>
                                    <div class="col-sm-6">
                                        <textarea id="toplanti_oda_aciklama" name="toplanti_oda_aciklama" class="form-control" rows="3">{{old('toplanti_oda_aciklama', $data->toplanti_oda_aciklama)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Ask for the current price</label>
                                    <div class="col-sm-2">
                                        <select id="flg_fiyatsor" name="flg_fiyatsor" class="form-control">
                                            <option value="0" {{old('flg_fiyatsor', $data->flg_fiyatsor) == 0 ? " selected" : ""}}>No</option>
                                            <option value="1" {{old('flg_fiyatsor', $data->flg_fiyatsor) == 1 ? " selected" : ""}}>Yes</option>
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
            bolgeGetirJson();
        });
        function bolgeGetirJson() {
            $("#bolge_id option:gt(0)").remove();
            if($("#sehir_id").val() == "") {
                $("#bolge_id option:first").prop('selected', true);
                $("#bolge_id").trigger('change');
                return;
            }
            var data = {
                '_token' : "{{csrf_token()}}",
                'sehir_id' : $("#sehir_id").val()
            };
            showLoading('', '');
            $.post("/{{$prefix}}/bolgeGetirJson", data, function(cevap) {
                $("#bolge_id option:first").prop('selected', true);
                $.each(cevap, function (i, row) {
                    $("#bolge_id").append("<option value='" + row.id + "' " + ( row.id == "{{old('bolge_id', $data->bolge_id)}}" ? " selected" : "" ) + ">" + row.adi + "</option>");
                });
            }, "json").done(function () {
                hideLoading();
                $("#bolge_id").trigger('change');

            });
        }
        function formuKaydet() {
            showLoading('', '');
        }
    </script>
@endsection
