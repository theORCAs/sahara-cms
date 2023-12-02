@extends('layouts.main')

@section('content')
    <!-- icerik buraya girilir -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar hidden">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="javascript:;"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Confirmed Courses - All
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    <h4 class="alert-heading"><i class="fa fa-warning"></i> Error</h4>
                    {{ $error }}
                </div>
            @endforeach
            @if(Session::has("msj"))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> Success</h4>
                    {{Session::get("msj")}}
                </div>
            @endif
            <div class="col-md-12">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Confirmed Courses List </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th> <input type="checkbox" class="checkbox"> </th>
                                    <th>
                                        <div>ID</div>
                                        <div>Week</div>
                                    </th>
                                    <th>
                                        <div>Instructor(s)</div>
                                        <div>Pay Statu</div>
                                    </th>
                                    <th>
                                        <div>Course Title</div>
                                        <div>Date</div>
                                        <div># of Days</div>
                                        <div>Training Location</div>
                                    </th>
                                    <th>
                                        <div>Name</div>
                                        <div>Email</div>
                                        <div>Pax Operations</div>
                                    </th>
                                    <th>
                                        <div>Company Name</div>
                                        <div>Country</div>
                                        <div>Visa Operations</div>
                                        <div>Airport Transfer</div>
                                        <div>Participant Hotel</div>
                                        <div>Hotel Reservation</div>
                                    </th>
                                    <th>
                                        <div>Course Registration Form (CRF)</div>
                                        <div>and Create Customer Documents</div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($liste as $key => $row)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>
                                            <div>ID: {{$row->id}}</div>
                                            <div class="font-red">W: {{date('W', strtotime($row->egitimKayit->egitimTarihi['baslama_tarihi']))}}</div>
                                            <div>{{$row->egitimKayit->egitimTarihi['baslama_tarihi']}}</div>
                                        </td>
                                        <td>
                                            <div>Suggested by: {{$row->egitimKayit->egitimler->teklifEden['adi_soyadi'] != '' ? $row->egitimKayit->egitimler->teklifEden['adi_soyadi'] : 'SAHARA Group'}}</div>

                                            @if($row->egitimHocalar->count() > 0)
                                                @foreach($row->egitimHocalar as $hoca_row)
                                                    @if($hoca_row->hoca_id != "")
                                                        <div>
                                                            {{trim($hoca_row->hocaBilgi->unvani->adi." ".$hoca_row->hocaBilgi->adi_soyadi)}}
                                                            @if(sizeof($hoca_row->egitimMateryal($hoca_row->hocaBilgi['kullanici_id'], $row->id)) > 0)
                                                                <span class="font-green">M. Uploaded </span>
                                                            @else
                                                                <span class="font-red">M. NOT Uploaded </span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <a href="javascript:;" class="" style="text-decoration: underline;">
                                                                @if($hoca_row["dersatama_mail"] == "")
                                                                    <span class="font-red">[Email S]</span>
                                                                @else
                                                                    <span class="font-green">[S Email]</span>
                                                                @endif
                                                            </a>
                                                            @if($hoca_row["ony_materyal"] == 1 && $hoca_row["ony_guidline"] == 1
                                                            && $hoca_row["ony_feerate"] == 1 && $hoca_row["ony_feepay"] == 1
                                                            && $hoca_row["ony_confidentiality"] == 1)
                                                                <span class="font-green">[Confirmed]</span>
                                                            @else
                                                                <span class="font-red">[NOT Confirmed]</span>
                                                            @endif
                                                            <a href="javascript:;" style="text-decoration: underline;">
                                                                @if($hoca_row["odeme_yapilma_tarih"] == '')
                                                                    <span class="font-red">[Unpaid {{$hoca_row["ucret"]}}]</span>
                                                                @else
                                                                    <span class="font-green">[Paid]</span>
                                                                @endif
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif

                                            <div>
                                            </div>
                                            <div><a href="/cc_now/insxsetup/{{$row->id}}">Instructor X Setup</a></div>
                                        </td>
                                        <td>
                                            <div>{{$row->egitimKayit->egitimler['kodu']." ".$row->egitimKayit->egitimler['adi']}}</div>
                                            <div>Start Date: <span class="font-red">{{date('d.m.Y', strtotime($row->egitimKayit->egitimTarihi['baslama_tarihi']))}}</span></div>
                                            <div># of Days: <span class="font-red">{{$row->egitimKayit->egitimTarihi['egitim_suresi']." ".$row->egitimKayit->egitimTarihi->egitimPart['adi']}}</span></div>
                                            <div>Venue: {{$row->egitimKayit->egitimTarihi->egitimYeri['adi']}}</div>
                                            <div>M Room Reservation:
                                                @if(sizeof($row->kursYeriRezerMail) > 0)
                                                    <a href="javascript:;" class="font-green">{{$row->kursYeriRezerMail[0]->otelBilgi['adi']}} Email S</a>
                                                    {{date('d.m.Y', strtotime($row->kursYeriRezerMail[0]->created_at))}}
                                                    <a hred="javascript:;">History</a>
                                                @else
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @endif
                                            </div>
                                            <div>Training Location:
                                                @if($row->kursYeri['otel_id'] > 0)
                                                    <a href="javascript:;" class="font-green">{{$row->kursYeri->otelBilgi['adi']}}</a>
                                                @else
                                                    <a href="javascript:;" class="font-red">Assign Training Location</a>
                                                @endif
                                            </div>
                                            <div>-</div>
                                            <div>
                                                @if($row->kursYeri['mail_egitmen'] == '')
                                                    <a href="javascript:;" class="font-red">T. Location-Instructor(s)</a>
                                                @else
                                                    <a href="javascript:;" class="font-green">T. Location-Instructor(s)</a>
                                                    {{date("d.m.Y", strtotime($row->kursYeri['mail_egitmen']))." ".$row->kursYeri->egitmenMailiGonderen['adi_soyadi']}}
                                                @endif
                                            </div>
                                            <div>
                                                @if($row->kursYeri['mail_katilimci'] == '')
                                                    <a href="javascript:;" class="font-red">T. Location-Participant(s)</a>
                                                @else
                                                    <a href="javascript:;" class="font-green">T. Location-Participant(s)</a>
                                                    {{date("d.m.Y", strtotime($row->kursYeri['mail_katilimci']))." ".$row->kursYeri->katilimciMailiGonderen['adi_soyadi']}}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>HR/Admin: {{trim($row->egitimKayit->kontakKisiUnvan['adi']." ".$row->egitimKayit['ct_adi_soyadi'])}}</div>
                                            <div>{{$row->egitimKayit['ct_sirket_email']}}
                                                @if($row['hradmin_mail'] == '')
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @else
                                                    <a href="javasxript:;" class="font-green">Email S</a> {{date('d.m.Y', strtotime($row['hradmin_mail']))}}
                                                @endif
                                            </div>
                                            <div>
                                                <span class="font-red">T: </span><span class="font-purple">{{trim($row->egitimKayit["ct_telefon_kodu"]." ".$row->egitimKayit['ct_telefon'])}}</span>
                                                <span class="font-red">M: </span><span class="font-purple">{{trim($row->egitimKayit["ct_cep_kodu"]." ".$row->egitimKayit['ct_cep'])}}</span>
                                            </div>
                                            <div><a href="javascript:;"><span class="font-red">{{$row->egitimKayit->katilimcilar()->count()}} Pax</span> Contacts (Add E2 and M2)</a></div>
                                            <div>
                                                @foreach($row->egitimKayit->katilimcilar as $k_key => $k_row)
                                                    <div>{{($k_key + 1).". ".$k_row['adi_soyadi']}}</div>
                                                    <div><span class="font-red">E1: </span>{{$k_row['email']}}

                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{$row->egitimKayit['sirket_adi']}}</div>
                                            <div class="font-purple">{{$row->egitimKayit->sirketUlke['adi']}}</div>
                                            <div>Visa Form
                                                @if($row['vdm_tarih'] != "")
                                                    <a href="javascript:;" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['vdm_tarih']))." ".$row->visaDavetMailGonderenKisi['adi_soyadi']}}
                                                @else
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @endif
                                            </div>
                                            <div>Visa: <a href="javascript:;">Form Filled-View</a>, <a href="javascript:;">Create PDF</a></div>
                                            <div>Visa Letter (PDF)
                                                @if($row['vpm_tarih'] == "")
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @else
                                                    <a href="javascript:;" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['vpm_tarih']))." ".$row->visaDavetPdfGonderenKisi['adi_soyadi']}}
                                                @endif
                                            </div>
                                            <div><b>Airport Transfer: </b>
                                                @if($row["apt_tarih"] == "")
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @else
                                                    <a href="javascript:;" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['apt_tarih']))." ".$row->airportTrasnferMailiGonderenKisi['adi_soyadi']}}
                                                @endif
                                            </div>
                                            <div><b>Hotel Reservation: </b>
                                                @if($row['orm_tarih'] == "")
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @else
                                                    <a href="javascript:;" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['orm_tarih']))." ".$row->otelRezervasyonMailiGonderenKisi['adi_soyadi']}}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div><a href="javascript:;">CRF View/Update</a></div>
                                            <div>Registration D: {{date('d.m.Y', strtotime($row->egitimKayit->created_at))}}</div>
                                            <div>&nbsp;</div>
                                            <div>Ref #: {{$row->egitimKayit->pdfInvoice['referans_no']}}</div>
                                            <div>
                                                <a href="javascript:;">INV-Create PDF</a>
                                                @if($row['invoice_pdf'] != "")
                                                    , <a href="javascript:;">INV-PDF</a>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="javascript:;">CNF-Create PDF</a>
                                                @if($row['confirmation_pdf'] != "")
                                                    , <a href="javascript:;">CNF-PDF</a>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="javascript:;">PRP-Create PDF</a>
                                                @if($row['proposal_pdf'] != '')
                                                    , <a href="javascript:;">PRP-PDF</a>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="javascript:;">OUTL-Create PDF</a>
                                                @if($row['outline_pdf'] != "")
                                                    , <a href="javascript:;">OUTL-PDF</a>
                                                @endif
                                            </div>
                                            <div style="margin-top: 5px;">
                                                @if($row['teklif_gon_tarih'] != "")
                                                    <a href="javascript:;" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['teklif_gon_tarih']))}}
                                                @else
                                                    <a href="javascript:;" class="font-red">Send Email</a>
                                                @endif
                                            </div>
                                            <div><a href="javascript:;" class="font-red">Add Photo</a></div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="col-md-12 text-center @if($liste->lastPage() == 1) hidden @endif">
                                <div class="pagination-panel">
                                    Page <a href="{{ $liste->previousPageUrl() }}" class="btn btn-sm default prev @if($liste->currentPage() == 1) disabled @endif"><i class="fa fa-angle-left"></i></a>
                                    <input type="text" class="pagination-panel-input form-control input-sm input-inline input-mini" maxlenght="5" style="text-align:center; margin: 0 5px;" value="{{$liste->currentPage()}}">
                                    <a href="{{ $liste->nextPageUrl() }}" class="btn btn-sm default next @if($liste->currentPage() == $liste->lastPage()) disabled @endif"><i class="fa fa-angle-right"></i></a>
                                    of <span class="pagination-panel-total">{{$liste->lastPage()}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyları yuklenir -->
@endsection
@section("js")
    <!-- js dosyları yuklenir -->
@endsection
