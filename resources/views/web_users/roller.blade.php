@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar {{(Auth::user()->isAllow('wu_ut_add')) ? "" : "hidden"}}">
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="user_type/create"><i class="fa fa-plus"></i> Add new</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE BAR -->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> User Types
            <small>Define system user types</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="col-md-6">
            <?php
            /*
            if(Auth::user()->isAllow('silmeYetki'))
                echo "aaaabbbbb";
            print_r(Session::all());
            */
            ?>
            @if(Session::has("msj"))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> Success</h4>
                    {{Session::get("msj")}}
                </div>
                @endif
            <div class="table-scrollable">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Active Users #</th>
                        <th>Passive Users #</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roller as $key => $row)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$row->adi}}</td>
                            <td class="text-center">{{$row->aktifKullaniciSayisi->sayisi}}</td>
                            <td class="text-center">{{$row->pasifKullaniciSayisi->sayisi}}</td>
                            <td class="text-center">
                                @if(Auth::user()->isAllow('wu_ut_edit'))
                                    <a href="/user_type/{{$row->id}}/edit" class="btn btn-xs btn-primary tooltips" data-container="body" data-placement="right" data-original-title="Update '{{$row->adi}}'"><i class="fa fa-pencil"></i></a>
                                @endif
                                @if(Auth::user()->isAllow('wu_ut_del'))
                                    <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->adi}}'" onclick="silmeKontrol('{{$row->id}}', '/user_type')"><i class="fa fa-trash"></i></a>
                                >@endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
