@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Instructor Background on Course Selection
            <small>{{$alt_baslik}}</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <form method="post" action="/ibcs_view/search">
            @csrf
            <div class="m-heading-1 border-green m-bordered col-md-8">
                <h3>Training Category</h3>
                <p class="col-md-7">
                    <select id="kategori_id" name="kategori_id" class="select2 form-control">
                        <option value="">Select</option>
                        @foreach($kategori_liste as $row)
                            <option value="{{$row->id}}" {{$secili_kategori_id == $row->id ? ' selected' : ''}}>{{$row->adi}}</option>
                            @endforeach
                    </select>
                </p>
                <p class="col-md-1">
                    <button type="submit" class="btn green">Search</button>
                </p>
            </div>
        </form>
        <div class="row">
            <div class="col-md-5">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">Ratings List</div>
                        <div class="tools hidden">
                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="javascript:;" class="remove" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            @if(sizeof($background_liste) == 0)
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ config('messages.listelenecek_kayit_yok')}}</div>
                            @else
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Trainer </th>
                                        <th> # of Course </th>
                                        <th class="text-center"> Last Update </th>
                                        <th> Action </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($background_liste as $key => $row)
                                        <tr>
                                            <td>{{$key + 1}}</td>
                                            <td>{{$row->adi_soyadi}}</td>
                                            <td>{{$row->egitim_sayisi}}</td>
                                            <td>{{$row->son_guncelleme != '' ? date('d.m.Y', strtotime($row->son_guncelleme)) : ''}}</td>
                                            <td><a href="javascript:;" onclick="return listeGetir('{{$row->id}}')">List</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div id="listeContainer"></div>
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
        $(document).ready(function () {
            $(".select2").select2();
        });

        function listeGetir(egitmen_id) {
            var data = {
                '_token' : "{{csrf_token()}}",
                'egitmen_id' : egitmen_id
            };

            showLoading('', '#listeContainer');
            $.post('/ibcs_view/listeGetir', data, function (cevap) {
                $("#listeContainer").html(cevap);
            }).done(function () {
                hideLoading('#listeContainer');
            });
        }
    </script>

@endsection
