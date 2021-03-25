<?php
if (isset($_GET['testing']))
{
    //$hosts = explode(',', $sessions->breakout_hosts);
    echo "<pre>"; print_r($admittedParticipants); exit("</pre>");
}

?>

<?php $hosts = explode(',', $sessions->breakout_hosts); ?>
<?php $isHost = (in_array($this->session->userdata('cid'), $hosts))?true:false ?>

<link href="<?= base_url() ?>front_assets/breakout/css/breakout-rooms.css?v=2" rel="stylesheet">

<link href="<?= base_url() ?>front_assets/breakout/cam-grids/styles.css" rel="stylesheet">
<script src="<?= base_url() ?>front_assets/breakout/cam-grids/script.js"></script>

<script src="https://kit.fontawesome.com/fd91b3535c.js" crossorigin="anonymous"></script>

<script>
    function extract(variable) {
        for (var key in variable) {
            window[key] = variable[key];
        }
    }

    var round_table_id = <?= $sessions->sessions_id ?>;
    var breakout_meeting_id = <?= $sessions->sessions_id ?>;
    var atttendee_name = "<?= $this->session->userdata('fullname') ?>";
    var attendee_id = "<?= $this->session->userdata('cid') ?>";
    var isHost = <?=($isHost)?'true':'false'?>;
    var base_url = "<?=base_url()?>";
</script>
<!--- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.3.5/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

<script type="text/javascript">
    $.get("<?=base_url()?>socket_config.php", function (data) {
        var config = JSON.parse(data);
        extract(config);

        $.getScript( "<?= base_url() ?>front_assets/breakout/js/breakout-room.js?v=1" )
            .done(function( script, textStatus ) {
                var config = {'host': 'https://socket.yourconference.live'};
                socket = io.connect(config.host, {secure: true});

                <?php if (in_array($this->session->userdata('cid'), $hosts) || in_array($this->session->userdata('cid'), $admittedParticipants)): ?>
                participantsList();
                joinParticipantsList();
                pageReady();
                redirectToARoom()
                <?php endif; ?>

                <?php if (in_array($this->session->userdata('cid'), $hosts)): ?>
                checkWaitingList();
                getOpenedRoomsStatus();
                getCreatedRoomsStatus();
                <?php endif; ?>

                <?php
                if (
                !in_array($this->session->userdata('cid'), $hosts) &&
                !in_array($this->session->userdata('cid'), $rejectedParticipants) &&
                !in_array($this->session->userdata('cid'), $admittedParticipants)
                ): ?>
                joinWaitingRoom();
                <?php endif; ?>
            })
            .fail(function( jqxhr, settings, exception ) {
                alert( "Unable to load required files: "+exception );
            });


    });
</script>

<style>
    #rightPaneParent{
        width: 100%;
        height: 60px;
        position: absolute;
        white-space: nowrap;
        margin-left: unset;
        margin-right: unset;
        z-index: 5;
    }

    .collapsible {
        background-color: #002f70;
        color: white;
        cursor: pointer;
        padding: 2px 12px 2px 35px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
    }

    .active, .collapsible:hover {
        background-color: #074191;
    }

    .collapsible-content {
        width: auto;
        padding: 0 18px;
        display: none;
        overflow: hidden;
        background-color: #f1f1f1;
    }

    #Dish div {
        background-image: url('<?=base_url()?>front_assets/breakout/cam-grids/loading.gif');
    }
</style>
<div class="row" id="rightPaneParent">
    <div id="rightPane" class="float-right">

        <?php if ($isHost): ?>
            <div id="waitingListDiv">
                <button type="button" class="collapsible">Waiting List (<span id="waitingListCount">0</span>) <i class="fas fa-caret-down"></i> </button>
                <div class="collapsible-content">
                    <ul id="waitingList" class="list-group">
                        <li class="list-group-item">Empty</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div id="participantsListDiv" style="margin-top: 1px;">
            <button type="button" class="collapsible">Participants (<span id="participantsListCount">0</span>) <i class="fas fa-caret-down"></i> </button>
            <div class="collapsible-content">
                <ul id="participantsList" class="list-group">
                </ul>
            </div>
        </div>
    </div>
</div>

<main role="main" class="container text-center">
    <div>
        <h2>
            <?= $sessions->session_title ?>
        </h2>
    </div>
</main>

<?php if (in_array($this->session->userdata('cid'), $rejectedParticipants)): ?>
    <div id="waitForAdmissionDiv" class="col-md-12 m-t-10 text-center">
        <span style="color:red;">Host rejected your request and permanently removed you.</span>
    </div>



<?php elseif (!in_array($this->session->userdata('cid'), $hosts) && !in_array($this->session->userdata('cid'), $admittedParticipants)): ?>
    <div id="waitForAdmissionDiv" class="col-md-12 m-t-10 text-center">
        <span style="color:red;">Please wait until host admits you.</span>
    </div>

<?php else: ?>

    <style>
        .badge{
            float: unset !important;
            margin-right: unset !important;
        }

        .participant-remove-btn{
            cursor: pointer;
            background-color: #ea0000;
        }

        .admitWaitingUser{
            background-color: #3d993d;
            cursor: pointer;
        }

        .rejectWaitingUser{
            background-color: #d54444;
            cursor: pointer;
        }

        .removeUser{
            background-color: #d54444;
            cursor: pointer;
        }
        .camera-feeds div{

        }
    </style>

    <div id="camera-container">
        <div id="Dish">

            <div class="Camera localVideoParent">
                <span class="name-tag">You<?=($isHost)?' [host] ':''?> </span>
                <video id="localVideo" autoplay muted playsinline width="100%"></video>
            </div>

        </div>
    </div>

    <div class="col-md-12 control-icons-col" style="display: none;">
        <div class="feed-control-icons" style="display: inline;">

            <button class="mute-mic-btn btn btn-sm btn-success"><i class="fa fa-microphone fa-2x" aria-hidden="true"></i> Mute</button>

            <button class="cam-btn btn btn-sm btn-success"><i class="fa fa-video-camera fa-2x" aria-hidden="true"></i> Stop Video</button>

            <button class="share-screen-btn btn btn-sm btn-info" onclick="window.open('<?=base_url()?>breakout_room/sharescreen/<?=$sessions->sessions_id?>', '_blank');"><i class="fa fa-desktop fa-2x" aria-hidden="true"></i> Screen Share</button>

            <?php if ($isHost): ?>
                <button class="breakout-rooms-btn btn btn-sm btn-primary" style="background:#0961d7;"><i class="fas fa-table fa-2x" aria-hidden="true"></i> Breakout Rooms</button>
            <?php endif; ?>

            <button class="leave-btn btn btn-sm btn-danger"><i class="fa fa-sign-out fa-2x" aria-hidden="true"></i> Leave</button>

        </div>
    </div>

    <input id="muteStatus" type="hidden" value="unmuted">
    <input id="camStatus" type="hidden" value="on">

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


    <?php if ($isHost): ?>
        <!-- Breakout rooms modal -->
        <div class="modal fade" id="breakoutRoomsModal" tabindex="-1" role="dialog" aria-labelledby="breakoutRoomsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="breakoutRoomsModalLabel">Breakout Rooms</h5>
                    </div>
                    <div id="breakoutRoomsModalBody" class="modal-body">

                        <div id="notEnoughParticipantsMsg">
                            <span style="font-weight: 700;">You need at least 2 participants other than the host to create rooms.</span>
                        </div>

                        <div id="createRoomsDiv" style="display: none;">
                            <span>
                                Assign <span id="participantsCountForRooms">0</span> participants into
                            <select id="numberOfRoomsSelect">
                                <option value="2">2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                            </select> rooms:</span><br>
                            <fieldset>
                                <div class="roomCreationTypeParent">
                                    <input type="radio" class="radio" name="roomCreationType" value="automatic" checked/>
                                    <label class="roomCreationTypeLabel">Automatically</label>
                                    <input type="radio" class="radio" name="roomCreationType" value="manual"/>
                                    <label class="roomCreationTypeLabel">Manually</label>
                                </div>
                            </fieldset>

                            <span>You can edit user allocation per rooms once rooms are created.</span>

                        </div>

                        <div id="createdRoomsDiv" style="display: none;">

                            <span class="mainRoomLabel">Main Room (this)</span>
                            <div class="mainRoom" room-id="main">
                            </div>

                            <div id="automaticRoomsDiv">

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <span id="participantPerRoomInfo" style="margin-top: 20px;float: left;">1 participants per room</span>
                        <button type="button" class="create-room-btn btn btn-sm btn-info" style="background:#0961d7;" disabled>Create Rooms</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Minimize</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        $(document).ready(function () {

            $('[data-toggle="tooltip"]').tooltip();

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

        });

        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                } else {
                    content.style.display = "block";
                }
            });
        }
    </script>

<?php endif; ?>
