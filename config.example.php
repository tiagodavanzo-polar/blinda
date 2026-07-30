<?php
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	class Config
	{
		public static $uniqid = "";
		public static $db_server = 'HOST';
		public static $db_user = 'USUARIO';
		public static $db_password = 'SENHA';
		public static $db_name = "BASE_DE_DADOS";
	}

	const SHOWENGENHARIA = ['USUARIO1', 'USUARIO2'];
	
	// Carregando os arquivos necessários...
	require_once 'php/funcoes.php';
	
	require_once 'syscode/bl_pv_producao.php';
	require_once 'syscode/bl_blinda.php';
	require_once 'syscode/BlindaEngenharia.php';
	require_once 'syscode/BlindaCompras.php';
