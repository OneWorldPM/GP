<link href="<?= base_url() ?>front_assets/css/private-sessions.css?v=200" rel="stylesheet">

<main role="main" class="container" style="text-align: center;">

    <div class="starter-template">
        <h1><?= $sessions->session_title ?></h1>
    </div>

    <button class="sharescreen-btn btn btn-success">Start Sharing Screen</button>

</main>

<input type="hidden" class="screenshare-status" value="not-sharing">


<!--- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.3.5/dist/sweetalert2.all.min.js"></script>

<!--- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />
<script>
    function extract(variable) {
        for (var key in variable) {
            window[key] = variable[key];
        }
    }
    var page_link = $(location).attr('href');
    var user_id = "<?= $this->session->userdata("cid") ?>";
    var base_url = "<?= base_url() ?>";
    var user_name = "<?= $this->session->userdata('fullname') ?>";
    user_name = (user_name == '') ? 'No Name' : user_name;

    var meeting_id = "<?=$sessions->sessions_id?>";

    $.get("<?=base_url()?>socket_config.php", function (data) {
        var config = JSON.parse(data);
        extract(config);

        $.getScript( "<?= base_url() ?>front_assets/js/private-sessions-share-screen.js?v=200", function( data, textStatus, jqxhr )
        {
        });
    });
</script>
