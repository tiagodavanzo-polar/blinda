<?php 
	ob_start();
	session_start();
	require_once("config.php");
	require_once("php/verifica.php");
	
	$string = explode('.', $_SESSION[Config::$uniqid]['USUARIO']);
    $nome = ucfirst($string[0]);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>Intranet Polar | Home</title>
	<link rel="icon" type="image/x-icon" href="img/favicon.png">
    <!-- GLOBAL MAINLY STYLES-->
    <link href="./assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="./assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- PLUGINS STYLES-->
    <link href="./assets/vendors/DataTables/datatables.min.css" rel="stylesheet" />
    <!-- THEME STYLES-->
    <link href="./assets/css/main.min.css" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES-->
	<style>
	
	#tbPrecos th, #tbPrecos td
	{
		text-align: center;
	}

	.v
	{
		color: green;
		font-weight: bold;
		text-align: center;
	}

	.nv
	{
		color: red;
		font-weight: bold;
		text-align: center;
	}

	.table td, .table th
	{
		padding: 0.3rem
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		color:#FFFFFF;
		background-color: #212529;
	}

	</style>
</head>

<body class="fixed-navbar sidebar-mini">
    <div class="page-wrapper">
        <!-- START HEADER-->
        <header class="header">
            <div class="page-brand">
                <!--<a class="link" href="index.html">
                    <span class="brand">Intranet Polar</span>
                </a>-->
				<span class="brand">Intranet Polar</span>
            </div>
            <div class="flexbox flex-1">
                <!-- START TOP-LEFT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li>
                        <a class="nav-link sidebar-toggler js-sidebar-toggler"><i class="ti-menu"></i></a>
                    </li>
                </ul>
                <!-- END TOP-LEFT TOOLBAR-->
                <!-- START TOP-RIGHT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li class="dropdown dropdown-user">
                        <a class="nav-link dropdown-toggle link" data-toggle="dropdown">
                            <img src="./assets/img/admin-avatar.png" />
                            <span></span><?php echo $nome; ?><i class="fa fa-angle-down m-l-5"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="logout.php"><i class="fa fa-power-off"></i>Logout</a>
                        </ul>
                    </li>
                </ul>
                <!-- END TOP-RIGHT TOOLBAR-->
            </div>
        </header>
        <!-- END HEADER-->

		<?php require_once 'menu.php'; ?>
        
        <div class="content-wrapper" style="min-height:1100px !important;">
			<div class="page-heading">
                <?php echo '<h2 class="page-title">Bem-vindo(a), '.$nome.'!</h2>'; ?>
            </div>
			<p>&nbsp;</p>
			<div class="row"></div>
            <footer class="page-footer">
                <div class="to-top"><i class="fa fa-angle-double-up"></i></div>
            </footer>
        </div>
    </div>
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS-->
    <script src="./assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
	<script src="./assets/vendors/DataTables/datatables.min.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="assets/js/app.min.js" type="text/javascript"></script>
</body>

</html>