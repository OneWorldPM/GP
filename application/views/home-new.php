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
    <link href="<?= base_url() ?>front_assets/login_template/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?= base_url() ?>front_assets/login_template/vendor/bootstrap/css/narrow-jumbotron.css"
          rel="stylesheet">
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

<body style="background-image: url(<?= base_url() ?>front_assets/gp/668940634.jpg);background-size: cover;background-repeat: no-repeat;">

<div class="container">
    <div class="jumbotron" style="background-color: #002f7000;">
        <video autoplay muted loop class="gpLogoAnimated" style="width: 100%; height: auto;">
            <source src="<?= base_url() ?>front_assets/gp/Animated_GP_Logo_Cropped-low_size.mp4" type="video/mp4">
        </video>
    </div>
    <div style="margin:auto !important">
        <div class="row text-center">
            <div class="col-md-2">
            </div>
            <div class="col-md-2">
                <div style="background-color: #002F70; cursor: pointer; border-radius: 25px" onclick="location.href='<?= base_url() ?>sessions'">
                    <i class="fas fa-chalkboard-teacher"
                       style="font-size: 125px !important; color: white; margin-top:10px;"></i>
                    <div style="margin-top: 15px;color: white;font-size: 25px;font-weight: bold;">SESSIONS</div>
                </div>
            </div>
            <div class="col-md-2">
                <div style="background-color: #002F70; cursor: pointer; border-radius: 25px" onclick="location.href='<?= base_url() ?>lounge'">
                    <i class="fas fa-couch" style="font-size: 135px !important; color: white; "></i>
                    <div style="margin-top: 15px;color: white;font-size: 25px;font-weight: bold;">LOUNGE</div>
                </div>
            </div>
            <div class="col-md-2">
                <div style="background-color: #002F70; cursor: pointer; border-radius: 25px" onclick="location.href='<?= base_url() ?>sponsor'">
                    <i class="fas fa-compress-arrows-alt"
                       style="font-size: 125px !important; color: white; margin-top:10px;"></i>
                    <div style="margin-top: 15px;color: white;font-size: 25px;font-weight: bold;">EXHIBITS</div>
                </div>
            </div>
            <div class="col-md-2">
                <div style="background-color: #002F70; cursor: pointer; border-radius: 25px" onclick="location.href='/support'">
                    <i class="fas fa-cog" style="font-size: 105px !important; color: white; margin-top:10px;"></i>
                    <div style="margin-top: 15px;color: white;font-size: 20px;font-weight: bold;">TECHNICAL SUPPORT
                    </div>
                </div>
                <div class="col-md-2">
                </div>
            </div>
        </div>
    </div>


</div> <!-- /container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?= base_url() ?>front_assets/login_template/vendor/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
<script src="https://kit.fontawesome.com/fd91b3535c.js" crossorigin="anonymous"></script>
</body>
</html>
