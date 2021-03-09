<link href="<?= base_url() ?>front_assets/css/private-sessions.css?v=201" rel="stylesheet">
<script>
    $(function () {

        $("#main_menu_top_bar").prepend('' +
                '<li style="margin-top: 30px;margin-right: 45px;">' +
                '<span style="font-weight: bold;font-size: 30px;">Time Left: <span class="countdown-timer">00:00:00<i><span>' +
                '</li>');


        function formatTime(seconds) {
            var h = Math.floor(seconds / 3600),
                    m = Math.floor(seconds / 60) % 60,
                    s = seconds % 60;
            if (h < 10)
                h = "0" + h;
            if (m < 10)
                m = "0" + m;
            if (s < 10)
                s = "0" + s;
            return h + ":" + m + ":" + s;
        }

        function timer() {
            count--;
            if (count < 0) {
                // window.location.replace("/LES/sessions/");
                return;
            }
            ;
            $('.countdown-timer').html(formatTime(count));
        }

        var count = <?= strtotime($sessions->end_time) - time() ?>;
        var counter = setInterval(timer, 1000);
    });
</script>

<main role="main" class="container text-center">
    <div>
        <h1>
            <?= $sessions->session_title ?>
        </h1>
        <small class="lead"><?= $sessions->sessions_description ?></small>
    </div>
</main>

<div class="row m-t-20 camera-feeds">



    <div class="col-md-3 localvideo-div">
        <div class="videoCover" style="display: none;"></div>
        <span class="name-tag">You</span>
        <video id="localVideo" autoplay muted playsinline width="100%"></video>
        <!-- <div class="soundbar"><span class="currentVolume"></span></div> -->
    </div>

</div>
<div class="col-md-12 control-icons-col" style="display: none;">
    <div class="feed-control-icons" style="display: inline;">

        <div class="mute-mic-btn" style="display: inline;">
            <i class="fa fa-microphone fa-3x mute-mic-btn-icon" aria-hidden="true" style="color:#12b81c;"></i>

        </div>

        <div class="cam-btn" style="display: inline;">
            <i class="fa fa-video-camera fa-3x cam-btn-icon" aria-hidden="true" style="color:#12b81c;"></i>
        </div>

        <div class="share-screen-btn" style="display: inline;" onclick="window.open('<?=base_url()?>private_sessions/sharescreen/<?=$sessions->sessions_id?>', '_blank');">
            <i class="fa fa-desktop fa-3x share-screen-btn-icon" aria-hidden="true" style="color:#6f8de3;"></i>
        </div>

        <div class="leave-btn" style="display: inline;">
            <i class="fa fa-sign-out fa-3x leave-btn-icon" aria-hidden="true" style="color:#e36f7a;"></i>
        </div>
    </div>
</div>

<input id="muteStatus" type="hidden" value="unmuted">
<input id="camStatus" type="hidden" value="on">


<script>
    function extract(variable) {
        for (var key in variable) {
            window[key] = variable[key];
        }
    }

    var round_table_id = <?= $sessions->sessions_id ?>;
    var atttendee_name = "<?= $this->session->userdata('fullname') ?>";
    var attendee_id = "<?= $this->session->userdata('cid') ?>";
</script>
<!--- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.3.5/dist/sweetalert2.all.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

<script type="text/javascript">
    $.get("<?=base_url()?>socket_config.php", function (data) {
        var config = JSON.parse(data);
        extract(config);

        $.getScript( "<?= base_url() ?>front_assets/js/private-sessions.js?v=201" )
            .done(function( script, textStatus ) {
                pageReady();
            })
            .fail(function( jqxhr, settings, exception ) {
                alert( "Unable to load required files: "+exception );
            });


    });
</script>
<div class="modal fade" id="push_notification" tabindex="-1" role="modal" aria-labelledby="modal-label" aria-hidden="true" style="display: none; text-align: left; right: unset;">
    <input type="hidden" id="push_notification_id" value="">
    <div class="modal-dialog">
        <div class="modal-content" style="border: 1px solid #ef9d45;">
            <div class="modal-body">
                <div class="row" style="padding-top: 10px; padding-bottom: 20px;">
                    <div class="col-sm-12">
                        <div style="color:#ef9d45; font-size: 16px; font-weight: 800; " id="push_notification_message"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close push_notification_close" style="padding: 10px; color: #fff; background-color: #ef9d45; opacity: 1;" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        push_notification_admin();
        //setInterval(push_notification_admin, 4000);

        $('.push_notification_close').on('click', function () {
            var push_notification_id = $("#push_notification_id").val();
            $.ajax({
                url: "<?= base_url() ?>push_notification/push_notification_close",
                type: "post",
                data: {'push_notification_id': push_notification_id},
                dataType: "json",
                success: function (data) {
                }
            });
        });

        function push_notification_admin()
        {
            var push_notification_id = $("#push_notification_id").val();

            $.ajax({
                url: "<?= base_url() ?>push_notification/get_push_notification_admin",
                type: "post",
                dataType: "json",
                success: function (data) {
                    if (data.status == "success") {
                        if (push_notification_id == "0") {
                            $("#push_notification_id").val(data.result.push_notification_id);
                        }
                        if (push_notification_id != data.result.push_notification_id) {
                            $.ajax({
                                url: "<?= base_url() ?>push_notification/get_push_notification_admin_check_status",
                                type: "post",
                                data: {'push_notification_id': data.result.push_notification_id},
                                dataType: "json",
                                success: function (dt) {
                                    if (dt.status == "success") {
                                        $("#push_notification_id").val(data.result.push_notification_id);
                                        $('#push_notification').modal('show');
                                        $("#push_notification_message").text(data.result.message);
                                    }
                                }
                            });
                        }
                    } else {
                        $('#push_notification').modal('hide');
                    }
                }
            });
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {

<?php
if ($sessions->sessions_id == 128) {
    ?>



<?php } ?>


    });
</script>
