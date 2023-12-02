<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>Sahara Training | CMS</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="SAHARA GROUP Content Management System" name="description" />
    <meta content="SAHARA GROUP IT" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/simple-line-icons/simple-line-icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/uniform/css/uniform.default.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{url('assets/global/plugins/bootstrap-toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url(('assets/global/plugins/select2/css/select2.min.css'))}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/global/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{url('assets/global/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{url('assets/global/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{url('assets/layouts/layout/css/layout.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/layouts/layout/css/themes/light2.min.css')}}" rel="stylesheet" type="text/css" id="style_color" />
    <link href="{{url('assets/layouts/layout/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="{{url('/favicon.ico')}}" />
    @yield("css")
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="{{url('/')}}">
                <img src="{{url('assets/layouts/layout/img/logo.png')}}" alt="logo" class="logo-default" /> </a>
            <div class="menu-toggler sidebar-toggler"> </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN NOTIFICATION DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                @if(session('ROL_ID') == 3)
                    @php($toplam_uyari_sayi = intval($registration_form_sayi) + intval($course_outline_sayi) + intval($stok_uyari_sayi))
                @if(intval($toplam_uyari_sayi) > 0)
                <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="icon-bell"></i>
                        <span class="badge badge-default"> {{intval($toplam_uyari_sayi)}} </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="external">
                            <h3>
                                <span class="bold">Pending</span> notifications</h3>
                            <a href="javascript:;">--</a>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                @if(intval($registration_form_sayi) > 0)
                                <li>
                                    <a href="/pm_wait">
                                        <span class="time">--</span>
                                        <span class="details">
                                            <span class="label label-sm label-icon label-success">
                                                <i class="fa fa-plus"></i>
                                            </span> {{$registration_form_sayi}} Registration Form
                                        </span>
                                    </a>
                                </li>
                                @endif
                                @if(intval($course_outline_sayi) > 0)
                                <li>
                                    <a href="/osnp_view">
                                        <span class="time">--</span>
                                        <span class="details">
                                            <span class="label label-sm label-icon label-danger">
                                                <i class="fa fa-bolt"></i>
                                            </span> {{$course_outline_sayi}} NEW Course Outline
                                        </span>
                                    </a>
                                </li>
                                @endif
                                @if(intval($stok_uyari_sayi) > 0)
                                <li>
                                    <a href="/sm_view">
                                        <span class="time">--</span>
                                        <span class="details">
                                            <span class="label label-sm label-icon label-warning">
                                                <i class="fa fa-bell-o"></i>
                                            </span> {{$stok_uyari_sayi}} Stok Control
                                        </span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif
                @endif
                <!-- END NOTIFICATION DROPDOWN -->
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle" src="{{url('assets/layouts/layout/img/avatar_small.png')}}" />
                        <span class="username username-hide-on-mobile"> {{ Auth::user()->adi_soyadi." (".session('ROL_ADI').")" }} </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        @if(Auth::user()->isAllow('switch_user'))
                        <li>
                            <a href="javascript:;" onclick="switchUserModal()">
                                <i class="fa fa-users"></i> Switch User</a>
                        </li>
                        @endif
                        @if(session()->has('SW_KULLANICI_ID'))
                        <li class="bg-red">
                            <a href="javascript:;" onclick="switchUserKapat()"><i class="fa fa-times"></i> End Swicth User</a>
                        </li>
                        @endif
                        <li class="divider"> </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="icon-key"></i>  {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->

            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"> </div>
<!-- END HEADER & CONTENT DIVIDER -->
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- BEGIN SIDEBAR -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="false" data-slide-speed="200" style="padding-top: 20px">
                <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                <li class="sidebar-toggler-wrapper hide">
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <div class="sidebar-toggler"> </div>
                    <!-- END SIDEBAR TOGGLER BUTTON -->
                </li>
                <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
                <li class="sidebar-search-wrapper">
                    <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                    <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
                    <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
                    <form class="sidebar-search  sidebar-search-bordered" action="" method="POST" style="margin: 4px 18px;">
                        <a href="javascript:;" class="remove">
                            <i class="icon-close"></i>
                        </a>
                        <div class="input-group">
                            <select class="form-control select2" id="menu-search" style="width: 100%" onchange="menuSearch()">
                                <option value="/">Search</option>
                                @foreach(session("moduller") as $row)
                                    @if($row->level1 == 1 || $row->ana_kategori == 1)
                                        @continue
                                        @endif
                                    <option value="/{{$row->menu_url}}">{{$row->adi}}</option>
                                    @endforeach
                            </select>
                            <span class="input-group-btn">
                                <a href="javascript:;" class="btn">
                                    <i class="icon-magnifier"></i>
                                </a>
                            </span>
                        </div>
                    </form>
                    <!-- END RESPONSIVE QUICK SEARCH FORM -->
                </li>
                @php($key_tmp = 0)
                @php($tmp_modul_id = 0)
                @php($sub_acik = 0)

                @foreach(session("moduller") as $row)
                    @php($sub_menu_arr = explode(",", $row->sub_menu_ids))

                    @if($row->ana_kategori == 1)
                        @if($sub_acik == 1)
                        </ul>
                        @php($sub_acik = 0)
                        @endif
                        <li class="heading" style="padding: 10px;">
                            <h5 class="font-red" style="margin-top: 2px; margin-bottom: 2px;">{{$row->adi}}</h5>
                        </li>
                    @endif
                    @if($row->level1 == 1)
                        @if($sub_acik == 1)
                            </ul>
                            @php($sub_acik = 0)
                            @endif
                        <li class="nav-item @if(in_array($aktif_modul_id, $sub_menu_arr)) active open @endif">
                            <a href="{{"/".$row->menu_url}}" class="nav-link nav-toggle">
                                <i class="{{$row->icon}}"></i>
                                <span class="title">{{$row->adi}}</span>
                                @if(in_array($aktif_modul_id, $sub_menu_arr)) <span class="selected"></span> @endif
                                @if($row->sub_menu > 0)<span class="arrow @if(in_array($aktif_modul_id, $sub_menu_arr)) open @endif"></span>@endif
                            </a>
                        @if($row->sub_menu == 0)
                            </li>
                        @else
                            <ul class="sub-menu">
                            @php($sub_acik = 1)
                            @endif
                        @endif
                    @if($row->level2 == 1)
                        <li class="nav-item @if($aktif_modul_id == $row->id) active @endif">
                            <a href="{{"/".$row->menu_url}}" class="nav-link">
                                <i class="{{$row->icon}}"></i>
                                <span class="title">{{$row->adi}}</span>
                            </a>
                        </li>
                        @endif
                @endforeach
                @if($sub_acik == 1)
                    @php("</ul>")
                    @endif
            </ul>
            <!-- END SIDEBAR MENU -->
            <!-- END SIDEBAR MENU -->
        </div>
        <!-- END SIDEBAR -->
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        @yield("content")
    </div>
    <!-- END CONTENT -->
    <form id="deleteForm" method="post" action="">
        @csrf()
        @method("delete")
    </form>
    <div id="stack1" class="modal fade" data-width="900" data-backdrop="static" data-keyboard="false">

    </div>

    <div id="stack2" class="modal fade" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">

    </div>
    <div id="stack3" class="modal fade" tabindex="-1" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">

    </div>
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner"> {{date("Y")}} &copy; SAHARA GROUP - Committed to inspire
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
<!--[if lt IE 9]>
<script src="{{url('assets/global/plugins/respond.min.js')}}"></script>
<script src="{{url('assets/global/plugins/excanvas.min.js')}}"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="{{url('assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/js.cookie.min.js')}}" type="text/javascript"></script>
<!--script src="{{url('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js')}}" type="text/javascript"></script-->
<script src="{{url('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/jquery.blockui.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/uniform/jquery.uniform.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{URL('assets/global/plugins/bootbox/bootbox.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
<!-- script src="{{url('assets/global/plugins/jquery-ui/jquery-ui.min.js')}}" type="text/javascript"></script--> <!-- tooltip için kapatıldı neresi patladi bilmiyorum -->
<script src="{{url('assets/global/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{url('assets/global/scripts/app.min.js')}}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<!--script src="{{url('assets/pages/scripts/ui-bootbox.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/pages/scripts/ui-modals.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/pages/scripts/components-select2.min.js')}}" type="text/javascript"></script-->
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{url('assets/layouts/layout/scripts/layout.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/layouts/layout/scripts/demo.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/layouts/global/scripts/quick-sidebar.min.js')}}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
<script type="text/javascript">
    function showLoading(mesaj, hedef) {
        App.blockUI({
            boxed: true,
            overlayColor: '#000000',
            message: (mesaj != "" ? mesaj : 'Processing...'),
            target : (hedef != "" ? hedef : "")
        });
        setTimeout(function () {
            hideLoading();
        }, 20000);
    }
    function hideLoading(hedef) {
        App.unblockUI(hedef);
    }

    function silmeKontrol(id, route) {
        bootbox.dialog({
            size: "small",
            // title: "<i class='fa fa-warning'></i>",
            message: "<i class='fa fa-warning'></i> Are you sure?",
            onEscape: false,
            backdrop: true,
            centerVertical: true,
            buttons: {
                confirm: {
                    label: '<i class="fa fa-trash"></i> Delete',
                    className: 'btn-danger',
                    callback: function(result){
                        showLoading('');
                        $("#deleteForm").attr("action", route + "/" + id);
                        $("#deleteForm").submit();
                    }
                },
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className: 'btn-default'
                }
            }
        })
    }

    function switchUserModal() {
        var data = {
            "_method" : "GET"
        };
        showLoading('', '');
        $.post("/switchUser", data, function (cevap) {
            $("#stack1").data("width", 900).html(cevap).modal("show");
        }).done(function () {
            hideLoading();
        });
    }

    function switchUserKapat() {
        var data = {
            "_method" : "GET"
        };
        showLoading('', '');
        $.post("/endSwitchUser", data, function (cevap) {
            if(cevap.cvp == 1) {
                window.location.href = "/";
            }
        }, "json").done(function () {
            hideLoading();
        });
    }

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    function menuSearch() {
        var url = $("#menu-search").val();
        if(url != "") {
            window.location.href=url;
        }
    }

    $(document).ready(function () {
        $(".select2").select2();
    });
</script>
@yield("js")
</body>

</html>
