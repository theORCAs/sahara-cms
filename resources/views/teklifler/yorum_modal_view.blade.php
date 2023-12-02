<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title"><b>Comment</b></h4>
        </div>
        <div class="modal-body">
            <p>{{$baslik}}</p>
            <form class="form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            {{$yorum_tarih}}
                            <textarea name="yorum" id="yorum" rows="5" class="form-control">{{$yorum}}</textarea>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-outline dark sbold" id="closeBtn">Close</button>
            <button type="button" class="btn green" onclick="yorumKaydet()">Save</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

    });


    function yorumKaydet() {
        var data = {
            "_token" : "{{csrf_token()}}",
            "yorum" : $("#yorum").val(),
            'teklif_id' : "{{$teklif_id}}",
        }
        showLoading('', '');
        $.post("/{{$prefix}}/yorumYazModalSendJson", data, function (cevap) {
            if(cevap.cvp == "1" && $("#yorum").val() != "") {
                $("#yorum_href_{{$teklif_id}}").addClass('font-red');
            }else{
                $("#yorum_href_{{$teklif_id}}").removeClass('font-red');
            }
            if(cevap.cvp == "1") {
                $("#closeBtn").trigger('click');
            }
        }, "json").done(function () {
            hideLoading('');
        });
    }
</script>
