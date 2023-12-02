@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Email Group Records
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
                                    <label class="col-sm-2 control-label">Group</label>
                                    <div class="col-sm-8">
                                        <select id="grup_id" name="grup_id" class="select2 form-control">
                                            <option value="">Select</option>
                                            @foreach($gruplar as $row)
                                                <option value="{{$row->id}}" {{old('grup_id', $data->grup_id) == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name Surname</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="adi_soyadi" name="adi_soyadi" class="form-control" value="{{old('adi_soyadi', $data["adi_soyadi"])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Departmen</label>
                                    <div class="col-md-6">
                                        <input type="text" id="departman" name="departman" class="form-control" value="{{old('departman', $data->departman)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Country</label>
                                    <div class="col-md-6">
                                        <select id="ulke_id" name="ulke_id" class="select2 form-control" onchange="sirketListesiGetir()">
                                            <option value="">Select</option>
                                            @foreach($ulkeler as $row)
                                                <option value="{{$row->id}}" {{old('ulke_id', $data->ulke_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">City</label>
                                    <div class="col-md-6">
                                        <input type="text" id="sehir" name="sehir" class="form-control" value="{{old('sehir', $data->sehir)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Company</label>
                                    <div class="col-md-10">
                                        <select id="referans_sirket_id" name="referans_sirket_id" class="select2 form-control" onchange="sirketadiGetir()">
                                            <option value="">Select</option>
                                        </select>
                                        <input type="text" id="sirket_adi" name="sirket_adi" value="{{old('sirket_adi', $data->sirket_adi)}}" class="form-control hidden"
                                            placeholder="Company Name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Phone</label>
                                    <div class="col-md-4">
                                        <input type="text" id="telefon" name="telefon" class="form-control" value="{{old('telefon', $data->telefon)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Mobile(GSM)</label>
                                    <div class="col-md-4">
                                        <input type="text" id="cep" name="cep" class="form-control" value="{{old('cep', $data->cep)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email(Private)</label>
                                    <div class="col-md-6">
                                        <input type="text" id="email" name="email" class="form-control" value="{{old('email', $data->email)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Email(Corparate)</label>
                                    <div class="col-md-6">
                                        <input type="text" id="email2" name="email2" class="form-control" value="{{old('email2', $data->email2)}}">
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-2 control-label">Additional Notes</label>
                                    <div class="col-md-6">
                                        <textarea id="notlar" name="notlar" class="form-control" rows="2">{{old('notlar', $data->notlar)}}</textarea>
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

            sirketListesiGetir();
        });
        function formuKaydet() {
            showLoading('', '');
        }

        function sirketListesiGetir() {
            var data = {
                "_token" : "{{csrf_token()}}",
                "ulke_id" : $("#ulke_id").val()
            };
            var tmp_flg_notinlist;
            var tmp_referans_sirket_id = "{{old('referans_sirket_id', $data->referans_sirket_id)}}";
            if("{{$data->id}}" != '' && "{{$data->referans_sirket_id}}" == "" && "{{$data->sirket_adi}}" != "")
                tmp_referans_sirket_id = -1;

            $.post("/em_grouplist/refSirketListeJson", data, function (cevap) {
                $("#referans_sirket_id option:first").prop('selected', true);
                $("#referans_sirket_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    if(i > 0 && tmp_flg_notinlist != row.flg_notinlist) {
                        $("#referans_sirket_id").append("<option value=''>------------------------------</option>");
                    }
                    $("#referans_sirket_id").append("<option value='" + row.id + "' " + ( tmp_referans_sirket_id == row.id ? ' selected' : '') + ">" + row.adi + "</option>");
                    tmp_flg_notinlist = row.flg_notinlist;
                });
                $("#referans_sirket_id").append("<option value='-1'" + ( tmp_referans_sirket_id == -1 ? ' selected' : '') + ">Company not in List</option>");
            }, "json").done(function () {
                $("#referans_sirket_id").trigger('change');
            });
        }

        function sirketadiGetir() {
            if($("#referans_sirket_id").val() == -1) {
                $("#sirket_adi").removeClass('hidden');
            } else {
                $("#sirket_adi").addClass('hidden');
            }
        }
    </script>
@endsection
