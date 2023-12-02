<div class="portlet box red">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i>{{$modul_adi}}</div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="" onclick="yapiEkleModal('{{$modul_id}}', '')"> </a>
            <a href="javascript:;" class="reload" data-original-title="" title="" onclick="modulYapiGetir('{{$modul_id}}')"> </a>
            <a href="javascript:;" class="remove hidden" data-original-title="" title=""> </a>
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-scrollable">
            <table class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th> # </th>
                    <th> Structure Name</th>
                    <th> Detail</th>
                    <th> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($yapi_listesi as $key => $row)
                    @php($sira = $key + 1)
                <tr onclick="return yapiYetkiliGetir('{{$row->id}}')">
                    <td>{{$sira}}</td>
                    <td>{{$row->adi}}</td>
                    <td>{{$row->aciklama}}</td>
                    <td class="text-center">
                        <a href="javascript:;" class="btn btn-xs btn-danger tooltips" data-container="body" data-placement="right" data-original-title="Delete '{{$row->adi}}'" onclick="silmeUyari2('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @method('PUT')
        </div>
    </div>
</div>
<script type="text/javascript">
    function silmeUyari2(yapi_id) {
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
                        $.post("yapi/" + yapi_id, data, function (cevap) {
                            if(cevap.cvp == 1) {
                                toastr['success']('{{config('messages.islem_basarili')}}', '');
                                modulYapiGetir('{{$modul_id}}');
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
