@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> {{$baslik}}
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
                        @if($data["id"] > 0)
                            <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/{{$data['id']}}" enctype="multipart/form-data">
                                @method("put")
                        @else
                            <form class="form-horizontal" role="form" method="post" action="/{{$prefix}}" enctype="multipart/form-data">
                        @endif
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">A - Form Registration Details</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Form Filled Date</label>
                                    <div class="col-md-2"><p class="form-control-static">{{date('d.m.Y', strtotime($data->created_at))}}</p></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">IP of Form Filling Party</label>
                                    <div class="col-md-2"><p class="form-control-static">{{$data->kayit_ip}}</p></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Last Updated Date</label>
                                    <div class="col-md-2"><p class="form-control-static">{{date('d.m.Y', strtotime($data->updated_at))}}</p></div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">B - Change Password</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-2">
                                        <input type="password" id="sifre" name="sifre" class="form-control" value="{{old('sifre')}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Password (repeat)</label>
                                    <div class="col-sm-2">
                                        <input type="password" id="sifre_tekrar" name="sifre_tekrar" class="form-control" value="{{old('sifre_tekrar')}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">C - Personel Details</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Title</label>
                                    <div class="col-sm-2">
                                        <select id="unvan_id" name="unvan_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($unvanlar as $row)
                                                <option value="{{$row->id}}" {{old('unvan_id', $data->unvan_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Full Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="adi_soyadi" name="adi_soyadi" class="form-control" value="{{old('adi_soyadi', $data["adi_soyadi"])}}">
                                    </div>
                                </div>
                                @if(isset($egitmen_formu) && $data->kullanici_id > 0)
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Status</label>
                                        <div class="col-sm-2">
                                            <select id="flg_durum" name="flg_durum" class="form-control">
                                                <option value="1" {{old('flg_durum', $data->flg_durum) == "1" ? ' selected' : ''}}>Active</option>
                                                <option value="0" {{old('flg_durum', $data->flg_durum) == "0" ? ' selected' : ''}}>Passive</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                <div class="form-group">
                                    <div class="row">
                                        <label class="control-label col-sm-2">Picture</label>
                                        <div class="control-group col-sm-10">

                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                    @if($data['resim'] != "")
                                                        <img src="{{\Illuminate\Support\Facades\Storage::url($data['resim'])}}">
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="btn red btn-outline btn-file">
                                                        <span class="fileinput-new"> Select image </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="resim">
                                                    </span>
                                                    <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Gender</label>
                                    <div class="col-sm-2">
                                        <select id="cinsiyet" name="cinsiyet" class="form-control">
                                            <option value="1" {{old('cinsiyet', $data->cinsiyet) == 1 || old('cinsiyet', $data->cinsiyet) == '' ? ' selected' : ''}}>Male</option>
                                            <option value="2" {{old('cinsiyet', $data->cinsiyet) == 2 ? ' selected' : ''}}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Marital Status</label>
                                    <div class="col-sm-2">
                                        <select id="medeni_durum" name="medeni_durum" class="form-control">
                                            <option value="1" {{old('medeni_durum', $data->medeni_durum) == 1 || old('medeni_durum', $data->medeni_durum) == '' ? ' selected' : ''}}>Single</option>
                                            <option value="2" {{old('medeni_durum', $data->medeni_durum) == 2 ? ' selected' : ''}}>Married</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Birth Date</label>
                                    <div class="col-sm-2"><input type="text" id="dogum_tarihi" name="dogum_tarihi" class="form-control date-picker"
                                        value="{{old('dogum_tarihi', $data->dogum_tarihi) != '' ? date('d.m.Y', strtotime(old('dogum_tarihi', $data->dogum_tarihi))) : ''}}"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Institution/Company Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="sirket_adi" name="sirket_adi" class="form-control" value="{{old('sirket_adi', $data->sirket_adi)}}">
                                        <p class="help-block"> (Currently Working)</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Country of Residence</label>
                                    <div class="col-sm-3">
                                        <select id="yasadigi_ulke" name="yasadigi_ulke" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($ulkeler as $row)
                                                <option value="{{$row->id}}" {{old('yasadigi_ulke', $data->yasadigi_ulke) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Country of Origin</label>
                                    <div class="col-sm-3">
                                        <select id="dogum_ulke" name="dogum_ulke" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($ulkeler as $row)
                                                <option value="{{$row->id}}" {{old('dogum_ulke', $data->dogum_ulke) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                        <p class="help-block"> (Citizenship)</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">City of Living</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="yasadigi_sehir" name="yasadigi_sehir" class="form-control" value="{{old('yasadigi_sehir', $data->yasadigi_sehir)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Personal Email</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="sahsi_email" name="sahsi_email" class="form-control" value="{{old('sahsi_email', $data->sahsi_email)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Corporate E-mail</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="sirket_email" name="sirket_email" class="form-control" value="{{old('sirket_email', $data->sirket_email)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Mobile (GSM)</label>
                                    <div class="col-sm-1">
                                        <input type="text" id="cep_tel_kod" name="cep_tel_kod" class="form-control"
                                               placeholder="Code" value="{{old('cep_tel_kod', $data->cep_tel_kod)}}">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" id="cep_tel" name="cep_tel" class="form-control"
                                               placeholder="Phone Number" value="{{old('cep_tel', $data->cep_tel)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Additional Telephone</label>
                                    <div class="col-sm-1">
                                        <input type="text" id="tel_kod" name="tel_kod" class="form-control"
                                               placeholder="Code" value="{{old('tel_kod', $data->tel_kod)}}">
                                        <p class="help-block"> (optional)</p>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" id="tel" name="tel" class="form-control"
                                               placeholder="Phone Number" value="{{old('tel', $data->tel)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Personal Webpage</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="sahsi_web" name="sahsi_web" class="form-control"  value="{{old('sahsi_web', $data->sahsi_web)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">wHow did you hear about us?</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="nereden_duydu" name="nereden_duydu" class="form-control"  value="{{old('nereden_duydu', $data->nereden_duydu)}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">D - Documents Uploaded</div>
                                </div>
                                <div class="row">
                                    <label class="control-label col-sm-2">CV1 (FreeFormat)</label>
                                    <div class="col-sm-10">
                                        @if($data->cv_dosya != '')
                                            <div class="clearfix margin-top-5 margin-bottom-10" id="cv_dosya_container">
                                                <div><a href="{{Storage::url($data->cv_dosya)}}" target="_blank">Click to Download/View </a> <a href="javascript:;" class="btn btn-xs btn-danger" title="Delete CV file" onclick="cvDosyaSil('cv_dosya')"><i class="fa fa-trash"></i></a></div>
                                                <div class="{{$data->cv_dosya_tarih == '' ? ' hidden' : ''}}">Last Update: {{date('d.m.Y', strtotime($data->cv_dosya_tarih))}}</div>
                                            </div>
                                        @endif
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="cv_dosya"> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                        <div class="clearfix margin-top-5 margin-bottom-10 font-red">
                                            <span class="label label-danger">NOTE!</span> 1) Maximum file size is 2 MB<br>
                                            <span class="label label-danger">NOTE!</span> 2) Please upload Word format
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="control-label col-sm-2">CV2 (TemplateFormat)</label>
                                    <div class="col-sm-10">
                                        @if($data->cv_dosya2 != '')
                                            <div class="clearfix margin-top-5 margin-bottom-10" id="cv_dosya2_container">
                                                <div><a href="{{Storage::url($data->cv_dosya2)}}" target="_blank">Click to Download/View </a> <a href="javascript:;" class="btn btn-xs btn-danger" title="Delete CV file" onclick="cvDosyaSil('cv_dosya2')"><i class="fa fa-trash"></i></a></div>
                                                <div class="{{$data->cv_dosya2_tarih == '' ? ' hidden' : ''}}">Last Update: {{date('d.m.Y', strtotime($data->cv_dosya2_tarih))}}</div>
                                            </div>
                                        @endif
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="cv_dosya2"> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                        <div class="clearfix margin-top-5 margin-bottom-10 font-red">
                                            <span class="label label-danger">NOTE!</span> 1) Maximum file size is 2 MB<br>
                                            <span class="label label-danger">NOTE!</span> 2) Please upload Word format
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">TC No </label>
                                    <div class="col-sm-2">
                                        <input type="text" id="tc_kimlik" name="tc_kimlik" class="form-control"  value="{{old('tc_kimlik', $data->tc_kimlik)}}">
                                        <p class="help-block"> (Turkish Citizens)</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Passport No </label>
                                    <div class="col-sm-2">
                                        <input type="text" id="pasaport_no" name="pasaport_no" class="form-control"  value="{{old('pasaport_no', $data->pasaport_no)}}">
                                        <p class="help-block"> (Non-Turkish)</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="control-label col-sm-2">Passport Copy</label>
                                    <div class="col-sm-10">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="pasaport_resim"> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                        <div class="clearfix margin-top-5 margin-bottom-10 font-red">
                                            <span class="label label-danger">NOTE!</span> 1) Page having photo<br>
                                            <span class="label label-danger">NOTE!</span> 2) Picture and passport number should be clear
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Residence No</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="ikame_no" name="ikame_no" class="form-control"  value="{{old('ikame_no', $data->ikame_no)}}">
                                        <p class="help-block font-red"> (Ikame)</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="control-label col-sm-2">Residence <span class="font-red">(Ikame)</span> copy</label>
                                    <div class="col-sm-10">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="ikame_resim"> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">E - Bank Details</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Account Holder Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="banka_hesap_adi" name="banka_hesap_adi" class="form-control"  value="{{old('banka_hesap_adi', $data->banka_hesap_adi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Bank Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="banka_adi" name="banka_adi" class="form-control"  value="{{old('banka_adi', $data->banka_adi)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Bank Branch</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="banka_sube" name="banka_sube" class="form-control"  value="{{old('banka_sube', $data->banka_sube)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Account No</label>
                                    <div class="col-sm-2">
                                        <input type="text" id="banka_hesap_no" name="banka_hesap_no" class="form-control"  value="{{old('banka_hesap_no', $data->banka_hesap_no)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">IBAN</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="banka_iban" name="banka_iban" class="form-control"  value="{{old('banka_iban', $data->banka_iban)}}">
                                        <p class="help-block font-red"> (TL Account)</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">F - Spoken Languages and Level of Competence</div>
                                </div>
                                @for($dil_i = 0; $dil_i < 4; $dil_i++)
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Language {{$dil_i + 1}}</label>
                                        <div class="col-sm-2">
                                            <select id="diller" name="diller[]" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($diller as $row)
                                                    <option value="{{$row->id}}" {{old('diller.'.$dil_i, (isset($egitmen_diller[$dil_i]) ? $egitmen_diller[$dil_i]->dil_id : '' ) ) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-1">
                                            <select id="derece" name="derece[]" class="form-control">
                                                <option value="">Select</option>
                                                <option value="Native" {{old('derece.'.$dil_i, (isset($egitmen_diller[$dil_i]) ? $egitmen_diller[$dil_i]->derece : '' ) ) == "Native" ? ' selected' : '' }}>Native</option>
                                                <option value="V. Good" {{old('derece.'.$dil_i, (isset($egitmen_diller[$dil_i]) ? $egitmen_diller[$dil_i]->derece : '' ) ) == "V. Good" ? ' selected' : '' }}>V. Good</option>
                                                <option value="Good" {{old('derece.'.$dil_i, (isset($egitmen_diller[$dil_i]) ? $egitmen_diller[$dil_i]->derece : '' ) ) == "Good" ? ' selected' : '' }}>Good</option>
                                                <option value="Average" {{old('derece.'.$dil_i, (isset($egitmen_diller[$dil_i]) ? $egitmen_diller[$dil_i]->derece : '' ) ) == "Average" ? ' selected' : '' }}>Average</option>
                                                <option value="Weak" {{old('derece.'.$dil_i, (isset($egitmen_diller[$dil_i]) ? $egitmen_diller[$dil_i]->derece : '' ) ) == "Weak" ? ' selected' : '' }}>Weak</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endfor

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">G - Educational Background</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <div class="table-scrollable">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th scope="col"> <a href="javascript:;" class="btn btn-xs btn-primary" onclick="yeniHocaEgitim()"><i class="fa fa-plus"></i></a> </th>
                                                    <th scope="col"> Degree </th>
                                                    <th scope="col"> Graduation Date </th>
                                                    <th scope="col"> Field/Specialization </th>
                                                    <th scope="col"> Institution </th>
                                                    <th scope="col"> City </th>
                                                    <th scope="col"> Country </th>
                                                </tr>
                                                </thead>
                                                <tbody id="hoca_egitim_container">
                                                @php($key = -1)
                                                @foreach($data->egitimAldigiOkullar as $key => $row)
                                                    <tr id="eo_satir" data-key="{{$key}}">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="hocaEgitimSil('{{$key}}', '{{$row->id}}')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_eo_id" name="hid_eo_id[]" data-key="{{$key}}" value="{{$row->id}}">
                                                        </td>
                                                        <td>
                                                            <select id="eo_derece" name="eo_derece[]" data-key="{{$key}}" class="form-control">
                                                                <option value="BSc/BA" {{old('eo_derece.'.$key, $row->derece) == 'BSc/BA' ? ' selected' : ''}}>BSc/BA</option>
                                                                <option value="MSc/MBA" {{old('eo_derece.'.$key, $row->derece) == 'MSc/MBA' ? ' selected' : ''}}>MSc/MBA</option>
                                                                <option value="PhD" {{old('eo_derece.'.$key, $row->derece) == 'PhD' ? ' selected' : ''}}>PhD</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="eo_mezun_tarih" name="eo_mezun_tarih[]" data-key="{{$key}}" class="form-control">
                                                                @for($yil = date('Y'); $yil >= 2000; $yil--)
                                                                    <option value="{{$yil}}" {{old('eo_mezun_tarih.'.$key, date('Y', strtotime($row->mezun_tarih))) == $yil ? ' selected' : ''}}>{{$yil}}</option>
                                                                    @endfor
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" id="eo_uzmanlik" name="eo_uzmanlik[]" data-key="{{$key}}" value="{{old('eo_uzmanlik.'.$key, $row->uzmanlik)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="eo_okul" name="eo_okul[]" data-key="{{$key}}" value="{{old('eo_okul.'.$key, $row->okul)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="eo_sehir" name="eo_sehir[]" data-key="{{$key}}" value="{{old('eo_sehir.'.$key, $row->sehir)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="eo_ulke_id" name="eo_ulke_id[]" data-key="{{$key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('eo_ulke_id.'.$key, $row->ulke_id) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                    @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @for($tmp_key = $key + 1; $tmp_key < 10; $tmp_key++)
                                                    <tr id="eo_satir" data-key="{{$tmp_key}}" class="hidden">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="hocaEgitimSil('{{$tmp_key}}', '')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_eo_id" name="hid_eo_id[]" data-key="{{$tmp_key}}" value="">
                                                        </td>
                                                        <td>
                                                            <select id="eo_derece" name="eo_derece[]" data-key="{{$tmp_key}}" class="form-control">
                                                                <option value="BSc/BA" {{old('eo_derece.'.$tmp_key) == 'BSc/BA' ? ' selected' : ''}}>BSc/BA</option>
                                                                <option value="MSc/MBA" {{old('eo_derece.'.$tmp_key) == 'MSc/MBA' ? ' selected' : ''}}>MSc/MBA</option>
                                                                <option value="PhD" {{old('eo_derece.'.$tmp_key) == 'PhD' ? ' selected' : ''}}>PhD</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="eo_mezun_tarih" name="eo_mezun_tarih[]" data-key="{{$tmp_key}}" class="form-control">
                                                                @for($yil = date('Y'); $yil >= 2000; $yil--)
                                                                    <option value="{{$yil}}" {{old('eo_mezun_tarih.'.$tmp_key) == $yil ? ' selected' : ''}}>{{$yil}}</option>
                                                                @endfor
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" id="eo_uzmanlik" name="eo_uzmanlik[]" data-key="{{$tmp_key}}" value="{{old('eo_uzmanlik.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="eo_okul" name="eo_okul[]" data-key="{{$tmp_key}}" value="{{old('eo_okul.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="eo_sehir" name="eo_sehir[]" data-key="{{$tmp_key}}" value="{{old('eo_sehir.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="eo_ulke_id" name="eo_ulke_id[]" data-key="{{$tmp_key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('eo_ulke_id.'.$tmp_key) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-3 font-red">H - Employment History</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <div class="table-scrollable">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th scope="col"> <a href="javascript:;" class="btn btn-xs btn-primary" onclick="yeniCalistigiYer()"><i class="fa fa-plus"></i></a> </th>
                                                    <th scope="col"> Period (dates) </th>
                                                    <th scope="col"> Department </th>
                                                    <th scope="col"> Position </th>
                                                    <th scope="col"> Institution </th>
                                                    <th scope="col"> City </th>
                                                    <th scope="col"> Country </th>
                                                </tr>
                                                </thead>
                                                <tbody id="hoca_egitim_container">
                                                @php($key = -1)
                                                @foreach($data->calistigiIsler as $key => $row)
                                                    <tr id="ei_satir" data-key="{{$key}}">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="calistigiYerSil('{{$key}}', '{{$row->id}}')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_ei_id" name="hid_ei_id[]" data-key="{{$key}}" value="{{$row->id}}">
                                                        </td>
                                                        <td>
                                                            <p class="help-block">Start Date</p>
                                                            <input type="text" id="ei_baslama_tarihi" name="ei_baslama_tarihi[]" data-key="{{$key}}"
                                                                   value="{{old('ei_baslama_tarihi.'.$key, $row->baslama_tarihi) != '' ? date('d.m.Y', strtotime(old('ei_baslama_tarihi.'.$key, $row->baslama_tarihi))) : ''}}"
                                                                   class="form-control date-picker">
                                                            <p class="help-block">Until (End Date)</p>
                                                            <input type="text" id="ei_bitis_tarihi" name="ei_bitis_tarihi[]" data-key="{{$key}}"
                                                                   value="{{old('ei_bitis_tarihi.'.$key, $row->bitis_tarihi) != '' ? date('d.m.Y', strtotime(old('ei_bitis_tarihi.'.$key, $row->bitis_tarihi))) : ''}}"
                                                                   class="form-control date-picker">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_departman" name="ei_departman[]" data-key="{{$key}}" value="{{old('ei_departman.'.$key, $row->departman)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_pozisyon" name="ei_pozisyon[]" data-key="{{$key}}" value="{{old('ei_pozisyon.'.$key, $row->pozisyon)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_sirket_adi" name="ei_sirket_adi[]" data-key="{{$key}}" value="{{old('ei_sirket_adi.'.$key, $row->sirket_adi)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_sehir" name="ei_sehir[]" data-key="{{$key}}" value="{{old('ei_sehir.'.$key, $row->sehir)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="ei_ulke_id" name="ei_ulke_id[]" data-key="{{$key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('ei_ulke_id.'.$key, $row->ulke_id) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @for($tmp_key = $key + 1; $tmp_key < 10; $tmp_key++)
                                                    <tr id="ei_satir" data-key="{{$tmp_key}}" class="hidden">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="calistigiYerSil('{{$tmp_key}}', '')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_ei_id" name="hid_ei_id[]" data-key="{{$tmp_key}}" value="">
                                                        </td>
                                                        <td>
                                                            <p class="help-block">Start Date</p>
                                                            <input type="text" id="ei_baslama_tarihi" name="ei_baslama_tarihi[]" data-key="{{$tmp_key}}"
                                                                   value="{{old('ei_baslama_tarihi.'.$tmp_key) != '' ? date('d.m.Y', strtotime(old('ei_baslama_tarihi.'.$tmp_key))) : ''}}"
                                                                   class="form-control date-picker">
                                                            <p class="help-block">Until (End Date)</p>
                                                            <input type="text" id="ei_bitis_tarihi" name="ei_bitis_tarihi[]" data-key="{{$tmp_key}}"
                                                                   value="{{old('ei_bitis_tarihi.'.$tmp_key) != '' ? date('d.m.Y', strtotime(old('ei_bitis_tarihi.'.$tmp_key))) : ''}}"
                                                                   class="form-control date-picker">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_departman" name="ei_departman[]" data-key="{{$tmp_key}}" value="{{old('ei_departman.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_pozisyon" name="ei_pozisyon[]" data-key="{{$tmp_key}}" value="{{old('ei_pozisyon.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_sirket_adi" name="ei_sirket_adi[]" data-key="{{$tmp_key}}" value="{{old('ei_sirket_adi.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ei_sehir" name="ei_sehir[]" data-key="{{$tmp_key}}" value="{{old('ei_sehir.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="ei_ulke_id" name="ei_ulke_id[]" data-key="{{$tmp_key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('ei_ulke_id.'.$tmp_key) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4 font-red">I - Professional Course/Certificates Training Programmes Attended</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <div class="table-scrollable">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th scope="col"> <a href="javascript:;" class="btn btn-xs btn-primary" onclick="yeniAldigiKurs()"><i class="fa fa-plus"></i></a> </th>
                                                    <th scope="col"> Period (dates) </th>
                                                    <th scope="col"> Course/Certificate Title </th>
                                                    <th scope="col"> Institution </th>
                                                    <th scope="col"> City </th>
                                                    <th scope="col"> Country </th>
                                                </tr>
                                                </thead>
                                                <tbody id="aldigi_kurs_container">
                                                @php($key = -1)
                                                @foreach($data->aldigiKurslar as $key => $row)
                                                    <tr id="ek_satir" data-key="{{$key}}">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="aldigiKursSil('{{$key}}', '{{$row->id}}')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_ek_id" name="hid_ek_id[]" data-key="{{$key}}" value="{{$row->id}}">
                                                        </td>
                                                        <td>
                                                            <p class="help-block">Start Date</p>
                                                            <input type="text" id="ek_baslama_tarihi" name="ek_baslama_tarihi[]" data-key="{{$key}}"
                                                                   value="{{old('ek_baslama_tarihi.'.$key, $row->baslama_tarihi) != '' ? date('d.m.Y', strtotime(old('ek_baslama_tarihi.'.$key, $row->baslama_tarihi))) : ''}}"
                                                                   class="form-control date-picker">
                                                            <p class="help-block">Until (End Date)</p>
                                                            <input type="text" id="ek_bitis_tarihi" name="ek_bitis_tarihi[]" data-key="{{$key}}"
                                                                   value="{{old('ek_bitis_tarihi.'.$key, $row->bitis_tarihi) != '' ? date('d.m.Y', strtotime(old('ek_bitis_tarihi.'.$key, $row->bitis_tarihi))) : ''}}"
                                                                   class="form-control date-picker">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ek_kurs_adi" name="ek_kurs_adi[]" data-key="{{$key}}" value="{{old('ek_kurs_adi.'.$key, $row->kurs_adi)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ek_kurum" name="ek_kurum[]" data-key="{{$key}}" value="{{old('ek_kurum.'.$key, $row->kurum)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ek_sehir" name="ek_sehir[]" data-key="{{$key}}" value="{{old('ek_sehir.'.$key, $row->sehir)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="ek_ulke_id" name="ek_ulke_id[]" data-key="{{$key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('ek_ulke_id.'.$key, $row->ulke_id) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @for($tmp_key = $key + 1; $tmp_key < 10; $tmp_key++)
                                                    <tr id="ek_satir" data-key="{{$tmp_key}}" class="hidden">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="aldigiKursSil('{{$tmp_key}}', '')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_ek_id" name="hid_ek_id[]" data-key="{{$tmp_key}}" value="">
                                                        </td>
                                                        <td>
                                                            <p class="help-block">Start Date</p>
                                                            <input type="text" id="ek_baslama_tarihi" name="ek_baslama_tarihi[]" data-key="{{$tmp_key}}"
                                                                   value="{{old('ek_baslama_tarihi.'.$tmp_key) != '' ? date('d.m.Y', strtotime(old('ek_baslama_tarihi.'.$tmp_key))) : ''}}"
                                                                   class="form-control date-picker">
                                                            <p class="help-block">Until (End Date)</p>
                                                            <input type="text" id="ek_bitis_tarihi" name="ek_bitis_tarihi[]" data-key="{{$tmp_key}}"
                                                                   value="{{old('ek_bitis_tarihi.'.$tmp_key) != '' ? date('d.m.Y', strtotime(old('ek_bitis_tarihi.'.$tmp_key))) : ''}}"
                                                                   class="form-control date-picker">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ek_kurs_adi" name="ek_kurs_adi[]" data-key="{{$tmp_key}}" value="{{old('ek_kurs_adi.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ek_kurum" name="ek_kurum[]" data-key="{{$tmp_key}}" value="{{old('ek_kurum.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ek_sehir" name="ek_sehir[]" data-key="{{$tmp_key}}" value="{{old('ek_sehir.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="ek_ulke_id" name="ek_ulke_id[]" data-key="{{$tmp_key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('ek_ulke_id.'.$tmp_key) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4 font-red">J - Professional Training (Certificate Courses) Delivered</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <div class="table-scrollable">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th scope="col"> <a href="javascript:;" class="btn btn-xs btn-primary" onclick="yeniAldigiEgitim()"><i class="fa fa-plus"></i></a> </th>
                                                    <th scope="col"> Period (dates) </th>
                                                    <th scope="col"> Course/Certificate Title </th>
                                                    <th scope="col"> Institution </th>
                                                    <th scope="col"> City </th>
                                                    <th scope="col"> Country </th>
                                                </tr>
                                                </thead>
                                                <tbody id="aldigi_egitim_container">
                                                @php($key = -1)
                                                @foreach($data->aldigiEgitimler as $key => $row)
                                                    <tr id="ee_satir" data-key="{{$key}}">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="aldigiEgitimSil('{{$key}}', '{{$row->id}}')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_ee_id" name="hid_ee_id[]" data-key="{{$key}}" value="{{$row->id}}">
                                                        </td>
                                                        <td>
                                                            <p class="help-block">Start Date</p>
                                                            <input type="text" id="ee_baslama_tarihi" name="ee_baslama_tarihi[]" data-key="{{$key}}"
                                                                   value="{{old('ee_baslama_tarihi.'.$key, $row->baslama_tarihi) != '' ? date('d.m.Y', strtotime(old('ee_baslama_tarihi.'.$key, $row->baslama_tarihi))) : ''}}"
                                                                   class="form-control date-picker">
                                                            <p class="help-block">Until (End Date)</p>
                                                            <input type="text" id="ee_bitis_tarihi" name="ee_bitis_tarihi[]" data-key="{{$key}}"
                                                                   value="{{old('ee_bitis_tarihi.'.$key, $row->bitis_tarihi) != '' ? date('d.m.Y', strtotime(old('ee_bitis_tarihi.'.$key, $row->bitis_tarihi))) : ''}}"
                                                                   class="form-control date-picker">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ee_kurs_adi" name="ee_kurs_adi[]" data-key="{{$key}}" value="{{old('ee_kurs_adi.'.$key, $row->kurs_adi)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ee_kurum" name="ee_kurum[]" data-key="{{$key}}" value="{{old('ee_kurum.'.$key, $row->kurum)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ee_sehir" name="ee_sehir[]" data-key="{{$key}}" value="{{old('ee_sehir.'.$key, $row->sehir)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="ee_ulke_id" name="ee_ulke_id[]" data-key="{{$key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('ee_ulke_id.'.$key, $row->ulke_id) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @for($tmp_key = $key + 1; $tmp_key < 10; $tmp_key++)
                                                    <tr id="ee_satir" data-key="{{$tmp_key}}" class="hidden">
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="aldigiEgitimSil('{{$tmp_key}}', '')"><i class="fa fa-trash"></i></a>
                                                            <input type="hidden" id="hid_ee_id" name="hid_ee_id[]" data-key="{{$tmp_key}}" value="">
                                                        </td>
                                                        <td>
                                                            <p class="help-block">Start Date</p>
                                                            <input type="text" id="ee_baslama_tarihi" name="ee_baslama_tarihi[]" data-key="{{$tmp_key}}"
                                                                   value="{{old('ee_baslama_tarihi.'.$tmp_key) != '' ? date('d.m.Y', strtotime(old('ee_baslama_tarihi.'.$tmp_key))) : ''}}"
                                                                   class="form-control date-picker">
                                                            <p class="help-block">Until (End Date)</p>
                                                            <input type="text" id="ee_bitis_tarihi" name="ee_bitis_tarihi[]" data-key="{{$tmp_key}}"
                                                                   value="{{old('ee_bitis_tarihi.'.$tmp_key) != '' ? date('d.m.Y', strtotime(old('ee_bitis_tarihi.'.$tmp_key))) : ''}}"
                                                                   class="form-control date-picker">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ee_kurs_adi" name="ee_kurs_adi[]" data-key="{{$tmp_key}}" value="{{old('ee_kurs_adi.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ee_kurum" name="ee_kurum[]" data-key="{{$tmp_key}}" value="{{old('ee_kurum.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" id="ee_sehir" name="ee_sehir[]" data-key="{{$tmp_key}}" value="{{old('ee_sehir.'.$tmp_key)}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <select id="ee_ulke_id" name="ee_ulke_id[]" data-key="{{$tmp_key}}" class="form-control select2">
                                                                <option value="">Select</option>
                                                                @foreach($ulkeler as $ulke_row)
                                                                    <option value="{{$ulke_row->id}}" {{old('ee_ulke_id.'.$tmp_key) == $ulke_row->id ? ' selected' : ''}}>{{$ulke_row->adi}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4 font-red">K - Course Categories and Titles Interested to Deliver, Please Select!</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-8">
                                        <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]">
                                            @foreach($egitim_kategorileri as $key => $row)
                                                <option value="{{$row->id}}" {{in_array($row->id, old('my_multi_select1', $sectigi_kategoriler)) ? ' selected' : ''}}>{{$row->kodu." ".$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4 font-red">Courses in Selected Category</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-8">
                                        @php($tmp_kategori_id = "")
                                        <select multiple="multiple" class="multi-select" id="my_multi_select2" name="my_multi_select2[]">
                                            @foreach($egitimler_listesi as $key => $row)
                                                @if($tmp_kategori_id != $row->kategori_id)
                                                    @if($key > 0)
                                                        </optgroup>
                                                        @endif
                                                    <optgroup label="{{$row->kategori_adi}}">
                                                    @endif
                                                <option value="{{$row->id}}" {{in_array($row->id, old('my_multi_select1', $sectigi_egitimler)) ? ' selected' : ''}}>{{$row->kodu." ".$row->adi}}</option>
                                                @php($tmp_kategori_id = $row->kategori_id)
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4 font-red">L - Additional information (you may provide the following)</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Additional course title you would like to suggest and deliver</label>
                                    <div class="col-sm-6">
                                        <textarea id="course_additional" name="course_additional" rows="3" class="form-control">{{old('course_additional', $data->course_additional)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">List of papers</label>
                                    <div class="col-sm-6">
                                        <textarea id="papers" name="papers" rows="3" class="form-control">{{old('papers', $data->papers)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Sofware programmes being used/familiar</label>
                                    <div class="col-sm-6">
                                        <textarea id="software" name="software" rows="3" class="form-control">{{old('software', $data->software)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">References</label>
                                    <div class="col-sm-6">
                                        <textarea id="referanslar" name="referanslar" rows="3" class="form-control">{{old('referanslar', $data->referanslar)}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Any other information you may think relevant</label>
                                    <div class="col-sm-6">
                                        <textarea id="other_info" name="other_info" rows="3" class="form-control">{{old('other_info', $data->other_info)}}</textarea>
                                    </div>
                                </div>

                                @if(isset($egitmen_formu))
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label font-red">Checked ?</label>
                                        <div class="col-sm-3">
                                            <select id="durum" name="durum" class="form-control">
                                                <option value="1" {{old('durum', $data->durum) == "1" ? ' selected' : ''}}>Checking Later, keep here!</option>
                                                <option value="3" {{old('durum', $data->durum) == "3" ? ' selected' : ''}}>Accept</option>
                                                <option value="2" {{old('durum', $data->durum) == "2" ? ' selected' : ''}}>Reject</option>
                                                <option value="-1" {{old('durum', $data->durum) == "-1" ? ' selected' : ''}}>Deleted-Move to Delete Tab</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif

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
    <link href="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('/assets/global/plugins/jquery-multi-select/css/multi-select.css')}}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .ms-container {
            width: 100% !important;
        }
    </style>
@endsection
@section("js")
    <script src="{{url('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/global/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
            $('#my_multi_select1, #my_multi_select2').multiSelect();
        });
        function formuKaydet() {
            showLoading('', '');
        }

        function cvDosyaSil(alan_adi) {
            bootbox.confirm("File will be deleted. Are you sure?", function(result) {
                if(result) {
                    showLoading('', '');
                    var data = {
                        "_token" : "{{csrf_token()}}",
                        "alan" : alan_adi,
                        "id" : "{{$data->id}}"
                    }
                    $.post('/cv_view/cvDosyaSil', data, function (cevap) {
                        if(cevap.cvp == '0') {
                            toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                        } else {
                            $("#" + alan_adi + "_container").remove();
                        }
                    }, "json").done(function () {
                        hideLoading();
                    });
                }
            });
        }

        function hocaEgitimSil(key, id) {
            bootbox.confirm("Do you want to delete record?", function(result) {
                if (result) {
                    var data = {
                        "_token": "{{csrf_token()}}",
                        "key" : key,
                        "id" : id
                    }
                    $.post('/cv_view/he_sil', data, function (cevap) {
                        if(cevap.cvp == 1) {
                            $("#hid_eo_id[data-key='" + key + "']").val("");
                            $("#eo_derece[data-key='" + key + "'] option:first").prop("selected", true);
                            $("#eo_mezun_tarih[data-key='" + key + "'] option:first").prop("selected", true);
                            $("#eo_uzmanlik[data-key='" + key + "']").val("");
                            $("#eo_okul[data-key='" + key + "']").val("");
                            $("#eo_sehir[data-key='" + key + "']").val("");
                            $("#eo_ulke_id[data-key='" + key + "'] option:first").prop("selected", true);
                            $("#eo_ulke_id[data-key='" + key + "']").trigger('change');

                            $("#eo_satir[data-key='" + key + "']").addClass('hidden');
                        } else {
                            toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                        }
                    }, "json");
                }
            });
        }

        function yeniHocaEgitim() {
            if(typeof($("#eo_satir").not(".hidden").data("key")) == "undefined") {
                satir = 0;
            } else {
                satir = parseInt($("tr[id^='eo_satir']:not(.hidden)").last().data('key')) + 1;
            }
            $("#eo_satir[data-key='" + satir + "']").removeClass('hidden');
        }

        function yeniCalistigiYer() {
            if(typeof($("#ei_satir").not(".hidden").data("key")) == "undefined") {
                satir = 0;
            } else {
                satir = parseInt($("tr[id^='ei_satir']:not(.hidden)").last().data('key')) + 1;
            }
            $("#ei_satir[data-key='" + satir + "']").removeClass('hidden');
        }

        function calistigiYerSil(key, id) {
            bootbox.confirm("Do you want to delete record?", function(result) {
                if (result) {
                    var data = {
                        "_token": "{{csrf_token()}}",
                        "key" : key,
                        "id" : id
                    }
                    $.post('/cv_view/hcy_sil', data, function (cevap) {
                        if(cevap.cvp == 1) {
                            $("#hid_ei_id[data-key='" + key + "']").val("");
                            $("#ei_baslama_tarihi[data-key='" + key + "']").val("");
                            $("#ei_bitis_tarihi[data-key='" + key + "']").val("");
                            $("#ei_departman[data-key='" + key + "']").val("");
                            $("#ei_pozisyon[data-key='" + key + "']").val("");
                            $("#ei_sirket_adi[data-key='" + key + "']").val("");
                            $("#ei_sehir[data-key='" + key + "']").val("");
                            $("#ei_ulke_id[data-key='" + key + "'] option:first").prop("selected", true);
                            $("#ei_ulke_id[data-key='" + key + "']").trigger('change');

                            $("#ei_satir[data-key='" + key + "']").addClass('hidden');
                        } else {
                            toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                        }
                    }, "json");
                }
            });
        }

        function yeniAldigiKurs() {
            if(typeof($("#ek_satir").not(".hidden").data("key")) == "undefined") {
                satir = 0;
            } else {
                satir = parseInt($("tr[id^='ek_satir']:not(.hidden)").last().data('key')) + 1;
            }
            $("#ek_satir[data-key='" + satir + "']").removeClass('hidden');
        }

        function aldigiKursSil(key, id) {
            bootbox.confirm("Do you want to delete record?", function(result) {
                if (result) {
                    var data = {
                        "_token": "{{csrf_token()}}",
                        "key" : key,
                        "id" : id
                    }
                    $.post('/cv_view/hak_sil', data, function (cevap) {
                        if(cevap.cvp == 1) {
                            $("#hid_ek_id[data-key='" + key + "']").val("");
                            $("#ek_baslama_tarihi[data-key='" + key + "']").val("");
                            $("#ek_bitis_tarihi[data-key='" + key + "']").val("");
                            $("#ek_kurs_adi[data-key='" + key + "']").val("");
                            $("#ek_kurum[data-key='" + key + "']").val("");
                            $("#ek_sehir[data-key='" + key + "']").val("");
                            $("#ek_ulke_id[data-key='" + key + "'] option:first").prop("selected", true);
                            $("#ek_ulke_id[data-key='" + key + "']").trigger('change');

                            $("#ek_satir[data-key='" + key + "']").addClass('hidden');
                        } else {
                            toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                        }
                    }, "json");
                }
            });
        }

        function yeniAldigiEgitim() {
            if(typeof($("#ee_satir").not(".hidden").data("key")) == "undefined") {
                satir = 0;
            } else {
                satir = parseInt($("tr[id^='ee_satir']:not(.hidden)").last().data('key')) + 1;
            }
            $("#ee_satir[data-key='" + satir + "']").removeClass('hidden');
        }

        function aldigiEgitimSil(key, id) {
            bootbox.confirm("Do you want to delete record?", function(result) {
                if (result) {
                    var data = {
                        "_token": "{{csrf_token()}}",
                        "key" : key,
                        "id" : id
                    }
                    $.post('/cv_view/hak_sil', data, function (cevap) {
                        if(cevap.cvp == 1) {
                            $("#hid_ee_id[data-key='" + key + "']").val("");
                            $("#ee_baslama_tarihi[data-key='" + key + "']").val("");
                            $("#ee_bitis_tarihi[data-key='" + key + "']").val("");
                            $("#ee_kurs_adi[data-key='" + key + "']").val("");
                            $("#ee_kurum[data-key='" + key + "']").val("");
                            $("#ee_sehir[data-key='" + key + "']").val("");
                            $("#ee_ulke_id[data-key='" + key + "'] option:first").prop("selected", true);
                            $("#ee_ulke_id[data-key='" + key + "']").trigger('change');

                            $("#ee_satir[data-key='" + key + "']").addClass('hidden');
                        } else {
                            toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                        }
                    }, "json");
                }
            });
        }
    </script>
@endsection
