<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title"><b>{{$modul_adi}}</b> <small>Add Update Structure</small></h4>
        </div>
        <div class="modal-body">
            <form method="post" id="kaydetForm" action="">
                <input type="hidden" name="modul_id" id="modul_id" value="{{$modul_id}}">
                @csrf
                @method("GET")
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th class="m-grid-col-middle">Structure</th>
                        <td><input type="text" name="yapi_adi" id="yapi_adi" class="form-control"></td>
                    </tr>
                    <tr>
                        <th class="m-grid-col-middle">Explain</th>
                        <td><textarea name="aciklama" id="aciklama" class="form-control"></textarea></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-outline dark sbold">Close</button>
            <button type="button" class="btn green" onclick="kaydet()">Save</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    function kaydet() {
        var data = $("#kaydetForm").serialize();
        $.post("yapi/create", data, function (cevap) {
            if(cevap.cvp == 1) {
                toastr['success']("{{config('messages.islem_basarili')}}", "");
                $("#stack1").modal('hide');
                modulYapiGetir('{{$modul_id}}');
            } else {
                toastr['error'](cevap.msj, "{{config('messages.islem_bsarisiz')}}");
            }
        }, "json");
    }
</script>
