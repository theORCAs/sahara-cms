<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title"><small>Add Update Authorization</small></h4>
        </div>
        <div class="modal-body">
            <form method="post" id="kaydetForm" action="">
                <input type="hidden" name="yapi_id" id="yapi_id" value="{{$yapi_id}}">
                @csrf
                @method("GET")
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th class="m-grid-col-middle" style="width: 100px;">User Type</th>
                        <td>
                            <select id="rol_id" name="rol_id" class="select2">
                                <option value="">Select</option>
                                @foreach($roller as $row)
                                    <option value="{{$row->id}}">{{$row->adi}}</option>
                                    @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="m-grid-col-middle">User</th>
                        <td>
                            <select id="kullanici_id" name="kullanici_id">
                                <option value="">Select</option>
                                @foreach($kullanicilar as $row)
                                    <option value="{{$row->id}}">{{$row->adi_soyadi}}</option>
                                    @endforeach
                            </select>
                        </td>
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
    $(document).ready(function () {
        $("#rol_id, #kullanici_id").select2();
    });
    function kaydet() {
        showLoading('', '');
        var data = $("#kaydetForm").serialize();
        $.post("authorization/create", data, function (cevap) {
            if(cevap.cvp == 1) {
                toastr['success']("{{config('messages.islem_basarili')}}", "");
                $("#stack1").modal('hide');
                yapiYetkiliGetir('{{$yapi_id}}')
            } else {
                toastr['error'](cevap.msj, "{{config('messages.islem_bsarisiz')}}");
            }
        }, "json").done(function () {
            hideLoading('');
        });
    }
</script>
