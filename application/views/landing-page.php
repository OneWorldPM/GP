<!doctype html>
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
    <link href="<?=base_url()?>front_assets/login_template/css/cover.css" rel="stylesheet">
</head>

<style>
    .gpLogoAnimated {
        display: block;
        position: fixed;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
    }

    .mobileLogo{
        display: none;
    }
    @media (max-width: 1600px) {
        .mobileLogo{
            display: block;
        }

        .gpLogoAnimated{
            display: none;
        }

        .mb-auto{
            margin-bottom: 20px !important;
        }

        body{
            background-image: url(<?=base_url()?>front_assets/gp/668940634.jpg);
            background-size: cover;
            background-repeat: no-repeat;
        }

        .cover-container{
            margin: 0 !important;
            position: absolute !important;
            top: 35% !important;
        }
    }
</style>

<body class="text-center"">

<video autoplay muted loop class="gpLogoAnimated">
    <source src="<?=base_url()?>front_assets/gp/Animated_GP_Logo-low_size.mp4" type="video/mp4">
</video>

<div class="cover-container d-flex h-100 p-3 mx-auto flex-column" style="position: fixed;margin-top: 15%;">
    <header class="masthead mb-auto">
        <div class="mobileLogo">
            <video autoplay muted loop style="width: 100%; height: auto;">
                <source src="<?=base_url()?>front_assets/gp/Animated_GP_Logo_Cropped-low_size.mp4" type="video/mp4">
            </video>
        </div>
    </header>

    <main role="main" class="inner cover">
<!--        <h1 class="cover-heading">Faux SKO Sales Kickoff 2021</h1>-->
        <p class="lead">
            <a href="<?=base_url()?>login" class="btn btn-lg btn-secondary shadow-sm" style="color: #0e306c;">Login</a>
            <a href="<?=base_url()?>register" class="btn btn-lg btn-secondary shadow-sm" style="color: #0e306c;">Register</a>
        </p>
        <p class="lead">
            <span>Powered by One World</span>
        </p>
    </main>

    <footer class="mastfoot mt-auto">
    </footer>
</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?=base_url()?>front_assets/login_template/vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="<?=base_url()?>front_assets/login_template/vendor/jquery/jquery-slim.min.js"></script>
<script src="<?=base_url()?>front_assets/login_template/vendor/bootstrap/js/popper.js"></script>
<script src="<?=base_url()?>front_assets/login_template/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
