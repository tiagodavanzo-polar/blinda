<?php
	/* Destruimos a sessão e redirecionamos 
	o usuário para a página index.php */
	session_start();
	session_destroy();
	header("location: login.php");
	exit;