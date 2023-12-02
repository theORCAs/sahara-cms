@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Participant Module
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
                    <div class="portlet-body form-vertical">
                        <form role="form" id="kayitForm" method="post" action="/{{$prefix}}/update/{{$crf_id}}" enctype="multipart/form-data">
                            @method("put")
                            @csrf
                            <div class="portlet box blue">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-users"></i>Participant List </div>
                                    <div class="tools hidden">
                                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                                        <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                                        <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <a href="/{{$prefix}}/store/{{$crf_id}}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a>
                                                </th>
                                                <th> Name </th>
                                                <th> Job Title </th>
                                                <th> Email </th>
                                                <th> Phone </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($liste as $key => $row)
                                            <tr>
                                                <td> {{$key + 1}}
                                                    <input type="hidden" name="hid_katilimci_id[]" value="{{$row->id}}">
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi_soyadi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}/delete')"><i class="fa fa-trash"></i></a>
                                                </td>
                                                <td>
                                                    <div class="col-xs-4">
                                                        <select id="unvan_id" name="unvan_id[]" class="form-control select2">
                                                            <option value="">Select</option>
                                                            @foreach($unvanlar as $unvan_row)
                                                                <option value="{{$unvan_row->id}}" {{old('unvan_id.'.$key, $row->unvan_id) == $unvan_row->id ? ' selected' : ''}}>{{$unvan_row->adi}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-8">
                                                        <input type="text" name="adi_soyadi[]" class="form-control" value="{{old('adi_soyadi.'.$key, $row->adi_soyadi)}}">
                                                    </div>
                                                </td>
                                                <td> <input type="text" name="is_pozisyonu[]" class="form-control" value="{{old('is_pozisyonu.'.$key, $row->is_pozisyonu)}}"> </td>
                                                <td>
                                                    <input type="text" name="email[]" class="form-control" value="{{old('email.'.$key, $row->email)}}" placeholder="Email1">
                                                    <input type="text" name="email2[]" class="form-control" value="{{old('email2.'.$key, $row->email2)}}" placeholder="Email2">
                                                </td>
                                                <td>
                                                    <div class="col-xs-4">
                                                        <input type="text" name="cep_tel_kodu[]" class="form-control" value="{{old('cep_tel_kodu.'.$key, $row->cep_tel_kodu)}}" placeholder="Code">
                                                    </div>
                                                    <div class="col-xs-8">
                                                        <input type="text" name="cep_tel[]" class="form-control" value="{{old('cep_tel.'.$key, $row->cep_tel)}}" placeholder="Phone">
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <input type="text" name="cep_tel2_kodu[]" class="form-control" value="{{old('cep_tel2_kodu.'.$key, $row->cep_tel2_kodu)}}" placeholder="Code2">
                                                    </div>
                                                    <div class="col-xs-8">
                                                        <input type="text" name="cep_tel2[]" class="form-control" value="{{old('cep_tel2.'.$key, $row->cep_tel2)}}" placeholder="Phone2">
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" onclick="formuKaydet()">Submit</button>
                                        <a href="javascript:;" class="btn default" onclick="participantKapat()">Cancel</a>
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
    function participantKapat() {
        window.close();
    }
</script>
@endsection
