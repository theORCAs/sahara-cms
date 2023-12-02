@extends('layouts.main')

@section('content')

    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title"> User Types
            <small>Add / Update User Type</small>
        </h3>
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="col-md-6">

            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    <h4 class="alert-heading"><i class="fa fa-warning"></i> Error</h4>
                    {{ $error }}
                </div>
            @endforeach
            @if($bilgi->id > 0)
                <form class="form-horizontal" role="form" method="post" action="/user_type/{{$bilgi->id}}">
                @method("put")
            @else
                <form class="form-horizontal" role="form" method="post" action="/user_type">
            @endif
                @csrf
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Role Type</label>
                        <div class="col-md-9">
                            <input type="text" name="adi" class="form-control" placeholder="Enter role type" value="{{$bilgi->adi}}">
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">Submit</button>
                            <button type="button" class="btn default" onclick="window.location.href='/user_type'">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
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
