<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title"><b>Switch User</b></h4>
        </div>
        <div class="modal-body">
            <form class="form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Roles</label>
                        <div class="col-md-9">
                            <select class="" id="rol_id" onchange="kullanicilariGetir()">
                                <option value="">Select</option>
                                @foreach($roller_listesi as $row)
                                    <option value="{{$row->id}}" {{ $row->id == 4 ? ' selected' : '' }}>{{$row->adi}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group last">
                        <label class="col-md-3 control-label">Users</label>
                        <div class="col-md-9">
                            <select id="kullanici_id">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-outline dark sbold">Close</button>
            <button type="button" class="btn green" onclick="gecisYap()">Save</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#rol_id, #kullanici_id").select2();
        setTimeout(function () {
            kullanicilariGetir();
        }, 500);

    });

    function kullanicilariGetir() {
        var rol_id = $("#rol_id").val();
        $("#kullanici_id option:gt(0)").remove();
        $("#kullanici_id option:first").prop("selected", true);
        $("#kullanici_id").trigger("change");
        if(rol_id == "") {
            return;
        }
        showLoading('', '#stack1');
        var data = {
            "_token" : '{{csrf_token()}}',
            "rol_id" : rol_id
        };
        $.post("/su_kullaniciGetir", data, function (cevap) {
            $.each(cevap, function (i, row) {
                $("#kullanici_id").append("<option value='" + row.id + "'>" + (row.unvan_adi != null ? row.unvan_adi : "") + " " + row.adi_soyadi + "</option>");
            });
        }, "json").done(function () {
            hideLoading('#stack1');
        });
    }

    function gecisYap() {
        if($("#kullanici_id").val() == "") {
            toastr['error']("Please select user", '');
            return;
        }
        var data = {
            "_token" : "{{csrf_token()}}",
            "kullanici_id" : $("#kullanici_id").val()
        }

        $.post("/su_GecisYap", data, function (cevap) {
            if(cevap.cvp == 1) {
                window.location.href = "/";
            }
        }, "json")
    }
</script>
