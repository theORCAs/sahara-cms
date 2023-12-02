@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Course Attendance & Evaluations
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
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div>#</div>
                                            <div>ID</div>
                                        </th>
                                        <th> TRAINING </th>
                                        <th>
                                            <div>Pax Name, Email</div>
                                            <div>Company, Country</div>
                                        </th>
                                        <th>  </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>ID: {{$row->id}}</div>
                                            </td>
                                            <td>
                                                <div>CRF: <a href="pm_wait/{{$row->egitimKayit->id}}/edit" target="_blank">View/Update</a></div>
                                                <div>&nbsp;</div>
                                                <div>{{$row->egitimKayit->egitimler->kodu." ".$row->egitimKayit->egitimler->adi}}</div>
                                                <div>Start D: <span class="font-red">{{date("d.m.Y", strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi))}}</span></div>
                                                <div># of days: <span class="font-red">{{$row->egitimKayit->egitimTarihi->egitim_suresi}}</span></div>
                                                <div>&nbsp;</div>
                                                <div>
                                                    @foreach($row->egitimHocalar as $hoca_row)
                                                        <li>{{$hoca_row->hocaBilgi->adi_soyadi}} (Instructor)</li>
                                                        @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div>ADM: {{$row->egitimKayit->ct_adi_soyadi}}</div>
                                                <div>{{$row->egitimKayit->ct_sirket_email}}</div>
                                                <div><span class="font-red">{{$row->egitimKayit->katilimcilar->count()}}</span> PAX:</div>
                                                @foreach($row->egitimKayit->katilimcilar as $pax_row)
                                                <div>{{$pax_row->adi_soyadi}}</div>
                                                <div>{{$pax_row->email}}</div>
                                                    @endforeach
                                                <div>&nbsp;</div>
                                                <div>{{$row->egitimKayit->sirket_adi}}</div>
                                                <div class="font-purple">{{$row->egitimKayit->sirketUlke->adi}}</div>
                                            </td>
                                            <td>
                                                <div><b>Location:</b> {{$row->egitimKayit->egitimTarihi->egitimYeri->adi}}</div>
                                                <div>&nbsp;</div>
                                                <div>Course Evaluation:</div>

                                                @foreach($row->egitimKayit->katilimcilar as $pax_row)
                                                    @php($katilimci_ek_bilgi = $pax_row->katilimciEkBilgi($row->egitimKayit->ref_teklif_id))
                                                    <div>
                                                        <a href="/{{$prefix}}/evaluationFormCreate/{{$pax_row->id}}" target="_blank">Create Evaluation Form PDF - {{$pax_row->adi_soyadi}}</a>
                                                        @if($katilimci_ek_bilgi["evaluation_form_pdf"] != "")
                                                            <a href="{{Storage::url($katilimci_ek_bilgi["evaluation_form_pdf"])}}" target="_blank"> - View PDF</a>
                                                            @endif
                                                    </div>
                                                    @endforeach
                                                @foreach($row->egitimKayit->katilimcilar as $pax_row)
                                                    @php($katilimci_ek_bilgi = $pax_row->katilimciEkBilgi($row->egitimKayit->ref_teklif_id))
                                                    <div>
                                                        <a href="/{{$prefix}}/evaluationMailView/{{$pax_row->id}}">Email for Online Evaluation - {{$pax_row->adi_soyadi}}</a>
                                                        @if($katilimci_ek_bilgi['evaluation_mail'] != '')
                                                            - {{date('d.m.Y', strtotime($katilimci_ek_bilgi['evaluation_mail']))}}
                                                            @endif
                                                    </div>
                                                @endforeach
                                                @foreach($row->egitimKayit->katilimcilar as $pax_row)
                                                    <div><a href="javascript:;" class="font-red">Evaluation Result in Class - {{$pax_row->adi_soyadi}}</a></div>
                                                @endforeach
                                                <div>&nbsp;</div>
                                                <div>Attendance Form:</div>
                                                <div><a href="javascript:;">Create PDF</a>, <a href="javascript:;">View PDF</a></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="col-md-12 text-center @if($liste->lastPage() == 1) hidden @endif">
                                    <div class="pagination-panel">
                                        <a href="{{ $liste->url(1) }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-double-left"></i></a>
                                        <a href="{{ $liste->previousPageUrl() }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-left"></i></a>
                                        Page <input type="text" class="pagination-panel-input form-control input-sm input-inline input-mini" maxlenght="5" style="text-align:center; margin: 0 5px;" value="{{$liste->currentPage()}}">
                                        of <span class="pagination-panel-total">{{$liste->lastPage()}}</span>
                                        <a href="{{ $liste->nextPageUrl() }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-right"></i></a>
                                        <a href="{{ $liste->url($liste->lastPage()) }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-double-right"></i></a>
                                        (# of records: {{$liste->total()}})
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

@endsection
