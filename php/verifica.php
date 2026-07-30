<?php
if(!isset($_SESSION))
   session_start();
//Verificando a existência da sessão
if(!(
   isset($_SESSION[Config::$uniqid]) && 
   isset($_SESSION[Config::$uniqid]['ID']) && 
   isset($_SESSION[Config::$uniqid]['USUARIO']) && 
   isset($_SESSION[Config::$uniqid]['SENHA']) &&
   isset($_SESSION[Config::$uniqid]['GRUPO'])
))
	{
      header("Location: logout.php");
      exit;
   }