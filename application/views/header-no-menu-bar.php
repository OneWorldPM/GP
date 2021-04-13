
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="shortcut icon" href="<?= base_url() ?>front_assets/gp/gp-logo-white.png">
        <title>Gravity Productions</title>
        <!-- Bootstrap Core CSS -->
        <link href="<?= base_url() ?>front_assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= base_url() ?>front_assets/vendor/fontawesome/css/font-awesome.min.css" type="text/css" rel="stylesheet">
        <link href="<?= base_url() ?>front_assets/vendor/animateit/animate.min.css" rel="stylesheet">

        <!-- Vendor css -->
        <link href="<?= base_url() ?>front_assets/vendor/owlcarousel/owl.carousel.css" rel="stylesheet">
        <link href="<?= base_url() ?>front_assets/vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

        <!-- Template base -->
        <link href="<?= base_url() ?>front_assets/css/theme-base.css?v=6" rel="stylesheet">

        <!-- Template elements -->
        <link href="<?= base_url() ?>front_assets/css/theme-elements.css" rel="stylesheet">

        <!-- Responsive classes -->
        <link href="<?= base_url() ?>front_assets/css/responsive.css" rel="stylesheet">

        <!-- [if lt IE 9]>
        <script src="https://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif] -->

        <!-- Template color -->
        <link href="<?= base_url() ?>front_assets/css/color-variations/blue.css?v=2" rel="stylesheet" type="text/css" media="screen" title="blue">

        <!-- LOAD GOOGLE FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,800,700,600%7CRaleway:100,300,600,700,800" rel="stylesheet" type="text/css" />

        <!-- CSS CUSTOM STYLE -->
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>front_assets/css/custom.css?v=2" media="screen" />
        <!--VENDOR SCRIPT-->
        <script src="<?= base_url() ?>front_assets/vendor/jquery/jquery-1.11.2.min.js"></script>
        <script src="<?= base_url() ?>front_assets/vendor/plugins-compressed.js"></script>

        <link href="<?= base_url() ?>assets/alertify/alertify.core.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url() ?>assets/alertify/alertify.default.css" rel="stylesheet" type="text/css" />
        <style>
            @media (min-width: 1200px){
                .container {
                    width: 1300px;
                }
            }
            @media (min-width: 1400px){
                .container {
                    width: 1450px;
                }
            }
            @media (min-width: 1600px){
                .container {
                    width: 1600px;
                }
            }
            @media (min-width: 1800px){
                .container {
                    width: 1700px;
                }
            }

            @media (min-width: 1900px){
                .container {
                    width: 1850px;
                }
            }
            @media (min-width: 2200px){
                .container {
                    width: 2400px;
                }
            }

            .button.black-light {
                border-color: #22a5da;
            }

            .logo{
                cursor: pointer;
            }

            .logo2 {
                border-left: 1px solid black;
                float: left;
                padding-left: 15px;
                margin-top: 8px;
            }

            .logo2 img {
                object-fit: contain;
                width: 79px;
                height: 50px;
            }
            .logo2 span {
                position: absolute;
                top: -6px;
                font-family: sans-serif;
                font-size: 11px;
            }

            #mainMenu2 {
                margin-right: 100px;
                margin-top: 20px !important;
            }

            #mainMenu2 .nav {
                height: max-content;
            }

            #mainMenu2 ul li a {
                line-height: 0 !important;
                height: max-content !important;
                font-weight: 600;
                font-size: 13px;
                color: white;
            }

            #mainMenu2 ul li {
                margin-right: 0px;
            }

            #mainMenu2 ul li a:hover {
                background-color: transparent;
                color: #22a5da;
                cursor: pointer;
            }

            #mainMenu2 ul li:hover ul {
                display: block !important;
            }

            .toolboxCustomDrop {

                display: none;
                background-color: white;
                position: absolute;
                width: 180px;
                box-shadow: 0 0 12px -6px black;
                padding: 11px !important;
                position: absolute;
                z-index: 124214214;
            }

            .toolboxCustomDrop li {
                margin-right: 0 !important;

                font-weight: bold;
            }

            .toolboxCustomDrop li a {
                color: #7a7a7a !important;
                cursor: pointer;
            }

            .toolboxCustomDrop li a:hover {
                color: #22a5da !important;

            }

            .toolboxCustomDrop li i {
                font-size: 19px !important;
            }

            .toolboxCustomDrop li:nth-of-type(1n+2) {
                margin-top: 12px;
            }

            @media screen and (max-width: 1290px) {
                #header-wrap {
                    padding: 16px 30px;
                }
            }

            @media screen and (max-width: 1200px) {
                #header-wrap {
                    padding: 16px 10px;
                }

                #header .container {
                    width: 100% !important;
                }

                #mainMenu2 {
                    margin-right: 0;
                }

                #mainMenu2 ul li {
                    margin-right: 10px;
                }
            }

            @media screen and (max-width: 992px) {
                .parallax {
                    margin-top: 0;
                }

                #mainMenu2 .nav {
                    background-color: white;
                    height: 200px;
                    width: 200px;
                    float: right;
                }

                .nav-main-menu-responsive {
                    height: max-content;
                    line-height: 0;
                }

            }

            @media screen and (max-width: 493px) {
                .logo2 {
                    margin-left: 5px;
                }

                .logo2 img {
                    width: 115px;
                }
            }
        </style>

    </head>
    <body class="wide">
        <!-- WRAPPER -->
        <div class="wrapper">
            <!-- HEADER -->

            <!-- END: HEADER -->
