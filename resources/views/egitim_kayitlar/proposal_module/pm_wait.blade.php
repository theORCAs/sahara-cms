@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
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
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption"></div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" style="display: block;">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th scope="col"> # </th>
                                    <th scope="col" class="hidden-xs"> ID </th>
                                    <th scope="col" style="width:450px !important"> Create and Send Documents to Client/Customer </th>
                                    <th scope="col"> Course Title</br>Date </th>
                                    <th scope="col"> Name
                                        Email
                                        Pax Operations </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($liste as $key => $row)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td class="hidden-xs"> {{$row->id}} </td>
                                    <td>
                                        <div>CRF:
                                            @if(Auth::user()->isAllow('pm_waiting_edit'))
                                                <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="left" data-original-title="Update '{{$row->adi}}'"><i class="fa fa-pencil"></i></a>
                                            @endif
                                            @if(Auth::user()->isAllow('pm_waiting_del'))
                                                <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="left" data-original-title="Delete '{{$row->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                            @endif</div>
                                        <div>CRF Reg Date: {{date("d.m.Y", strtotime($row->created_at))}}</div>
                                        <div>

                                        </div>
                                        <div>Ref No: {{date("Y", strtotime($row->created_at))." / ".$row->id}}</div>
                                        <div><a href="/pm_wait/inv_pdf/{{$row->id}}">INV-Create PDF</a> @if($row->aktifTeklif->invoice_pdf != ''), <a href="{{Storage::URL($row->aktifTeklif->invoice_pdf)}}" target="_blank" class="font-green">INV-PDF</a> @endif</div>
                                        <div><a href="/pm_wait/cnf_pdf/{{$row->id}}">CNF-Create PDF</a> @if($row->aktifTeklif->confirmation_pdf != ''), <a href="{{Storage::URL($row->aktifTeklif->confirmation_pdf)}}" target="_blank" class="font-green">CNF-PDF</a> @endif</div>
                                        <div><a href="/pm_wait/prp_pdf/{{$row->id}}">PRP-Create PDF</a> @if($row->aktifTeklif->proposal_pdf != ''), <a href="{{Storage::URL($row->aktifTeklif->proposal_pdf)}}" target="_blank" class="font-green">PRP-PDF</a> @endif</div>
                                        <div><a href="/pm_wait/outl_pdf_create/{{$row->id}}">OUTL-Create PDF</a> @if($row->aktifTeklif->outline_pdf != ''), <a href="{{Storage::URL($row->aktifTeklif->outline_pdf)}}" target="_blank" class="font-green">OUTL-PDF</a> @endif</div>
                                        <br>
                                        <div>
                                            <a href="/pm_wait/send_email/{{$row->id}}">
                                            @if($row->aktifTeklif['teklif_gon_tarih'] == "")
                                                <span class="font-red">Send Email</span>
                                                @else
                                                <span class="font-green">Email Sent</span><span> - {{date('d.m.Y', strtotime($row->aktifTeklif['teklif_gon_tarih']))}}</span>
                                            @endif
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{$row->egitimler->kodu." ".$row->egitimler->adi}}</div>
                                        <div><a href="javascript:;">Schedule</a> / <a href="javascript:;">Outline</a></div>
                                        <div style="margin-top: 15px;">Start Date: {{date("d.m.Y", strtotime($row->egitimTarihi->baslama_tarihi))}}</div>
                                        <div># of Days: {{$row->egitimTarihi->egitim_suresi." ".$row->egitimTarihi->egitimPart->adi}}</div>
                                        <div>Venue: {{$row->egitimTarihi->egitimYeri->adi}}</div>
                                        <div style="margin-top: 15px">{{$row->sirket_adi}}</div>
                                        <label class="bg-purple-medium bg-font-purple-medium" style="border: 2px solid #FFF;">{{$row->sirketUlke["adi"]}}</label>
                                    </td>
                                    <td>
                                        <div>HR/Admin: {{$row->kontakKisiUnvan->adi." ".$row->ct_adi_soyadi}}</div>
                                        <div style="padding-left: 15px;">{{$row->ct_sirket_email}}</div>
                                        <div style="padding-left: 15px;">
                                            <span class="font-red">T: </span><span class="font-purple">{{$row->ct_telefon_kodu." ".$row->ct_telefon}}</span>
                                            <span class="font-red bolder">M: </span><span class="font-purple">{{$row->ct_cep_kodu." ".$row->ct_cep}}</span>
                                        </div>
                                        <div><a href="javascript:;"><span class="font-red">{{$row->katilimcilar->count()}} pax</span> Contacts & Email (After Course)</a></div>
                                        <div style="padding-left: 14px;">
                                            @foreach($row->katilimcilar as $k_key => $katilimci)
                                                <div>{{($k_key + 1).". ".$katilimci->adi_soyadi}}</div>
                                                <div>
                                                    <span class="font-red">E1: </span>{{$katilimci->email}}
                                                    <span class="font-red">E2: </span>{{$katilimci->email2}}
                                                </div>
                                                <div>
                                                    <span class="font-red">T1: </span>{{trim($katilimci->cep_tel_kodu." ".$katilimci->cep_tel)}}
                                                    <span class="font-red">T2: </span>{{trim($katilimci->cep_tel2_kodu." ".$katilimci->cep_tel2)}}
                                                </div>
                                                @endforeach
                                        </div>
                                        <div>&nbsp;</div>
                                        <div>
                                        @if($row->aktifTeklif->yorum != '')
                                            <a href="/pm_wait/commentView/{{$row->id}}/{{$row->ref_teklif_id}}" class="font-green">Comment <span class="font-dark">- {{date('d.m.Y', strtotime($row->aktifTeklif->yorum_tarih))}}</span></a>
                                            @else
                                            <a href="/pm_wait/commentView/{{$row->id}}/{{$row->ref_teklif_id}}" class="font-red">Comment</a>
                                        @endif
                                        </div>
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

    </script>
@endsection
