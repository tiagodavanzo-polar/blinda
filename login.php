<?php
	require_once('config.php');
	
	if(isset($_REQUEST['submit']))
	{
		if($_REQUEST['user'] == "" || $_REQUEST['pass'] == ""){
			echo "<script>alert('Por favor, preencha todos os campos!');</script>";
		}
		else {
			$retorno = Login($_REQUEST['user'], sha1($_REQUEST['pass']));
			
			if($retorno['RETORNO'] == 'OK')
				GeraSessao($retorno['USUARIO']);
			else
				echo "<script>alert('Usuário e/ou senha inválido(s), Tente novamente!');</script>";
		}
	}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>Intranet Polar | Login</title>
    <!-- GLOBAL MAINLY STYLES-->
    <link href="./assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="./assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- THEME STYLES-->
    <link href="assets/css/main.css" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES-->
    <link href="./assets/css/pages/auth-light.css" rel="stylesheet" />
</head>

<body class="bg-silver-300">
    <div class="content">
        <div class="brand">
            <img src="img/logo-cabecalho-original.png" height="100" />
        </div>
        <form action="" method="post">
            <h2 class="login-title">Intranet</h2>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="fa fa-user"></i></div>
                    <input class="form-control" type="user" name="user" placeholder="Usuário" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="fa fa-lock font-16"></i></div>
                    <input class="form-control" type="password" name="pass" placeholder="Senha">
                </div>
            </div>
            <div class="form-group">
                <input class="btn btn-info btn-block" type="submit" name="submit" value="Login" />
            </div>
        </form>
    </div>
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS -->
    <script src="./assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="assets/js/app.js" type="text/javascript"></script>
</body>

</html>