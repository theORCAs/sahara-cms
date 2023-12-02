@extends('layouts.main')

@section('content')
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> Invoice PDF
            <small>Create Invoice PDF</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row col-md-8">
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
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-pdf-o"></i> PDF Form </div>
                    <div class="tools hidden">
                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                        <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                        <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                        <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form id="pdfForm" action="" class="form-horizontal" method="post">
                        <input type="hidden" name="egitim_kayit_id" value="{{$egitim_kayit_id}}">
                        @csrf
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Additional Text</label>
                                <div class="col-md-9">
                                    <input type="text" id="ekalan_ust" name="ekalan_ust" class="form-control" value="{{old('ekalan_ust', $ekalan_ust)}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Date</label>
                                <div class="col-md-3">
                                    <input type="text" id="tarih" name="tarih" class="form-control form-control-inline date-picker" value="{{date("d.m.Y", strtotime($tarih))}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Referance No</label>
                                <div class="col-md-3">
                                    <input type="text" id="referans_no" name="referans_no" class="form-control" value="{{$referans_no}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Company</label>
                                <div class="col-md-9">
                                    <input type="text" id="sirket_adi" name="sirket_adi" class="form-control" value="{{$sirket_adi}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Address</label>
                                <div class="col-md-9">
                                    <textarea id="sirket_adres" name="sirket_adres" rows="2" class="form-control">{{$sirket_adres}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Country</label>
                                <div class="col-md-4">
                                    <select id="sirket_ulke_id" name="sirket_ulke_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach($ulkeler as $row)
                                            <option value="{{$row->id}}" @if($row->id == $sirket_ulke_id) selected @endif>{{$row->adi}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Service Description</label>
                                <div class="col-md-9">
                                    <textarea id="aciklama" name="aciklama" class="ckeditor">{{$aciklama}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity</label>
                                <div class="col-md-2">
                                    <input type="text" id="miktar" name="miktar" class="form-control" value="{{$miktar}}">
                                </div>
                                <label class="col-md-2 control-label">Unit Price</label>
                                <div class="col-md-2">
                                    <input type="text" id="ucret" name="ucret" class="form-control" value="{{$ucret}}">
                                </div>
                                <label class="col-md-2 control-label">Amount</label>
                                <div class="col-md-2">
                                    <input type="text" id="ucret_toplam" name="ucret_toplam" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Sub-Total</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="ara_toplam" name="ara_toplam">
                                </div>
                                <label class="col-md-2 control-label">Surcharge/Discount</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="genel_indirim" name="genel_indirim" value="{{$genel_indirim}}">
                                </div>
                                <label class="col-md-2 control-label">Total</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="son_toplam" name="son_toplam">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Currency Type</label>
                                <div class="col-md-3">
                                    <select name="para_birimi" id="para_birimi" class="form-control" onchange="bankaBilgileriGetir()">
                                        @foreach($para_birimleri as $p_birim)
                                            <option value="{{$p_birim->id}}" data-bankabilgi="{{$p_birim->banka_bilgileri}}"
                                                {{$egitim_kayitlar->aktifTeklif->pdfInvoiceBilgileri->para_birimi == $p_birim->id ? " selected" : ""}} >{{$p_birim->adi}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Total Text</label>
                                <div class="col-md-6">
                                    <input type="text" id="genel_ucret_yazisi" name="genel_ucret_yazisi" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Additional Explanation</label>
                                <div class="col-md-9">
                                    <input type="text" id="ekalan_alt" name="ekalan_alt" class="form-control" value="{{$ekalan_alt}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Label</label>
                                <div class="col-md-6">
                                    <input type="text" id="ekalan_1" name="ekalan_1" class="form-control" value="{{$ekalan_1}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Name</label>
                                <div class="col-md-6">
                                    <input type="text" id="isim" name="isim" class="form-control" value="{{$isim}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Position</label>
                                <div class="col-md-6">
                                    <input type="text" id="pozisyon" name="pozisyon" class="form-control" value="{{$pozisyon}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Bank Detail</label>
                                <div class="col-md-9">
                                    <textarea id="banka_detay" name="banka_detay" rows="5" class="form-control">{{$banka_detay}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Add header image to PDF</label>
                                <div class="col-md-3">
                                    <div class="mt-checkbox-list">
                                        <input type="checkbox" id="header_ekle" name="header_ekle" value="1" checked class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Add signature image to PDF</label>
                                <div class="col-md-3">
                                    <div class="mt-checkbox-list">
                                        <input type="checkbox" id="imza_ekle" name="imza_ekle" value="1" checked class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="button" class="btn green" onclick="kaydetKontrol()">Save</button>
                                    <a href="/{{$prefix}}" class="btn default">Cancel</a>
                                    <button type="button" class="btn blue" onclick="pdfOlustur()"><i class="fa fa-file-pdf-o"></i> Create PDF</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
@endsection

@section("css")
    <!-- css dosyalari yuklenir -->
    <!--link href="{{url('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" type="text/css" /-->
    <link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section("js")
    <!-- js dosyalari yuklenir -->
    <!--script src="{{url('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script-->
    <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{url('assets/global/plugins/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd.mm.yyyy'
            });
            $('#miktar,#ucret,#ucret_toplam, #para_birimi, #genel_indirim').change(function (){
                console.log('change');
                hesapla();
            })

            hesapla();
            bankaBilgileriGetir();
        });

        function hesapla() {
            var miktar = parseInt($("#miktar").val() || 0);
            var ucret = parseFloat($("#ucret").val() || 0);
            var ucret_toplam = parseFloat(miktar * ucret).toFixed(2);
            var para_birimi = $("#para_birimi option:selected").text();

            $("#ucret_toplam").val(ucret_toplam);
            $("#ara_toplam").val(ucret_toplam);

            var genel_indirim = parseFloat($("#genel_indirim").val());
            var son_toplam = (parseFloat(ucret_toplam) + parseFloat(genel_indirim)).toFixed(2);
            $("#son_toplam").val(son_toplam);

            var data = {
                '_token' : '{{csrf_token()}}',
                '_method' : 'POST',
                'number' : son_toplam
            }
            $.get("/convertToString/" + son_toplam, function (cevap) {
                $("#genel_ucret_yazisi").val(cevap.string + ' ' + para_birimi);
            }, "json");
        }

        function kaydetKontrol() {
            showLoading('', '');
            $('#pdfForm').attr('action', '/pm_wait/inv_pdf/save/{{$egitim_kayit_id}}');
            $("#pdfForm").submit();
        }
        function pdfOlustur() {
            // showLoading('', '');
            $("#pdfForm").attr('action', '/pm_wait/inv_pdf/inv_pdf_create/{{$egitim_kayit_id}}');
            $("#pdfForm").submit();
        }
        function bankaBilgileriGetir() {
            var banka_bilgileri = $("#para_birimi option:selected").data("bankabilgi");
            $("#banka_detay").html(banka_bilgileri);
        }
    </script>
@endsection
