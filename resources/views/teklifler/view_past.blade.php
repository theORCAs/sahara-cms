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
            <div class="col-md-6">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-search"></i>Filters </div>
                        <div class="tools">
                            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" style="display: none;">
                        <form id="filtre_form" class="form-horizontal" method="post" action="{{$prefix}}/searchPast">
                            @csrf
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Year</label>
                                    <div class="col-md-4">
                                        <select id="filtre_yil" name="filtre_yil" class="form-control" onchange="filtreUlkeGetir()">
                                            @foreach($filtre_yil_liste as $fy_row)
                                                <option value="{{$fy_row->yil}}"@if($filtre_yil == $fy_row->yil) selected @endif>{{$fy_row->yil." (".$fy_row->sayi.")"}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Country</label>
                                    <div class="col-md-4">
                                        <select id="filtre_ulke_id" name="filtre_ulke_id" class="form-control" onchange="filtreSirketGetir()">
                                            <option value="0">Select</option>
                                            @foreach($filtre_ulke_liste as $fu_row)
                                                <option value="{{$fu_row->id}}"@if($filtre_ulke_id == $fu_row->id) selected @endif>{{$fu_row->adi." (" . $fu_row->sayi . ")"}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Company Name</label>
                                    <div class="col-md-8">
                                        <select id="filtre_sirket_id" name="filtre_sirket_id" class="" onchange="hocaOdemeGetir()">
                                            <option value="0">Select</option>
                                            @foreach($filtre_ref_sirket_liste as $sl_row)
                                                <option value="{{$sl_row->id}}" {{$filtre_ref_sirket_id == $sl_row->id ? ' selected' : ''}}>{{$sl_row->adi." ($sl_row->sayi)"}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Instructor Payment Status</label>
                                    <div class="col-md-4">
                                        <select id="filtre_hoca_odeme" name="filtre_hoca_odeme" class="form-control" onchange="kursOdemeGetir()">
                                            <option value="0">Select</option>
                                            <option value="1" {{$filtre_hoca_odeme == "1" ? ' selected' : ''}}>Paid ({{$filtre_hoca_odeme_liste->paid_sayi}})</option>
                                            <option value="2" {{$filtre_hoca_odeme == "2" ? ' selected' : ''}}>Unpaid ({{$filtre_hoca_odeme_liste->unpaid_sayi}})</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <label class="col-md-4 control-label">Course Payment Status</label>
                                    <div class="col-md-4">
                                        <select id="filtre_egitim_odeme" name="filtre_egitim_odeme" class="form-control">
                                            <option value="0">Select</option>
                                            <option value="1" {{$filtre_egitim_odeme == "1" ? ' selected' : ''}}>Paid ({{$filtre_egitim_odeme_liste->paid_sayi}})</option>
                                            <option value="2" {{$filtre_egitim_odeme == "2" ? ' selected' : ''}}>Unpaid ({{$filtre_egitim_odeme_liste->unpaid_sayi}})</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-4 col-md-9">
                                        <button type="submit" class="btn green"><i class="fa fa-search"></i> Search</button>
                                        <button type="button" class="btn default"><i class="fa fa-times"></i> Reset sorting sequence below table to default setting</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Confirmed Courses List </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-responsive">
                            @if(sizeof($liste) == 0)
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{config('messages.listelenecek_kayit_yok')}}</div>
                            @else
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
                                    @php($tmp_week = "")
                                    @foreach($liste as $key => $row)
                                        @if($tmp_week != date('W', strtotime($row->baslama_tarihi)))
                                            <tr>
                                                <td colspan="7" class="bg-red font-white">{{date('Y', strtotime($row->baslama_tarihi))}} - WEEK {{date('W', strtotime($row->baslama_tarihi))}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">-</td>
                                                <td colspan="5">
                                                    <div><a href="/{{$prefix}}/meetingRoomReservationView/{{date('Y-m-d', strtotime($row->baslama_tarihi))}}">W: {{date('W', strtotime($row->baslama_tarihi))}} - Meeting Room Reservation</a> </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @php($tmp_week = date('W', strtotime($row->baslama_tarihi)) )
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>
                                                <div>ID: {{$row->id}}</div>
                                                <div class="font-red">W: {{date('W', strtotime($row->baslama_tarihi))}}</div>
                                            </td>
                                            <td>
                                                <div>Suggested by: {{$row->egitimKayit->egitimler->teklifEden['adi_soyadi'] != '' ? $row->egitimKayit->egitimler->teklifEden['adi_soyadi'] : 'SAHARA Group'}}</div>
                                                <div>
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                @for($hgun = 0; $hgun < $row->egitimKayit->egitimTarihi->egitim_suresi; $hgun++)
                                                                    @php($egitim_tarih = date('d.m', strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi." +$hgun day")))
                                                                    @php($atanma_durum = $row->guneHocaAtanmismi(date('Y-m-d', strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi." +$hgun day"))) )
                                                                    {!! $hgun % 5 == 0 ? "<br>" : "" !!}
                                                                    <div class="{{(int)$atanma_durum["atanmis"] > 0 ? 'secili-gun' : 'secili-degil-gun'}} text-center" style="float: left">{{$egitim_tarih}}<br>{{$atanma_durum["hoca_kisa_adi"]}}</div>
                                                                @endfor
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                @php($egitim_hoca_arr = [])
                                                @foreach($row->egitimHocalar as $hoca_row)
                                                    @if(!in_array($hoca_row->hoca_id, $egitim_hoca_arr))
                                                        @php(array_push($egitim_hoca_arr, $hoca_row->hoca_id))
                                                    @else
                                                        @continue
                                                    @endif
                                                    <div>
                                                        <a href="/ia_active/{{$hoca_row->hocaBilgi->id}}/edit" target="_blank">{{trim($hoca_row->hocaBilgi->unvani['adi']." ".$hoca_row->hocaBilgi["adi_soyadi"])}}</a>
                                                        @if(sizeof($hoca_row->egitimMateryal($hoca_row->hocaBilgi['kullanici_id'], $row->id)) > 0)
                                                            <span class="font-green">M. Uploaded </span>
                                                        @else
                                                            <span class="font-red">M. NOT Uploaded </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="/{{$prefix}}/courseAssignMailView/{{$hoca_row->id}}" class="" style="text-decoration: underline;">
                                                            @if($hoca_row["dersatama_mail"] == "")
                                                                <span class="font-red">[Email S]</span>
                                                            @else
                                                                <span class="font-green">[S Email]</span>
                                                            @endif
                                                        </a>
                                                        @if($hoca_row["ony_materyal"] == 1 && $hoca_row["ony_guideline"] == 1
                                                        && $hoca_row["ony_feerate"] == 1 && $hoca_row["ony_feepay"] == 1
                                                        && $hoca_row["ony_confidentiality"] == 1)
                                                            <span class="font-green">[Confirmed]</span>
                                                        @else
                                                            <span class="font-red">[NOT Confirmed]</span>
                                                        @endif
                                                        <a href="/{{$prefix}}/coursePaymentMailView/{{$hoca_row->id}}" style="text-decoration: underline;">
                                                            @if(empty($hoca_row["odeme_yapilma_tarih"]))
                                                                <span class="font-red">[Unpaid {{$hoca_row["ucret"]}}]</span>
                                                            @else
                                                                <span class="font-green"
                                                                      title="{{$hoca_row->odeme_yapilma_tarih != '' ? 'Mail Date: '.date('d.m.Y', strtotime($hoca_row->odeme_yapilma_tarih)) : ''}}">
                                                                    [Paid]</span>
                                                            @endif
                                                        </a>
                                                    </div>
                                                @endforeach


                                                <div>
                                                </div>
                                                <div><a href="/{{$prefix}}/insxsetup/{{$row->id}}">Instructor X Setup</a></div>
                                            </td>
                                            <td>
                                                <div>{{$row->egitimKayit->egitimler['kodu']." ".$row->egitimKayit->egitimler['adi']}}</div>
                                                <div>Start Date: <span class="font-red">{{date('d.m.Y', strtotime($row->egitimKayit->egitimTarihi['baslama_tarihi']))}}</span></div>
                                                <div>
                                                    <div class="col-sm-6"># of Days: <span class="font-red">{{$row->egitimKayit->egitimTarihi['egitim_suresi']." ".$row->egitimKayit->egitimTarihi->egitimPart['adi']}}</span></div>
                                                    <div class="col-sm-6"><a href="/to_outline/{{$row->egitimKayit->egitim_id}}/outline_edit" target="_blank">Outline</a></div>
                                                </div>
                                                <div>Venue: {{$row->egitimKayit->egitimTarihi->egitimYeri['adi']}}</div>
                                                <div>M Room Reservation:
                                                    @if(sizeof($row->kursYeriRezerMail) > 0)
                                                        <a href="javascript:;" class="font-green">Email S</a>
                                                        {{date('d.m.Y', strtotime($row->kursYeriRezerMail[0]->created_at))}}
                                                        <a hred="javascript:;">History</a>
                                                    @else
                                                        <a href="" class="font-red">Send Email</a>
                                                    @endif
                                                </div>
                                                <div>Training Location:
                                                    @if($row->kursYeri['otel_id'] > 0)
                                                        <a href="{{$prefix}}/assignTrainingLocationView/{{$row->id}}" class="font-green">{{$row->kursYeri->otelBilgi['adi']}}</a>
                                                    @else
                                                        <a href="{{$prefix}}/assignTrainingLocationView/{{$row->id}}" class="font-red">Assign Training Location</a>
                                                    @endif
                                                </div>
                                                <div>-</div>
                                                <div>
                                                    @if($row->kursYeri['mail_egitmen'] == '')
                                                        <a href="/{{$prefix}}/tLocationInstructorMailView/{{$row->id}}" class="font-red">T. Location-Instructor(s)</a>
                                                    @else
                                                        <a href="/{{$prefix}}/tLocationInstructorMailView/{{$row->id}}" class="font-green">T. Location-Instructor(s)</a>
                                                        {{date("d.m.Y", strtotime($row->kursYeri['mail_egitmen']))." ".$row->kursYeri->egitmenMailiGonderen['adi_soyadi']}}
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($row->kursYeri['mail_katilimci'] == '')
                                                        <a href="/{{$prefix}}/tLocationParticipantMailView/{{$row->id}}" class="font-red">T. Location-Participant(s)</a>
                                                    @else
                                                        <a href="/{{$prefix}}/tLocationParticipantMailView/{{$row->id}}" class="font-green">T. Location-Participant(s)</a>
                                                        {{date("d.m.Y", strtotime($row->kursYeri['mail_katilimci']))." ".$row->kursYeri->katilimciMailiGonderen['adi_soyadi']}}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>HR/Admin: {{trim($row->egitimKayit->kontakKisiUnvan['adi']." ".$row->egitimKayit['ct_adi_soyadi'])}}</div>
                                                @if($row->egitimKayit->ct_sirket_email != '')
                                                    <div>{{$row->egitimKayit['ct_sirket_email']}}
                                                        @if($row->hradmin_mail == '')
                                                            <a href="/{{$prefix}}/hrAdminMailView/{{$row->id}}" class="font-red">Send Email</a>
                                                        @else
                                                            <a href="/{{$prefix}}/hrAdminMailView/{{$row->id}}" class="font-green">Email S</a> {{date('d.m.Y', strtotime($row['hradmin_mail']))}}
                                                        @endif
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="font-red">T: </span><span class="font-purple">{{trim($row->egitimKayit["ct_telefon_kodu"]." ".$row->egitimKayit['ct_telefon'])}}</span>
                                                    <span class="font-red">M: </span><span class="font-purple">{{trim($row->egitimKayit["ct_cep_kodu"]." ".$row->egitimKayit['ct_cep'])}}</span>
                                                </div>
                                                <div><a href="javascript:;"><span class="font-red">{{$row->egitimKayit->katilimcilar()->count()}} Pax</span> Contacts (Add E2 and M2)</a></div>
                                                <div>
                                                    @foreach($row->egitimKayit->katilimcilar as $k_key => $k_row)
                                                        @php($katilimci_ek = $k_row->katilimciEkBilgi($row->id))
                                                        <div>{{($k_key + 1).". ".$k_row['adi_soyadi']}} <a href="/{{$prefix}}/paxCertificateView/{{$k_row->id}}/{{$row->id}}" target="_blank">Certificate</a></div>
                                                        <div><span class="font-red">E1: </span>{{$k_row['email']}}
                                                            @if($katilimci_ek['deneyim_mail_tarih'] != "")
                                                                <a href="" class="font-green">Email S</a>
                                                            @else
                                                                <a href="/{{$prefix}}/paxExperienceMailView/{{$k_row->id}}/{{$row->id}}" class="font-red">Send Email</a>
                                                            @endif
                                                        </div>
                                                        <div><span class="font-red">E2: </span>{{$k_row->email2}}</div>
                                                        <div>
                                                            <span class="font-red">M1: </span>{{trim($k_row->cep_tel_kodu." ".$k_row->cep_tel)}}
                                                            <span class="font-red">M2: </span>{{trim($k_row->cep_tel2_kodu." ".$k_row->cep_tel2)}}
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div><a href="/{{$prefix}}/allPaxExperienceMailView/{{$row->id}}">Send Email to All Participant</a></div>
                                                <div>
                                                    @if($row->flg_odendi == "1")
                                                        <a href="javascript:;" id="odeme_durum_{{$row->id}}" class="font-green" onclick="teklifOdemeYapJson('{{$row->id}}', '0')">Paid</a>
                                                    @else
                                                        <a href="javascript:;" id="odeme_durum_{{$row->id}}" class="font-red" onclick="teklifOdemeYapJson('{{$row->id}}', '1')">Unpaid</a>
                                                    @endif
                                                    / <a href="javascript:;" onclick="yorumYazModal('{{$row->id}}')" class="{{$row->yorum != "" ? 'font-green' : ''}}" id="yorum_href_{{$row->id}}">Comment</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{$row->egitimKayit['sirket_adi']}}</div>
                                                <div class="font-purple">{{$row->egitimKayit->sirketUlke['adi']}}</div>
                                                <div>Visa Form
                                                    @if($row['vdm_tarih'] != "")
                                                        <a href="/{{$prefix}}/visaLetterReqMailView/{{$row->id}}" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['vdm_tarih']))." ".$row->visaDavetMailGonderenKisi['adi_soyadi']}}
                                                    @else
                                                        <a href="/{{$prefix}}/visaLetterReqMailView/{{$row->id}}" class="font-red">Send Email</a>
                                                    @endif
                                                </div>
                                                <div>Visa: <a href="/{{$prefix}}/visaFormFilledView/{{$row->id}}">Form Filled-View</a>, <a href="/{{$prefix}}/visaLetterPDFView/{{$row->id}}">Create PDF</a>
                                                    @if($row->vpm_pdf_dosyasi != '')
                                                        <a href="{{Storage::URL($row->vpm_pdf_dosyasi)}}" target="_blank" class="font-green">View PDF</a>
                                                    @endif
                                                </div>
                                                <div>Visa Letter (PDF)
                                                    @if($row->vpm_pdf_dosyasi != "")
                                                        @if($row['vpm_tarih'] == "")
                                                            <a href="/{{$prefix}}/visaLetterPDFMailView/{{$row->id}}" class="font-red">Send Email</a>
                                                        @else
                                                            <a href="/{{$prefix}}/visaLetterPDFMailView/{{$row->id}}" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['vpm_tarih']))." ".$row->visaDavetPdfGonderenKisi['adi_soyadi']}}
                                                        @endif
                                                    @else
                                                        <span class="font-red">not created</span>
                                                    @endif
                                                </div>
                                                <div><b>Airport Transfer: </b>
                                                    @if($row["apt_tarih"] == "")
                                                        <a href="/{{$prefix}}/airportTransferMailView/{{$row->id}}" class="font-red">Send Email</a>
                                                    @else
                                                        <a href="/{{$prefix}}/airportTransferMailView/{{$row->id}}" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['apt_tarih']))." ".$row->airportTrasnferMailiGonderenKisi['adi_soyadi']}}
                                                    @endif
                                                </div>
                                                @if($row->airportTransferFormu->id > 0)
                                                    <div><span class="font-red">(Airport F. Filled)</span> {{date('d.m.Y', strtotime($row->airportTransferFormu->created_at))}}</div>
                                                    <div><b>Arrival Date: </b>
                                                        {!! $row->airportTransferFormu->gelis_tarih != '' ? date('d.m.Y', strtotime($row->airportTransferFormu->gelis_tarih))." <b>Time:</b> ".
                                                        date('H:s', strtotime($row->airportTransferFormu->gelis_saat))
                                                        : '' !!}
                                                    </div>
                                                    @if($row->airportTransferFormu->gelis_tasima_onay_id > 0)
                                                        <div class="bg-green">Registered with Transfer Company</div>
                                                    @else
                                                        <div class="bg-red font-white">NOT Registered with Transfer Company</div>
                                                    @endif
                                                    <div><a href="/at_confirmed_arr/{{$row->airportTransferFormu->id}}/edit" target="_blank">View F. Form</a></div>
                                                @endif
                                                <div><b>Hotel Reservation: </b>
                                                    @if($row['orm_tarih'] == "")
                                                        <a href="/{{$prefix}}/hotelReservationFormMailView/{{$row->id}}" class="font-red">Send Email</a>
                                                    @else
                                                        <a href="/{{$prefix}}/hotelReservationFormMailView/{{$row->id}}" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['orm_tarih']))." ".$row->otelRezervasyonMailiGonderenKisi['adi_soyadi']}}
                                                    @endif
                                                </div>
                                                @foreach($row->otelRezervasyon->odalar as $rezotel_row)
                                                    <div>Participant Hotel: {{$rezotel_row->otel->adi}}</div>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div><a href="/pm_wait/{{$row->egitim_kayit_id}}/edit" target="_blank">CRF View/Update</a></div>
                                                <div>Registration D: {{date('d.m.Y', strtotime($row->egitimKayit->created_at))}}</div>
                                                <div>&nbsp;</div>
                                                <div>Ref #: {{$row->egitimKayit->pdfInvoice['referans_no']}}</div>
                                                <div>
                                                    <a href="/pm_wait/inv_pdf/{{$row->egitim_kayit_id}}">INV-Create PDF</a>
                                                    @if($row['invoice_pdf'] != "")
                                                        , <a href="{{Storage::URL($row->invoice_pdf)}}" target="_blank">INV-PDF</a>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="/pm_wait/cnf_pdf/{{$row->egitim_kayit_id}}" target="_blank">CNF-Create PDF</a>
                                                    @if($row['confirmation_pdf'] != "")
                                                        , <a href="{{Storage::URL($row->confirmation_pdf)}}" target="_blank">CNF-PDF</a>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="/pm_wait/prp_pdf/{{$row->egitim_kayit_id}}" target="_blank">PRP-Create PDF</a>
                                                    @if($row['proposal_pdf'] != '')
                                                        , <a href="{{Storage::URL($row->proposal_pdf)}}" target="_blank">PRP-PDF</a>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="/pm_wait/outl_pdf_create/{{$row->egitim_kayit_id}}" target="_blank">OUTL-Create PDF</a>
                                                    @if($row['outline_pdf'] != "")
                                                        , <a href="{{Storage::URL($row->outline_pdf)}}" target="_blank">OUTL-PDF</a>
                                                    @endif
                                                </div>
                                                <div>&nbsp;</div>
                                                <div style="margin-top: 5px;">
                                                    @if($row['teklif_gon_tarih'] != "")
                                                        <a href="/pm_wait/send_email/{{$row->egitim_kayit_id}}" class="font-green">Email Sent</a> {{date('d.m.Y', strtotime($row['teklif_gon_tarih']))}}
                                                    @else
                                                        <a href="/pm_wait/send_email/{{$row->egitim_kayit_id}}" class="font-red">Send Email</a>
                                                    @endif
                                                </div>
                                                <div><a href="javascript:;" class="font-red">Add Photo</a></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyları yuklenir -->
    <style type="text/css">
        .secili-gun {
            background-color: #2fa360;
            font-size: 10px;
            width: 35px;
            height: 35px;
            margin: 1px;
        }
        .secili-degil-gun {
            background-color: #e7505a;
            font-size: 10px;
            width: 35px;
            height: 35px;
            margin: 1px;
        }
    </style>
@endsection
@section("js")
    <!-- js dosyları yuklenir -->
    <script type="text/javascript">
        $(document).ready(function () {
            $("#filtre_sirket_id").select2();
        });

        function teklifOdemeYapJson(teklif_id, flg_odendi) {
            bootbox.confirm("Do you want to change payment stuation?", function(result) {
                if(result) {
                    showLoading('', '');
                    $.get('/{{$prefix}}/teklifOdemeDurumDegistirJson/' + teklif_id + "/" + flg_odendi, function (cevap) {
                        $("#odeme_durum_" + teklif_id).removeClass('font-green');
                        $("#odeme_durum_" + teklif_id).removeClass('font-red');
                        if(flg_odendi == "1") {
                            $("#odeme_durum_" + teklif_id).addClass('font-green');
                            $("#odeme_durum_" + teklif_id).text('Paid');
                            $("#odeme_durum_" + teklif_id).attr('onclick', "teklifOdemeYapJson('" + teklif_id + "', '0')");
                        } else{
                            $("#odeme_durum_" + teklif_id).addClass('font-red');
                            $("#odeme_durum_" + teklif_id).text('Unpaid');
                            $("#odeme_durum_" + teklif_id).attr('onclick', "teklifOdemeYapJson('" + teklif_id + "', '1')");
                        }
                    }, "json").done(function () {
                        hideLoading('');
                    });
                }
            });
        }

        function yorumYazModal(teklif_id) {
            var data = {
                "_method" : "GET",
                'teklif_id' : teklif_id,
            };
            showLoading('', '');
            $.post("/{{$prefix}}/yorumYazModalView", data, function (cevap) {
                $("#stack1").data("width", 900).html(cevap).modal("show");
            }).done(function () {
                hideLoading();
            });
        }

        function filtreUlkeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val()
            };

            showLoading('', '');
            $.post("/cc_past/filtreUlkeGetirJSON", data, function (cevap) {
                $("#filtre_ulke_id option:first").prop('selected', true);
                $("#filtre_ulke_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_ulke_id").append("<option value='" + row.id + "' " + ("{{$filtre_ulke_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                hideLoading();
                $("#filtre_ulke_id").trigger('change')
            });
        }

        function filtreSirketGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val()
            };

            $.post("/cc_past/filtreSirketGetirJSON", data, function (cevap) {
                $("#filtre_sirket_id option:first").prop('selected', true);
                $("#filtre_sirket_id option:gt(0)").remove();
                $.each(cevap, function (i, row) {
                    $("#filtre_sirket_id").append("<option value='" + row.id + "' " + ("{{$filtre_ref_sirket_id}}" == row.id ? ' selected' : '') + ">" + row.adi + " (" + row.sayi + ")</option>");
                })
            }, "json").done(function () {
                $("#filtre_sirket_id").trigger('change')
            });
        }

        function hocaOdemeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_sirket_id" : $("#filtre_sirket_id").val()
            };

            $.post("/cc_past/filtreHocaOdemeGetirJSON", data, function (row) {
                $("#filtre_hoca_odeme option:first").prop('selected', true);
                $("#filtre_hoca_odeme option:gt(0)").remove();

                $("#filtre_hoca_odeme").append("<option value='1' " + ("{{$filtre_hoca_odeme}}" == 1 ? " selected" : "") + ">Paid (" + row.paid_sayi + ")</option>");
                $("#filtre_hoca_odeme").append("<option value='2' " + ("{{$filtre_hoca_odeme}}" == 2 ? " selected" : "") + ">Unpaid (" + row.unpaid_sayi + ")</option>");

            }, "json").done(function () {
                $("#filtre_hoca_odeme").trigger('change')
            });
        }

        function kursOdemeGetir() {
            var data = {
                "_method" : "POST",
                "_token" : "{{csrf_token()}}",
                "filtre_yil" : $("#filtre_yil").val(),
                "filtre_ulke_id" : $("#filtre_ulke_id").val(),
                "filtre_sirket_id" : $("#filtre_sirket_id").val()
            };

            $.post("/cc_past/filtreKursOdemeGetirJSON", data, function (row) {
                $("#filtre_egitim_odeme option:first").prop('selected', true);
                $("#filtre_egitim_odeme option:gt(0)").remove();

                $("#filtre_egitim_odeme").append("<option value='1' " + ("{{$filtre_egitim_odeme}}" == 1 ? " selected" : "") + ">Paid (" + row.paid_sayi + ")</option>");
                $("#filtre_egitim_odeme").append("<option value='2' " + ("{{$filtre_egitim_odeme}}" == 2 ? " selected" : "") + ">Unpaid (" + row.unpaid_sayi + ")</option>");

            }, "json").done(function () {
                $("#filtre_egitim_odeme").trigger('change')
            });
        }
    </script>
@endsection
