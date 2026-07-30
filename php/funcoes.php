<?php
	function ConexaoRM()
	{
		//Usando PDO
		$options = [
			PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
			PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
		  ];

		$con = new PDO('sqlsrv:server='.Config::$db_server.';Database='.Config::$db_name, Config::$db_user, Config::$db_password, $options);

		return $con;
	}

	function ConexaoBlinda($db = 'Polar')
	{
		//Usando PDO
		$options = [
			PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
			PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
		  ];

		$con = new PDO("sqlsrv:server=".Config::$db_server.";Database={$db}", Config::$db_user, Config::$db_password, $options);

		return $con;
	}
	
	function Login($login, $senha)
	{
		$sql = "SELECT ID, USUARIO, SENHA, EMAIL, CODIGO, CODIGOAUX, GRUPO, SETOR FROM TUSUARIO WHERE USUARIO = ? AND SENHA = ?";

		$con = ConexaoBlinda('Conteudo');

		$stmt = $con->prepare($sql);
		$stmt->execute([$login, $senha]);

		$result = $stmt->fetch();

		if($result['USUARIO'] == $login && $result['SENHA'] == $senha)
			return ['RETORNO' => 'OK', 'USUARIO' => $result];
		else
			return ['RETORNO' => 'Erro ao fazer login. Tente novamente.'];
	}

	function GeraSessao($usuario)
	{
		//Iniciando a sessão
		session_start();
		
		//Criando as variáveis para a sessão
		$_SESSION[Config::$uniqid] = [
			'ID' => $usuario['ID'], 
			'USUARIO' => $usuario['USUARIO'],
			'SENHA' => $usuario['SENHA'],
			'EMAIL' => $usuario['EMAIL'],
			'CODIGO' => $usuario['CODIGO'],
			'CODIGOAUX' => $usuario['CODIGOAUX'],
			'GRUPO' => $usuario['GRUPO'],
			'SETOR' => $usuario['SETOR'],
			'BOOKMARK' => (in_array($usuario['SETOR'], ['TI', 'SUPERVISOR'])),
			'PROCESSO_CRITICO' => (in_array($usuario['SETOR'], ['SUPERVISOR']) || in_array($usuario['USUARIO'], ['leon.pereira', 'fabio.diogo', 'william.lemos', 'joao.victor', 'tiago.davanzo', 'nicole.gomes', 'uvendas']))
		];
		
		//Redirecionando para a página principal
		if(in_array($_SESSION[Config::$uniqid]['USUARIO'], ['felipe.medeiros', 'henrique.pires', 'tiago.davanzo']))
			header("Location: engenharia.php");
		else
			header("Location: producao.php");
	}

	function intervaloMeses($mesInicio, $mesTermino)
	{
		$start    = new DateTime(substr($mesInicio, 3, 4).'-'.substr($mesInicio, 0, 2).'-01');
		$end      = (new DateTime(substr($mesTermino, 3, 4).'-'.substr($mesTermino, 0, 2).'-01'))->modify('first day of next month');
		$interval = DateInterval::createFromDateString('1 month');
		$period   = new DatePeriod($start, $interval, $end);

		$meses = [];

		foreach ($period as $dt) {
			array_push($meses, $dt->format("m/Y"));
		}

		return $meses;
	}
	
	function scan_dir($dir) {
		
		if (!is_dir($dir))
			mkdir($dir, 0755, true);
		
		$ignored = ['.', '..', '.svn', '.htaccess']; // -- ignore these file names
		$files = []; //----------------------------------- create an empty files array to play with
		foreach (scandir($dir) as $file) {
			if ($file[0] === '.') continue; //----------------- ignores all files starting with '.'
			if (in_array($file, $ignored)) continue; //-------- ignores all files given in $ignored
			$files[$file] = filemtime($dir . '/' . $file); //-- add to files list
		}
		arsort($files); //------------------------------------- sort file values (creation timestamps)
		$files = array_keys($files); //------------------------ get all files after sorting
		return ($files) ? $files : [];
	}

	function UploadEngenharia($arquivo, $linha, $nomeArquivo)
	{
		$ext = mb_strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
		$validExt = ['eml'];
		
		if(!in_array($ext, $validExt)){
			return 'O anexo aceita apenas o formato eml.';
		}

		$uploadDir = "../upload/aprovacao/{$linha}/";
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0777, true);
		}

		if(move_uploaded_file($arquivo['tmp_name'], "{$uploadDir}anexo-{$nomeArquivo}")){
			return 'OK';
		}
		
		return 'Erro ao fazer o upload. Entre em contato com o administrador.';
	}

	function NomeArquivo($nomeArquivo){
		$nomeArquivo = str_replace(' ', '-', $nomeArquivo);
		$nomeArquivo = RemoverAcento($nomeArquivo);
		$nomeArquivo = mb_strtolower($nomeArquivo);

		return $nomeArquivo;
	}

	function RemoverAcento($string) 
	{
		$from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇºª°";
		$to = "aaaaeeiooouucAAAAEEIOOOUUC---";
		
		$keys = array();
		$values = array();
		preg_match_all('/./u', $from, $keys);
		preg_match_all('/./u', $to, $values);
		$mapping = array_combine($keys[0], $values[0]);
		return strtr($string, $mapping);
	}

	function formataValor($numero, $casas = 2) {
		// Formata o número com as casas decimais e separadores definidos
		$formatado = number_format($numero, $casas, ',', '.');
		
		// Remove os zeros que estão à direita da vírgula
		$formatado = rtrim($formatado, '0');
		
		// Remove a vírgula caso tenha ficado no final (número inteiro)
		return rtrim($formatado, ',');
	}
