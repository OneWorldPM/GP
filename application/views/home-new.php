<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?= base_url() ?>front_assets/gp/gp-logo-white.png">

    <title>Gravity Productions</title>

    <!-- Bootstrap core CSS -->
    <link href="<?=base_url()?>front_assets/login_template/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?=base_url()?>front_assets/login_template/vendor/bootstrap/css/narrow-jumbotron.css" rel="stylesheet">
</head>

<style>
    @media (max-width: 750px) {
        .text-right {
            text-align: center !important;
        }

        .text-left {
            text-align: center !important;
        }
    }
</style>

<body style="background-image: url(<?=base_url()?>front_assets/gp/668940634.jpg);background-size: cover;background-repeat: no-repeat;">

<div class="container">
    <div class="jumbotron" style="background-color: #002f7000;">
        <video autoplay muted loop class="gpLogoAnimated" style="width: 100%; height: auto;">
            <source src="<?=base_url()?>front_assets/gp/Animated_GP_Logo_Cropped-low_size.mp4" type="video/mp4">
        </video>
    </div>

    <div class="row text-center">
        <div class="col-md-4">
            <div style="border: 1px solid white;border-radius: 25px;padding: 10px;margin-bottom: 10px;cursor: pointer;background-color: #ffffff30;" onclick="location.href='<?= base_url() ?>sessions'">
                <i class="fas fa-chalkboard-teacher" style="font-size: 125px !important; color: #009ce9;"></i>
                <div style="margin-top: 15px;color: white;font-size: 25px;font-weight: bold;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;">SESSIONS</div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="border: 1px solid white;border-radius: 25px;padding: 10px;margin-bottom: 10px;cursor: pointer;background-color: #ffffff30;" onclick="location.href='<?= base_url() ?>lounge'">
                <i class="fas fa-couch" style="font-size: 125px !important; color: #009ce9;"></i>
                <div style="margin-top: 15px;color: white;font-size: 25px;font-weight: bold;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;">LOUNGE</div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="border: 1px solid white;border-radius: 25px;padding: 10px;margin-bottom: 10px;cursor: pointer;background-color: #ffffff30;" onclick="location.href='<?= base_url() ?>sponsor'">
                <i class="fas fa-compress-arrows-alt" style="font-size: 125px !important; color: #009ce9;"></i>
                <div style="margin-top: 15px;color: white;font-size: 25px;font-weight: bold;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;">EXHIBITS</div>
            </div>
        </div>
    </div>

    <div class="row text-center">
        <div class="col-md-6 text-right">
            <div class="text-center" style="border: 1px solid white;border-radius: 25px;padding: 10px;margin-bottom:5px;width: 240px;display: inline-block;cursor: pointer;background-color: #ffffff30;">
                <i class="fas fa-info-circle" style="font-size: 95px !important; color: #009ce9;"></i>
                <div style="margin-top: 15px;color: white;font-size: 20px;font-weight: bold;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;">HELP DESK</div>
            </div>
        </div>
        <div class="col-md-6 text-left">
            <div class="text-center" style="border: 1px solid white;border-radius: 25px;padding: 10px;margin-bottom:5px;width: 240px;display: inline-block;cursor: pointer;background-color: #ffffff30;" onclick="location.href='/support'">
                <i class="fas fa-cog" style="font-size: 95px !important; color: #009ce9;"></i>
                <div style="margin-top: 15px;color: white;font-size: 20px;font-weight: bold;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;">TECHNICAL SUPPORT</div>
            </div>
        </div>
    </div>

</div> <!-- /container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?=base_url()?>front_assets/login_template/vendor/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
<script src="https://kit.fontawesome.com/fd91b3535c.js" crossorigin="anonymous"></script>
</body>
</html>
