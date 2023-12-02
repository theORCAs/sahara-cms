@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Delivery CONFIRMATION (AFTER course assignment) & Material Upload
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
                <div class="portlet box red">
                    <div class="portlet-title">

                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            @if(sizeof($liste) == 0)
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Course/Program Title </th>
                                        <th> Course Start Date<br>Days/Dates Assigned </th>
                                        <th> Guidelines and Rules </th>
                                        <th> Process </th>
                                        <th> Uploaded Document Name & Details <br>
                                            Maximum file size is 30 MB, <br>
                                            if larger than 30 MB, you can divide and upload part-by-part </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>T_ID: {{$row->teklif_id}}</div>
                                            </td>
                                            <td>
                                                <div>{{$row->kurs_adi}}</div>
                                                <div class="font-purple">{{$row->egitimKayit->sirketUlke->adi}}</div>
                                            </td>
                                            <td>
                                                <div>Start: {{date('d.m.Y', strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi))}}</div>
                                                <div>&nbsp;</div>
                                                @foreach($row->egitim_bilgi as $eb_row)
                                                    <div>Day: {{$eb_row->ders_sira + 1}} --> {{date('d.m.Y', strtotime($eb_row->ders_tarihi))}}</div>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($row->ony_materyal == 1 && $row->ony_guideline == 1
                                                 && $row->ony_feerate == 1 && $row->ony_feepay == 1 && $row->ony_confidentiality == 1)
                                                    <a href="/cm_readandconfirm/{{$row->eh_id}}" class="font-green">Read and Confirmed</a>
                                                    @else
                                                    <a href="/cm_readandconfirm/{{$row->eh_id}}" class="font-red">Assignment NOT confirmed yet Please click here, read & confirm</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="/cm_upload/{{$row->teklif_id}}">Upload Materials</a>
                                            </td>
                                            <td>
                                                @foreach($row->yuklu_dosyalar as $d_row)
                                                    <div>
                                                        <div class="col-sm-12">
                                                            <div class="col-sm-1">
                                                                <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body"
                                                                   data-placement="left"
                                                                   data-original-title="Delete" onclick="silmeKontrolUpload('{{$d_row->id}}')"><i class="fa fa-trash"></i></a>
                                                            </div>
                                                            <div class="col-sm-11">{{$d_row->dosya_adi}}</div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="col-md-12 text-center">
                                    <div class="pagination-panel">
                                        Page <a href="{{ $liste->previousPageUrl() }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-left"></i></a>
                                        <input type="text" class="pagination-panel-input form-control input-sm input-inline input-mini" maxlenght="5" style="text-align:center; margin: 0 5px;" value="{{$liste->currentPage()}}">
                                        <a href="{{ $liste->nextPageUrl() }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-right"></i></a>
                                        of <span class="pagination-panel-total">{{$liste->lastPage()}}</span>
                                    </div>
                                </div>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
@endsection
@section("css")
    <!--  css dosyaları yuklenir -->

@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script type="text/javascript">
        function silmeKontrolUpload(id) {
            bootbox.dialog({
                size: "small",
                // title: "<i class='fa fa-warning'></i>",
                message: "<i class='fa fa-warning'></i> Are you sure?",
                onEscape: false,
                backdrop: true,
                centerVertical: true,
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-trash"></i> Delete',
                        className: 'btn-danger',
                        callback: function(result){
                            showLoading('');
                            var data = {
                                '_token' : "{{csrf_token()}}"
                            }
                            $.post('/cm_upload_del/' + id, data, function (cevap) {
                                if(cevap.cvp == 1) {
                                    window.location.reload();
                                } else {
                                    toastr['error'](cevap.msj, "{{config('messages.islem_bsarisiz')}}");
                                    hideLoading();
                                }
                            }, "json");
                        }
                    },
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel',
                        className: 'btn-default'
                    }
                }
            })
        }
    </script>
@endsection
