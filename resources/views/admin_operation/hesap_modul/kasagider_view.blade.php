@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('ama_spent_add')) ? "" : "hidden"}}">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="/{{$prefix}}/create"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Account Module (Admin)
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
        <form method="post" action="/{{$prefix}}/search">
            @csrf
            <div class="m-heading-1 border-green m-bordered col-md-8">
                <h3>Filters</h3>
                <p class="col-md-2">
                    <select id="s_filtre_yil" name="s_filtre_yil" class="form-control select2">
                        @foreach($yillar_liste as $row)
                            <option value="{{$row->yil}}" {{old('s_filtre_yil', $s_filtre_yil) == $row->yil ? " selected" : ""}}>{{$row->yil}}</option>
                            @endforeach
                    </select>
                </p>
                <p class="col-md-3">
                    <select id="s_gider_kalem_id" name="s_gider_kalem_id" class="form-control select2">
                        <option value="">Select Expense Type</option>
                        @foreach($gider_turleri as $row)
                            <option value="{{$row->id}}" {{old('s_gider_kalem_id', $s_gider_kalem_id) == $row->id ? " selected" : ""}}>{{$row->adi}}</option>
                            @endforeach
                    </select>
                </p>
                <p class="col-md-4">
                    <select id="s_ilgili_kisi" name="s_ilgili_kisi" class="form-control select2">
                        <option value="">Select Related Person</option>
                        @foreach($personel_listesi as $row)
                            <option value="{{$row->id}}" {{old('s_ilgili_kisi', $s_ilgili_kisi) == $row->id ? " selected" : ""}}>{{$row->adi_soyadi}}</option>
                            @endforeach
                    </select>
                </p>

                <p class="col-md-2">
                    <button type="submit" class="btn green">Search</button>
                    <button type="button" class="btn red" onclick="javascript:window.location.href='/{{$prefix}}'">Reset</button>
                </p>

            </div>
        </form>
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
                                        <th colspan="5"></th>
                                        <th class="text-right">Total (Given)</th>
                                        <th class="text-right">Total (Spent)</th>
                                        <th class="text-right">Difference (TL)</th>
                                    </tr>
                                    <tr>
                                        <th colspan="5"></th>
                                        <th class="text-right font-green">@money_format($totals['given'])</th>
                                        <th class="text-right font-red">@money_format($totals['spent'])</th>
                                        <th class="text-right {{(float)$totals['fark'] > 0 ? 'font-green' : 'font-red'}}">@money_format($totals['fark'])</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div>#</div>
                                            <div>ID</div>
                                        </th>
                                        <th> Date </th>
                                        <th> Expense Type </th>
                                        <th> Personel </th>
                                        <th> Explanation </th>
                                        <th class="text-right font-green"> Given (TL) </th>
                                        <th class="text-right font-red"> Spent (TL) </th>
                                        <th class="text-center"> Action </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($liste as $key => $row)
                                        <tr>
                                            <td>
                                                <div>{{$liste->firstItem() + $key}}</div>
                                                <div>ID: {{$row->id}}</div>
                                            </td>
                                            <td>{{date('d.m.Y', strtotime($row->tarih))}}</td>
                                            <td>{{$row->giderKalem->adi}}</td>
                                            <td>{{$row->ilgiliKisi->adi_soyadi}}</td>
                                            <td>{{$row->aciklama}}</td>
                                            <td class="text-right font-green">
                                                @if((float)$row->gider_tl > 0)@money_format($row->gider_tl) <i class="fa fa-try"></i>@endif
                                            </td>
                                            <td class="text-right font-red">
                                                @if((float)$row->per_gider_tl > 0)@money_format($row->per_gider_tl) <i class="fa fa-try"></i>@endif
                                            </td>
                                            <td class="text-center">
                                                @if(Auth::user()->isAllow('ama_spent_edit'))
                                                    <a href="/{{$prefix}}/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->giderKalem->adi." ".$row->aciklama}}'"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(Auth::user()->isAllow('ama_spent_del'))
                                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->giderKalem->adi." ".$row->aciklama}}'" onclick="silmeKontrol('{{$row->id}}', '/{{$prefix}}')"><i class="fa fa-trash"></i></a>
                                                @endif
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

@endsection
@section("js")
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2();
        })
    </script>
@endsection
