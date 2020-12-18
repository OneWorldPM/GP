<!DOCTYPE html>
<html lang="en">
<head>
    <title>Faux SKO 21</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="<?= base_url() ?>front_assets/images/FAUXSKO21/fauxsko_icon_transparent.png"/>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/css/util.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>front_assets/login_template/css/main.css?v2">
    <!--===============================================================================================-->
</head>
<body>

<div class="limiter">
    <div class="container-login100" style="background-image: url(<?=base_url()?>front_assets/images/mohammed-shaheen-Fo44off83V8-unsplash.jpg)">
        <div class="wrap-login100">
            <div class="login100-form-title"">
					<img src="<?=base_url()?>front_assets/images/FAUXSKO21/SKO_2021_WebHero_1920w.png" style="width: 100%;height: auto;">
            </div>

            <form class="login100-form validate-form" method="post" action="<?= base_url() ?>login/authentication">
                <div class="wrap-input100 validate-input m-b-26" data-validate="Username is required">
                    <span class="label-input100">Username</span>
                    <input class="input100" type="text" name="email" placeholder="Enter username">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-18" data-validate = "Password is required">
                    <span class="label-input100">Password</span>
                    <input class="input100" type="password" name="password" placeholder="Enter password">
                    <span class="focus-input100"></span>
                </div>

                <div class="flex-sb-m w-full p-b-30">
<!--                    <div class="contact100-form-checkbox">-->
<!--                        <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">-->
<!--                        <label class="label-checkbox100" for="ckb1">-->
<!--                            Remember me-->
<!--                        </label>-->
<!--                    </div>-->

                    <div>
                        <a href="#" class="txt1">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn">
                        Login
                    </button>
                </div>
            </form>
        <a href="<?=base_url()?>">
            <button class="btn btn-secondary">
                Back
            </button>
        </a>
        </div>
    </div>
</div>

<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/vendor/bootstrap/js/popper.js"></script>
<script src="<?=base_url()?>front_assets/login_template/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/vendor/daterangepicker/moment.min.js"></script>
<script src="<?=base_url()?>front_assets/login_template/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
<script src="<?=base_url()?>front_assets/login_template/js/main.js"></script>

</body>
</html>
