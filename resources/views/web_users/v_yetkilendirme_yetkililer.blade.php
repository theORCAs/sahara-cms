<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i>List of Authorities</div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""  onclick="yetkiliEkleModal('0')"> </a>
            <a href="javascript:;" class="reload" data-original-title="" title="" onclick="yapiYetkiliGetir('{{$yapi_id}}')"> </a>
            <a href="javascript:;" class="remove hidden" data-original-title="" title=""> </a>
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-scrollable">
            <table class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th> # </th>
                    <th> Name</th>
                    <th> Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($yetkili_listesi as $key => $row)
                    @php($sira = $key + 1)
                    <tr>
                        <td>{{$sira}}</td>
                        <td>{{$row->rol_adi}}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->rol_adi}}'" onclick="silmeUyari('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.tooltips').tooltip();
    });
    function yetkiliEkleModal(yetkili_id) {
        var data = {
            "_method" : "GET",
            "yapi_id" : "{{$yapi_id}}"
        };
        showLoading('', '');
        $.post("authorization/" + yetkili_id, data, function (cevap) {
            $("#stack1").data("width", 900).html(cevap).modal("show");
        }).done(function () {
            hideLoading();
        });
    }

    function silmeUyari(yetki_id) {
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
                        showLoading('', '');
                        var data = {
                            "_token" : '{{csrf_token()}}',
                            "_method" : "DELETE"
                        };
                        $.post("authorization/" + yetki_id, data, function (cevap) {
                            if(cevap.cvp == 1) {
                                toastr['success']('{{config('messages.islem_basarili')}}', '');
                                yapiYetkiliGetir('{{$yapi_id}}');
                            } else {
                                toastr['error']("{{config('messages.islem_basarisiz')}} " + cevap.msj, '');
                            }
                        }, "json").done(function () {
                            hideLoading('');
                        });

                    }
                },
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className: 'btn-default'
                }
            }
        })
    }
</script>
