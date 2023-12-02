@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Proposal Module
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
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-body form-horizontal">
                        <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}" enctype="multipart/form-data">
                            @method("put")
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">Proposal Details</div>
                                </div>
                                <input type="hidden" name="formId" value="{{$data['id']}}">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Form Filled Date</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">{{date("d.m.Y", strtotime($data["created_at"]))}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">IP of Form Filling Party</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">{{$data["kayit_ip"]}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Status</label>
                                    <div class="col-md-3">
                                        <select id="durum" name="durum" class="form-control">
                                            <option value="1" @if(old('durum', $data["durum"]) == 1) selected @endif>Waiting</option>
                                            <option value="2" @if(old('durum', $data["durum"]) == 2) selected @endif>Confirmed</option>
                                            <option value="3" @if(old('durum', $data["durum"]) == 3) selected @endif>Rejected</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">A - Course Details</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Course Code</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">{{$data->egitimler->kodu." ".$data->egitimler->adi}}</p>
                                        <a href="javascript:;">[Change]</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Start Date</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">{{date("d.m.Y", strtotime($data["egitimTarihi"]["baslama_tarihi"]))." / ".$data["egitimTarihi"]["egitim_suresi"]." ".$data["egitimTarihi"]["egitimPart"]["adi"]}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Venue</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">{{$data->egitimTarihi->egitimYeri->adi}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Fee</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">$ {{$data["egitimTarihi"]->egitimUcretiGetir()}}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">B - Institution/Company Details</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Country *</label>
                                    <div class="col-md-3">
                                        @php($tmp_tel_kodu = "---")
                                        <select id="sirket_ulke_id" name="sirket_ulke_id" class="form-control select2" onchange="telKoduDegistir()">
                                            <option value="" data-telkod="">Select</option>
                                            @foreach($ulkeler as $row)
                                                @if($row->id == $data["sirket_ulke_id"])
                                                    @php($tmp_tel_kodu = $row->tel_kodu)
                                                @endif
                                                <option value="{{$row->id}}" {{old('sirket_ulke_id', $data->sirket_ulke_id) == $row->id ? ' selected' : ''}} data-telkod="{{$row->tel_kodu}}">{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Company Select</label>
                                    <div class="col-md-4">
                                        <select id="referans_id" name="referans_id" class="select2 form-control" onchange="sirketadiGoster()">
                                            <option value="">Select</option>
                                            @foreach($referanslar as $row)
                                                <option value="{{$row->id}}" {{old('referans_id', $data->referans_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group company-name">
                                    <label class="col-md-2 control-label">Company Name *</label>
                                    <div class="col-md-4">
                                        <input type="text" id="sirket_adi" name="sirket_adi" class="form-control" value="{{old('sirket_adi', $data["sirket_adi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Website *</label>
                                    <div class="col-md-4">
                                        <input type="text" id="sirket_web" name="sirket_web" class="form-control" value="{{old('sirket_web', $data["sirket_web"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Nature of Business</label>
                                    <div class="col-md-4">
                                        <input type="text" id="yapilan_is" name="yapilan_is" class="form-control" value="{{old('yapilan_id', $data["yapilan_is"])}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4 font-red">C - Contact Person Details for Invoicing (Training Department)</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Salutation *</label>
                                    <div class="col-md-2">
                                        <select id="ct_unvan_id" name="ct_unvan_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach($unvanlar as $row)
                                                <option value="{{$row->id}}" {{old('ct_unvan_id', $data->ct_unvan_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Full Name *</label>
                                    <div class="col-md-4">
                                        <input type="text" name="ct_adi_soyadi" id="ct_adi_soyadi" class="form-control" value="{{old('ct_adi_soyadi', $data["ct_adi_soyadi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Job Title *</label>
                                    <div class="col-md-4">
                                        <input type="text" name="ct_pozisyon" id="ct_pozisyon" class="form-control" value="{{old('ct_pozisyon', $data["ct_pozisyon"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Postal Address *</label>
                                    <div class="col-md-4">
                                        <textarea id="sirket_adres" name="sirket_adres" rows="2" class="form-control">{{old('sirket_adres', $data["sirket_adres"])}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">E-mail (corporate) *</label>
                                    <div class="col-md-4">
                                        <input type="email" name="ct_sirket_email" id="ct_sirket_email" class="form-control" value="{{old('ct_sirket_email', $data["ct_sirket_email"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Telephone *</label>
                                    <div class="col-md-4">
                                        <div class="input-inline input-medium">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span id="txt_ct_telefon_kodu">{{$tmp_tel_kodu}}</span>
                                                    <input type="hidden" id="ct_telefon_kodu" name="ct_telefon_kodu"
                                                           value="{{($data["ct_telefon_kodu"] != "" ? $data["ct_telefon_kodu"] : $tmp_tel_kodu)}}">
                                                </span>
                                                <input type="text" id="ct_telefon" name="ct_telefon" class="form-control" value="{{old('ct_telefon', $data["ct_telefon"])}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mobile *</label>
                                    <div class="col-md-4">
                                        <div class="input-inline input-medium">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span id="txt_ct_cep_kodu">{{$tmp_tel_kodu}}</span>
                                                    <input type="hidden" id="ct_cep_kodu" name="ct_cep_kodu"
                                                           value="{{($data["ct_cep_kodu"] != "" ? $data["ct_cep_kodu"] : $tmp_tel_kodu)}}">
                                                </span>
                                                <input type="text" id="ct_cep" name="ct_cep" class="form-control" value="{{old('ct_cep', $data["ct_cep"])}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red"><a href="/participant/view/{{$crf_id}}" target="_blank">D - Participant(s) Details</a></div>
                                </div>
                                @foreach($data["katilimcilar"] as $key => $row)
                                    <div class="form-group">
                                        <label class="col-md-2 control-label text-danger">{{$key +1 }} Participant</label>
                                        <input type="hidden" name="hid_katilimci_id[]" value="{{$row->id}}">
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Salutation *</label>
                                        <div class="col-md-2">
                                            <select id="k_unvan_id" name="k_unvan_id[]" class="form-control select2">
                                                <option value="">Select</option>
                                                @foreach($unvanlar as $u_row)
                                                    <option value="{{$u_row->id}}" {{old('k_unvan_id.'.$key, $row->unvan_id) == $u_row->id ? ' selected' : ''}}>{{$u_row->adi}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Name *</label>
                                        <div class="col-xs-4">
                                            <input type="text" id="k_adi_soyadi" name="k_adi_soyadi[]" class="form-control"
                                                   value="{{old('k_adi_soyadi.'.$key, $row->adi_soyadi)}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">City of Living</label>
                                        <div class="col-xs-3">
                                            <select id="k_yasadigi_ulke_id" name="k_yasadigi_ulke_id[]" class="form-control select2">
                                                <option value="" data-telkod="">Select</option>
                                                @foreach($ulkeler as $u_row)
                                                    <option value="{{$u_row->id}}" {{old('k_yasadigi_ulke_id.'.$key, $row->k_yasadigi_ulke_id) == $u_row->id ? ' selected' : ''}}
                                                        data-telkod="{{$u_row->tel_kodu}}">{{$u_row->adi}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Email</label>
                                        <div class="col-xs-4">
                                            <input type="text" id="k_email" name="k_email[]" class="form-control" value="{{old('k_email.'.$key, $row->email)}}">
                                            <input type="text" id="k_email2" name="k_email2[]" class="form-control" value="{{old('k_email2.'.$key, $row->email2)}}" placeholder="Email 2">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Job Title</label>
                                        <div class="col-xs-4">
                                            <input type="text" id="k_is_pozisyonu" name="k_is_pozisyonu[]" class="form-control" value="{{old('k_is_pozisyonu.'.$key, $row->is_pozisyonu)}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Mobile Phone</label>
                                        <div class="col-xs-4">
                                            <div class="input-inline input-medium">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <span class="k_cep_tel_kodu">{{$row->cep_tel_kodu != "" ? $row->cep_tel_kodu : $tmp_tel_kodu}}</span>
                                                        <input type="hidden" id="k_cep_tel_kodu" name="k_cep_tel_kodu[]"
                                                               data-katilimciid="{{$row->id}}" value="{{$row->cep_tel_kodu}}">
                                                    </span>
                                                    <input type="text" id="k_cep_tel" name="k_cep_tel[]" class="form-control" value="{{old('k_cep_tel.'.$key, $row->cep_tel)}}">
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <span class="k_cep_tel_kodu">{{$row->cep_tel2_kodu != "" ? $row->cep_tel2_kodu : $tmp_tel_kodu}}</span>
                                                        <input type="hidden" id="k_cep_tel2_kodu" name="k_cep_tel2_kodu[]"
                                                               data-katilimciid="{{$row->id}}" value="{{$row->cep_tel2_kodu}}">
                                                    </span>
                                                    <input type="text" id="k_cep_tel2" name="k_cep_tel2[]" class="form-control" value="{{old('k_cep_tel2.'.$key, $row->cep_tel2)}}"
                                                        placeholder="Phone 2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">E - Complete Registration</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">How did you hear about us?</label>
                                    <div class="col-md-4">
                                        <select id="nereden_duydu_id" name="nereden_duydu_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach($nereden_duydu as $row)
                                                <option value="{{$row->id}}" {{old('nereden_duydu_id', $data->nereden_duydu_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                            @endforeach
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
        $("document").ready(function () {
            $(".select2").select2();
            telKoduDegistir();
        });
        function telKoduDegistir() {
            var tel_kodu = $("#sirket_ulke_id option:selected").data("telkod");
            $("#txt_ct_cep_kodu, #txt_ct_telefon_kodu, .k_cep_tel_kodu").text(tel_kodu);
            $("#ct_cep_kodu, #ct_telefon_kodu, input[id^='k_cep_tel_kodu']").val(tel_kodu);

            var data = {
                "_token" : "{{csrf_token()}}",
                'sirket_ulke_id' : $("#sirket_ulke_id").val()
            }
            var tmp_referans_id = "{{old('referans_id', $data->referans_id)}}";
            $.post("/pm_wait/refSirketGetirJson", data, function (cevap) {
                $("#referans_id option:gt(0)").remove();
                $("#referans_id option:first").prop('selected', true);
                $.each(cevap, function (i, row) {
                    $("#referans_id").append("<option value='" + row.id + "' " + ( tmp_referans_id == row.id ? ' selected' : '') + ">" + row.adi + "</option>");
                })
            }, "json").done(function () {
                $("#referans_id").trigger('change');
            });
        }
        function formuKaydet() {
            showLoading('', '');
        }

        function sirketadiGoster() {
            if($("#referans_id").val() > 0) {
                $(".company-name").addClass('hidden');
                $("#sirket_adi").val('');
            } else {
                $(".company-name").removeClass('hidden');
            }
        }
    </script>
@endsection
