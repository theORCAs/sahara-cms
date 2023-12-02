@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Delivery SELECTION (from confirmed list)
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="alert alert-danger">
            {!! nl2br('Important Notes:
1) We need to meet face-to-face before any course assignment, so if we didn’t meet yet, please arrange an appointment with our management.
2) We make course assignment close to course delivery date
3) A course may be CANCELED or POSTPONED. In this case your selection(s) will disappear
4) In case you do not hear from us, assume you assignment is NOT made, feel free
5) We make use of MULTIPLE instructors for each course, so please SELECT day/date suits you most APPROPRIATE.') !!}
        </div>
        <div class="row">
            <div class="col-md-12">
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

                <div class="portlet box red">
                    <div class="portlet-title">

                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
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
                                        <th> # </th>
                                        <th> Course Title and Web Link </th>
                                        <th> Start Date </th>
                                        <th class="text-center"> # of Days </th>
                                        <th class="text-center"> # Pax </th>
                                        <th> Participant/Trainee From (Country) </th>
                                        <th> Select Day(s) topics MOST relevant to your background/experience and Date(s) of your availability </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td title="teklif_id:{{$row->id}}">{{$liste->firstItem() + $key}}</td>
                                            <td>{{$row->egitimKayit->egitimler->kodu." ".$row->egitimKayit->egitimler->adi}}</td>
                                            <td>{{date('d.m.Y', strtotime($row->egitimKayit->egitimTarihi->baslama_tarihi))}}</td>
                                            <td class="text-center">{{$row->egitimKayit->egitimTarihi->egitim_suresi}}</td>
                                            <td class="text-center">{{$row->egitimKayit->katilimcilar->count()}}</td>
                                            <td>{{$row->egitimKayit->sirketUlke->adi}}</td>
                                            <td>
                                                I'm interested <select class="form-control" id="select_secim" data-teklifid="{{$row->id}}" onchange="gunGoster('{{$row->id}}')">
                                                    <option value="">Select</option>
                                                    <option value="2" {{is_array($row->hoca_secim_arr) && $row->hoca_secim_arr[0] != '' ? ' selected' : ''}}>Choose Day/Date</option>
                                                </select>
                                                <div id="day_container" data-teklifid="{{$row->id}}"
                                                     class="{{is_array($row->hoca_secim_arr) && $row->hoca_secim_arr[0] != '' ? "" : " hidden"}}">

                                                    @for($gun = 0; $gun < $row->egitimKayit->egitimTarihi->egitim_suresi; $gun++)
                                                        <div><input type="checkbox" id="gun_secim" data-teklifid="{{$row->id}}"
                                                                    value="{{$gun}}" onclick="gunSecKaydet('{{$row->id}}', '{{$gun}}')"
                                                                    {{ is_array($row->hoca_secim_arr) && in_array( ($gun + 1), $row->hoca_secim_arr ) ? ' checked' : ''}}>
                                                            Day {{$gun + 1}} --> {{date("d.m.Y", strtotime( $row->egitimKayit->egitimTarihi->baslama_tarihi." +$gun day"))}}</div>
                                                        @endfor
                                                </div>
                                                <!--
                                                @foreach($row->kursaTalipler as $t_row)
                                                    <div class="row">
                                                        <div class="col-md-1"><input type="checkbox" class="checkbox"></div>
                                                        <div class="col-md-5">{{$t_row->egitmen->adi_soyadi}}</div>
                                                        <div class="col-md-2">{{str_replace('|', ',', $t_row->secili_gun)}}</div>
                                                        <div class="col-md-4">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    @for($i = 1; $i <= $row->egitimKayit->egitimTarihi->egitim_suresi; $i++)
                                                                        <td>
                                                                            @if(strpos($t_row->secili_gun, "$i") === false)
                                                                                <div class="secili-degil-gun text-center">&nbsp;</div>
                                                                            @else
                                                                                <div class="secili-gun text-center">{{$i}}</div>
                                                                            @endif
                                                                        </td>
                                                                    @endfor
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                -->
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
    <style type="text/css">
        .secili-gun {
            background-color: #2fa360;
            width: 25px;
            height: 25px;
            margin: 1px;
        }
        .secili-degil-gun {
            background-color: #e7505a;
            width: 25px;
            height: 25px;
            margin: 1px;
        }
    </style>
@endsection
@section("js")
    <!-- javascript yüklenir -->
    <script type="text/javascript">
        function gunGoster(teklif_id) {
            var tmp_secim = $("#select_secim[data-teklifid='" + teklif_id + "']").val();
            if(tmp_secim == 2) {
                $("#day_container[data-teklifid='" + teklif_id + "']").removeClass('hidden');
            } else {
                $("#day_container[data-teklifid='" + teklif_id + "']").addClass('hidden');
            }
        }

        function gunSecKaydet(teklif_id, gun) {
            var tmp_islem = $("#gun_secim[data-teklifid='" + teklif_id + "'][value='" + gun + "']").is(":checked") ? "1" : "0";
            var data = {
                "_token" : "{{csrf_token()}}",
                "teklif_id" : teklif_id,
                "gun" : gun,
                "islem" : tmp_islem
            }
            showLoading('', '');
            $.post("/cds_secimYap", data, function (cevap) {
                if(cevap.cvp == 1) {
                    toastr['success']('{{config('messages.islem_basarili')}}', '');
                } else {
                    toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                }
            }, "json").done(function () {
                hideLoading();
            });
            //alert(teklif_id + " - " + gun);
        }
    </script>
@endsection
