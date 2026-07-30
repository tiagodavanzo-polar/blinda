<?php

class BlindaEngenharia
{
	
	private $linha;
    private $pr;
    private $numeromov;
    private $sku;
    private $numitempedido;
    private $referencia;
    private $eng;
    private $pre_eng;
    private $ckl_eng;
    private $doc_eng;
    private $aprov_eng;
    private $bom_eng;
    private $sku_eng;
    private $vend_eng;
    private $cli_eng;

	// Construtor opcional para agilizar a criação do objeto
    public function __construct(array $dados = []) {
        foreach ($dados as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

	// Métodos Getter e Setter Dinâmicos (ou você pode declarar um por um)
    public function __set($atribute, $value) {
        $this->$atribute = $value;
    }

    public function __get($atribute) {
        return $this->$atribute;
    }

	public static function getAll($status = 'carteira', $db = 'Polar')
	{
		$where = ($status == 'carteira') ? "WHERE ENGENHARIA IN ('I','P','FP')" : "WHERE ENGENHARIA = 'FT'";
	
		$sql = 
		"SELECT 
			[LINHA], [PRIORIDADE], 
			CASE 
				WHEN PRIORIDADE = 1 THEN 'ALTA'
				WHEN PRIORIDADE = 2 THEN 'MÉDIA'
				WHEN PRIORIDADE = 3 THEN 'NORMAL'
				WHEN PRIORIDADE = 4 THEN 'BAIXA'
			END AS [PR], 
			[MOV], [CCUSTO], [STATUSMOV], ENG.NUMEROMOV AS [NUMEROMOV], ENG.NUMITEMPEDIDO AS [NUMITEMPEDIDO], ENG.SKU AS [SKU], [ITEM], ENG.REFERENCIA AS [REFERENCIA], 
			[QTDE], [DT ENTREGA], FORMAT([DT ENTREGA], 'dd/MM/yyyy') AS [DATAENTREGA], I.DATAOTIFCOMPRAS, I.DATACOMPRAS, I.DATAOTIFPRODUCAO, I.DATAPRODUCAO, I.TEMPOAPROVACAO, I.DATAOTIFAPROV,
			[ENGENHARIA], [PRE_ENG], [CKL_ENG], [DOC_ENG], [APROV_ENG], [BOM_ENG], [SKU_ENG], [VEND_ENG], [CLI_ENG]
		FROM TBLINDAENGENHARIA ENG
		LEFT JOIN TTB1BLINDAITEM I (NOLOCK) ON I.NUMEROMOV = ENG.NUMEROMOV AND I.POCLIENTE = ENG.POCLIENTE AND I.NUMITEMPEDIDO = ENG.NUMITEMPEDIDO AND I.SKU = ENG.SKU
		$where
		ORDER BY [PRIORIDADE], [DT ENTREGA]";

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute();

		$itens = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

		if($row['NUMEROMOV'] != '316381' || ($row['NUMEROMOV'] == '316381' && $_SESSION[Config::$uniqid]['USUARIO'] == 'tiago.davanzo'))
		{
			$itens[] = [
				'LINHA' => $row['LINHA'],
				'PR' => substr($row['PR'], 0, 1),
				'MOV' => $row['MOV'],
				'CCUSTO' => $row['CCUSTO'],
				'HTMLNUMEROMOV' => '<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="Actions(\''.$row['NUMEROMOV'].'\',\''.$row['SKU'].'\', \''.$row['REFERENCIA'].'\', \''.$row['NUMITEMPEDIDO'].'\')">'.$row['NUMEROMOV'].'</a>',
				'NUMEROMOV' => $row['NUMEROMOV'],
				'STATUSMOV' => $row['STATUSMOV'],
				'SKU' => ($row['NUMEROMOV'] != '316381') ? "{$row['SKU']} - {$row['REFERENCIA']}" : "<b>[IGNORAR LINHA TESTE]</b> {$row['SKU']} - {$row['REFERENCIA']}",
				'NUMITEMPEDIDO' => $row['NUMITEMPEDIDO'],
				'REFERENCIA' => $row['REFERENCIA'],
				'QTDE' => $row['QTDE'],
				//'ACAO' => $btnPlanejamento,
				'DATAENTREGA' => $row['DATAENTREGA'],
				'DATAOTIFCOMPRAS' => ($row['DATAOTIFCOMPRAS'] != '') ? (new DateTime($row['DATAOTIFCOMPRAS']))->format('d/m/Y') : $row['DATAOTIFCOMPRAS'],
				'DATACOMPRAS' => ($row['DATACOMPRAS'] != '') ? (new DateTime($row['DATACOMPRAS']))->format('d/m/Y') : $row['DATACOMPRAS'],
				'DATAOTIFPRODUCAO' => ($row['DATAOTIFPRODUCAO'] != '') ? (new DateTime($row['DATAOTIFPRODUCAO']))->format('d/m/Y') : $row['DATAOTIFPRODUCAO'],
				'DATAPRODUCAO' => ($row['DATAPRODUCAO'] != '') ? (new DateTime($row['DATAPRODUCAO']))->format('d/m/Y') : $row['DATAPRODUCAO'],
				'TEMPOAPROVACAO' => $row['TEMPOAPROVACAO'],
				'DATAOTIFAPROV' => ($row['TEMPOAPROVACAO'] > 0 && $row['DATAOTIFAPROV'] != '') ? (new DateTime($row['DATAOTIFAPROV']))->format('d/m/Y') : $row['DATAOTIFAPROV'],
				'ENGENHARIA' => $row['ENGENHARIA'],
				'PRE_ENG' => $row['PRE_ENG'],
				'CKL_ENG' => $row['CKL_ENG'],
				'DOC_ENG' => $row['DOC_ENG'],
				'APROV_ENG' => $row['APROV_ENG'],
				'BOM_ENG' => $row['BOM_ENG'],
				'SKU_ENG' => $row['SKU_ENG'],
				'VEND_ENG' => $row['VEND_ENG'],
				'CLI_ENG' => $row['CLI_ENG']
			];
		}
		}

		return $itens;
	}

	public static function getRow($numeromov, $sku, $item, $db = 'Polar')
	{
		$sql = 
		"SELECT 
			[LINHA], [PRIORIDADE], 
			CASE 
				WHEN PRIORIDADE = 1 THEN 'ALTA'
				WHEN PRIORIDADE = 2 THEN 'MÉDIA'
				WHEN PRIORIDADE = 3 THEN 'NORMAL'
				WHEN PRIORIDADE = 4 THEN 'BAIXA'
			END AS [PR], 
			[CODTMV], [MOV], [CCUSTO], [NUMEROMOV], [POCLIENTE], [STATUSMOV], [NUMITEMPEDIDO], [SKU], [ITEM], [REFERENCIA], [QTDE], 
			[DATASTART], [DATACHECKLIST], [DT ENTREGA], FORMAT([DT ENTREGA], 'dd/MM/yyyy') AS [DATAENTREGA], ISNULL(UF, CODETD) AS [UF], [TEMPOUF],
			[ENGENHARIA], [PRE_ENG], [CKL_ENG], [DOC_ENG], [APROV_ENG], [BOM_ENG], [SKU_ENG], [VEND_ENG], [CLI_ENG]
		FROM TBLINDAENGENHARIA
		WHERE NUMEROMOV = ?
		AND SKU = ?
		AND NUMITEMPEDIDO = ?";

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute([$numeromov, $sku, $item]);

		$item = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

			$item = [
				'LINHA' => $row['LINHA'],
				'PR' => substr($row['PR'], 0, 1),
				'CODTMV' => $row['CODTMV'],
				'MOV' => $row['MOV'],
				'CCUSTO' => $row['CCUSTO'],
				'HTMLNUMEROMOV' => '<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="Actions(\'Processo '.$row['NUMEROMOV'].' | '.$row['SKU'].' - '.$row['REFERENCIA'].' | Item '.$row['NUMITEMPEDIDO'].'\')">'.$row['NUMEROMOV'].'</a>',
				'NUMEROMOV' => $row['NUMEROMOV'],
				'POCLIENTE' => $row['POCLIENTE'],
				'STATUSMOV' => $row['STATUSMOV'],
				'SKU' => $row['SKU'],
				'NUMITEMPEDIDO' => $row['NUMITEMPEDIDO'],
				'REFERENCIA' => $row['REFERENCIA'],
				'QTDE' => $row['QTDE'],
				'DATASTART' => $row['DATASTART'],
				'DATACHECKLIST' => $row['DATACHECKLIST'],
				'DATAENTREGA' => $row['DATAENTREGA'],
				'DATAOTIF' => $row['DT ENTREGA'],
				'UF' => $row['UF'],
				'TEMPOUF' => $row['TEMPOUF'],
				'ENGENHARIA' => $row['ENGENHARIA'],
				'PRE_ENG' => $row['PRE_ENG'],
				'CKL_ENG' => $row['CKL_ENG'],
				'DOC_ENG' => $row['DOC_ENG'],
				'APROV_ENG' => $row['APROV_ENG'],
				'BOM_ENG' => $row['BOM_ENG'],
				'SKU_ENG' => $row['SKU_ENG'],
				'VEND_ENG' => $row['VEND_ENG'],
				'CLI_ENG' => $row['CLI_ENG']
			];
		}

		return $item;
	}

	public static function getGrupos($db = 'Polar')
	{
		$sql = 
		"SELECT GR.CODTB1FAT AS [CODTB1FAT], DESCRICAO, CAST(BL.TEMPO AS INT) AS [TEMPO], (SELECT P.CODTB1FAT + ' - ' + P.DESCRICAO FROM CorporeRM.dbo.TTB1 P WHERE CODCOLIGADA = 1 AND P.CODTB1FAT = SUBSTRING(GR.CODTB1FAT, 0, 5)) AS [PAI] 
		FROM CorporeRM.dbo.TTB1 GR
		INNER JOIN TTB1BLI BL (NOLOCK) ON BL.CODTB1FAT = GR.CODTB1FAT
		WHERE GR.CODTB1FAT LIKE '5%'
		AND LEN(GR.CODTB1FAT) >= 9
		AND GR.CODCOLIGADA = 1";

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute();

		$grupos = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

			$grupos[] = [
				'CODTB1FAT' => $row['CODTB1FAT'],
				'DESCRICAO' => $row['DESCRICAO'],
				'TEMPO' => $row['TEMPO'],
				'PAI' => $row['PAI']
			];
		}

		return $grupos;
	}

	public static function getGrupo($grupo, $db = 'Polar')
	{
		$sql = 
		"SELECT 
			GR.CODTB1FAT AS [CODTB1FAT], DESCRICAO, BL.TEMPO, TEMPO44, TEMPO45, TEMPO46, TEMPO47,
			(SELECT P.CODTB1FAT + ' - ' + P.DESCRICAO FROM CorporeRM.dbo.TTB1 P WHERE CODCOLIGADA = 1 AND P.CODTB1FAT = SUBSTRING(GR.CODTB1FAT, 0, 5)) AS [PAI] 
		FROM CorporeRM.dbo.TTB1 GR
		INNER JOIN TTB1BLI BL (NOLOCK) ON BL.CODTB1FAT = GR.CODTB1FAT
		WHERE GR.CODTB1FAT = ?
		AND CODCOLIGADA = 1";

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute([$grupo]);

		$grupo = $stmt->fetch(PDO::FETCH_ASSOC);

		return $grupo;
	}

	public static function EtapasEngenharia($item, $idetapa, $acao, $tipomotivo = '', $motivo = '', $compras = '', $cliente = '', $dtfim = '', $anexo = '', $db = 'Polar')
	{
		$con = ConexaoBlinda($db);

		//$sql = "EXEC PROC_ETAPAS_ENGENHARIA ?, ?, ?, ?, ?, ?, ?, ?";
		$sql = "SET NOCOUNT ON; EXEC PROC_ETAPAS_ENGENHARIA :NUMEROMOV, :NUMITEMPEDIDO, :SKU, :IDETAPA, :ACAO, :USUARIO, :TIPOMOTIVO, :MOTIVO, NULL, :COMPRAS, :CLIENTE";
		
		if($dtfim != '' && $anexo != '')
			$sql = "SET NOCOUNT ON; EXEC PROC_ETAPAS_ENGENHARIA :NUMEROMOV, :NUMITEMPEDIDO, :SKU, :IDETAPA, :ACAO, :USUARIO, :TIPOMOTIVO, :MOTIVO, :DTFIM, :COMPRAS, :CLIENTE, :ANEXO";
		elseif($dtfim != '')
			$sql = "SET NOCOUNT ON; EXEC PROC_ETAPAS_ENGENHARIA :NUMEROMOV, :NUMITEMPEDIDO, :SKU, :IDETAPA, :ACAO, :USUARIO, :TIPOMOTIVO, :MOTIVO, :DTFIM, :COMPRAS, :CLIENTE";

		$stmt = $con->prepare($sql);
		
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV'], PDO::PARAM_STR);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO'], PDO::PARAM_INT);
		$stmt->bindParam(':SKU', $item['SKU'], PDO::PARAM_STR);
		$stmt->bindParam(':IDETAPA', $idetapa, PDO::PARAM_INT);
		$stmt->bindParam(':ACAO', $acao, PDO::PARAM_STR);
		$stmt->bindParam(':USUARIO', $_SESSION[Config::$uniqid]['USUARIO'], PDO::PARAM_STR);
		$stmt->bindParam(':TIPOMOTIVO', $tipomotivo, PDO::PARAM_STR);
		$stmt->bindParam(':MOTIVO', $motivo, PDO::PARAM_STR);
		if($dtfim != '') 
			$stmt->bindParam(':DTFIM', $dtfim, PDO::PARAM_STR);
		$stmt->bindParam(':COMPRAS', $compras, PDO::PARAM_STR);
		$stmt->bindParam(':CLIENTE', $cliente, PDO::PARAM_STR);
		if($dtfim != '' && $anexo != '')
			$stmt->bindParam(':ANEXO', $anexo, PDO::PARAM_STR);
		

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row;
	}

	public static function getGrupoItem($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = 
		"SELECT 
			NUMEROMOV, POCLIENTE, NUMITEMPEDIDO, SKU, CODTB1FAT, TIPO,
			TEMPO, TEMPOAPROVACAO, TEMPO44, TEMPO45, TEMPO46, TEMPO47, UF, TEMPOUF,
			DATASTART, DATACHECKLIST, DATAOTIF, DATAOTIFAPROV, DATAAPROV, TEMPOCOMPRAS, DATAOTIFCOMPRAS, DATACOMPRAS, TEMPOPRODUCAO, DATAOTIFPRODUCAO, DATAPRODUCAO

		FROM TTB1BLINDAITEM I 
		WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);	
		$stmt->execute();

		$itemCategoria = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		? $row
		: [
			'NUMEROMOV' => '', 'POCLIENTE' => '', 'NUMITEMPEDIDO' => '', 'SKU' => '', 'CODTB1FAT' => '', 'TIPO' => '', 'TEMPO' => '', 
			'TEMPOAPROVACAO' => '', 'TEMPO44' => '', 'TEMPO45' => '', 'TEMPO46' => '', 'TEMPO47' => '', 'UF' => '', 'TEMPOUF' => '',
			'DATASTART' => '', 'DATACHECKLIST' => '', 'DATAOTIF' => '', 'DATAOTIFAPROV' => '', 'DATAAPROV' => '', 'TEMPOCOMPRAS' => '', 'DATAOTIFCOMPRAS' => '', 'DATACOMPRAS' => '', 'TEMPOPRODUCAO' => '', 'DATAOTIFPRODUCAO' => '', 'DATAPRODUCAO' => ''
		];

		return $itemCategoria;
	}

	public static function insertGrupo($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = 
		"INSERT INTO TTB1BLINDAITEM (NUMEROMOV, POCLIENTE, NUMITEMPEDIDO, SKU, CODTB1FAT, TIPO, TEMPO, TEMPOAPROVACAO, TEMPO44, TEMPO45, TEMPO46, TEMPO47, UF, TEMPOUF, DATASTART, DATACHECKLIST, DATAOTIF, DATAOTIFAPROV, TEMPOCOMPRAS, DATAOTIFCOMPRAS, TEMPOPRODUCAO, DATAOTIFPRODUCAO) 
		 VALUES (:NUMEROMOV, :POCLIENTE, :NUMITEMPEDIDO, :SKU, :CODTB1FAT, :TIPO, :TEMPO, :TEMPOAPROVACAO, :TEMPO44, :TEMPO45, :TEMPO46, :TEMPO47, :UF, :TEMPOUF, :DATASTART, :DATACHECKLIST, :DATAOTIF, :DATAOTIFAPROV, :TEMPOCOMPRAS, :DATAOTIFCOMPRAS, :TEMPOPRODUCAO, :DATAOTIFPRODUCAO)";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);
		$stmt->bindParam(':CODTB1FAT', $item['CODTB1FAT']);
		$stmt->bindParam(':TIPO', $item['TIPO']);
		$stmt->bindParam(':TEMPO', $item['TEMPO']);
		$stmt->bindParam(':TEMPOAPROVACAO', $item['TEMPOAPROVACAO']);
		$stmt->bindParam(':TEMPO44', $item['TEMPO44']);
		$stmt->bindParam(':TEMPO45', $item['TEMPO45']);
		$stmt->bindParam(':TEMPO46', $item['TEMPO46']);
		$stmt->bindParam(':TEMPO47', $item['TEMPO47']);
		$stmt->bindParam(':UF', $item['UF']);
		$stmt->bindParam(':TEMPOUF', $item['TEMPOUF']);
		$stmt->bindParam(':DATASTART', $item['DATASTART']);
		$stmt->bindParam(':DATACHECKLIST', $item['DATACHECKLIST']);
		$stmt->bindParam(':DATAOTIF', $item['DATAOTIF']);
		$stmt->bindParam(':DATAOTIFAPROV', $item['DATAOTIFAPROV']);
		$stmt->bindParam(':TEMPOCOMPRAS', $item['TEMPOCOMPRAS']);
		$stmt->bindParam(':DATAOTIFCOMPRAS', $item['DATAOTIFCOMPRAS']);
		$stmt->bindParam(':TEMPOPRODUCAO', $item['TEMPOPRODUCAO']);
		$stmt->bindParam(':DATAOTIFPRODUCAO', $item['DATAOTIFPRODUCAO']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateGrupo($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = 
		"UPDATE TTB1BLINDAITEM SET CODTB1FAT = :CODTB1FAT, TIPO = :TIPO, TEMPO = :TEMPO, TEMPOAPROVACAO = :TEMPOAPROVACAO, 
				TEMPO44 = :TEMPO44, TEMPO45 = :TEMPO45, TEMPO46 = :TEMPO46, TEMPO47 = :TEMPO47, UF = :UF, TEMPOUF = :TEMPOUF, 
				DATASTART = :DATASTART, DATACHECKLIST = :DATACHECKLIST, DATAOTIF = :DATAOTIF, DATAOTIFAPROV = :DATAOTIFAPROV,
				TEMPOCOMPRAS = :TEMPOCOMPRAS, DATAOTIFCOMPRAS = :DATAOTIFCOMPRAS, TEMPOPRODUCAO = :TEMPOPRODUCAO, DATAOTIFPRODUCAO = :DATAOTIFPRODUCAO

		 WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':CODTB1FAT', $item['CODTB1FAT']);
		$stmt->bindParam(':TIPO', $item['TIPO']);
		$stmt->bindParam(':TEMPO', $item['TEMPO']);
		$stmt->bindParam(':TEMPOAPROVACAO', $item['TEMPOAPROVACAO']);
		$stmt->bindParam(':TEMPO44', $item['TEMPO44']);
		$stmt->bindParam(':TEMPO45', $item['TEMPO45']);
		$stmt->bindParam(':TEMPO46', $item['TEMPO46']);
		$stmt->bindParam(':TEMPO47', $item['TEMPO47']);
		$stmt->bindParam(':UF', $item['UF']);
		$stmt->bindParam(':TEMPOUF', $item['TEMPOUF']);
		$stmt->bindParam(':DATASTART', $item['DATASTART']);
		$stmt->bindParam(':DATACHECKLIST', $item['DATACHECKLIST']);
		$stmt->bindParam(':DATAOTIF', $item['DATAOTIF']);
		$stmt->bindParam(':DATAOTIFAPROV', $item['DATAOTIFAPROV']);
		$stmt->bindParam(':TEMPOCOMPRAS', $item['TEMPOCOMPRAS']);
		$stmt->bindParam(':DATAOTIFCOMPRAS', $item['DATAOTIFCOMPRAS']);
		$stmt->bindParam(':TEMPOPRODUCAO', $item['TEMPOPRODUCAO']);
		$stmt->bindParam(':DATAOTIFPRODUCAO', $item['DATAOTIFPRODUCAO']);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function getTotalAprov($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "SELECT COUNT(*) AS [TOTAL] FROM ETAPAITEMOTIF WHERE IDETAPA = 102 AND NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row['TOTAL'];
	}

	public static function updateDataAprov($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = 
		"UPDATE I 
		SET DATAAPROV = (SELECT TOP 1 DTINICIO FROM ETAPAITEMOTIF E WHERE E.IDETAPA = 102 AND E.NUMEROMOV = I.NUMEROMOV AND E.POCLIENTE = I.POCLIENTE AND E.NUMITEMPEDIDO = I.NUMITEMPEDIDO AND E.SKU = I.SKU ORDER BY E.DTINICIO) 
		FROM TTB1BLINDAITEM I
		WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function datasLinha($item, $tipoData, $db = 'Polar')
	{
		$campo = '';

		switch($tipoData)
		{
			case 'C': $campo = 'DATACOMPRAS'; break;
			case 'P': $campo = 'DATAPRODUCAO'; break;
		}

		if($campo != '')
		{
			$sql = "UPDATE TTB1BLINDAITEM SET {$campo} = CAST(GETDATE() AS DATE) WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

			$con = ConexaoBlinda($db);
			$stmt = $con->prepare($sql);

			//$stmt->bindParam(":{$campo}", $data);
			$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
			$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
			$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
			$stmt->bindParam(':SKU', $item['SKU']);

			return $stmt->execute() ? true : $stmt->errorInfo();
		}
		else
			return false;
	}

	public static function tempoCategoria($categoria, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);

		$total = self::getCategoria($categoria['CODTB1FAT']);

		$sql = ($total == 0)
		? "INSERT INTO TTB1BLI (TEMPO, CODTB1FAT) VALUES (:TEMPO, :CODTB1FAT)"
		: "UPDATE TTB1BLI SET TEMPO = :TEMPO WHERE CODTB1FAT : CODTB1FAT";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':TEMPO', $categoria['TEMPO']);
		$stmt->bindParam(':CODTB1FAT', $categoria['CODTB1FAT']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function getCategoria($codtb1fat, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "SELECT COUNT(*) AS [TOTAL] FROM TTB1BL WHERE CODTB1FAT :CODTB1FAT";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':CODTB1FAT', $codtb1fat);	
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row['TOTAL'];
	}

	public static function getDocumentos($db = 'Polar')
	{
		$sql = "SELECT ID, NOME FROM TBLINDADOC";

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute();

		$documentos = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

			$documentos[] = [
				'ID' => $row['ID'],
				'NOME' => $row['NOME']
			];
		}

		return $documentos;
	}

	public static function insertDoc($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "INSERT INTO TBLINDADOCITEM (NUMEROMOV, POCLIENTE, NUMITEMPEDIDO, SKU, ID_TBLINDADOC) VALUES (:NUMEROMOV, :POCLIENTE, :NUMITEMPEDIDO, :SKU, :ID_TBLINDADOC)";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);
		$stmt->bindParam(':ID_TBLINDADOC', $item['ID_TBLINDADOC']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateDoc($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "UPDATE TBLINDADOCITEM SET STATUS = :STATUS WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU AND ID_TBLINDADOC = :ID_TBLINDADOC";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':STATUS', $item['STATUS']);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);
		$stmt->bindParam(':ID_TBLINDADOC', $item['ID_TBLINDADOC']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function deleteAllDocs($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "DELETE FROM TBLINDADOCITEM WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function deleteDoc($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "DELETE FROM TBLINDADOCITEM WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU AND ID_TBLINDADOC = :ID_TBLINDADOC";

		$stmt = $con->prepare($sql);

		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);
		$stmt->bindParam(':ID_TBLINDADOC', $item['ID_TBLINDADOC']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function getDocumentosItem($item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "SELECT ID_TBLINDADOC, STATUS FROM TBLINDADOCITEM WHERE NUMEROMOV = ? AND POCLIENTE = ? AND NUMITEMPEDIDO = ? AND SKU = ?";

		$stmt = $con->prepare($sql);
		$stmt->execute([$item['NUMEROMOV'], $item['POCLIENTE'], $item['NUMITEMPEDIDO'], $item['SKU']]);

		$documentosItem = [];
		$statusItem = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			array_push($documentosItem, $row['ID_TBLINDADOC']);
			$statusItem[$row['ID_TBLINDADOC']] = $row['STATUS'];
		}

		return [$documentosItem, $statusItem];
	}

	public static function getInfoSKU($numeromov, $sku, $pocliente, $numitempedido, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = 
		"SELECT T.IDMOV AS [IDMOV], T.NUMEROMOV AS [NUMEROMOV], I.IDPRD AS [IDPRD], P.CODIGOPRD AS [SKU], I.NSEQITMMOV AS [NSEQITMMOV], F.NUMPEDIDO AS [POCLIENTE], F.NUMITEMPEDIDO AS [NUMITEMPEDIDO]
		FROM CorporeRM.dbo.TITMMOV I
		INNER JOIN CorporeRM.dbo.TITMMOVFISCAL F (NOLOCK) ON F.IDMOV = I.IDMOV AND F.CODCOLIGADA = I.CODCOLIGADA AND F.NSEQITMMOV = I.NSEQITMMOV
		INNER JOIN CorporeRM.dbo.TPRD P (NOLOCK) ON P.IDPRD = I.IDPRD AND P.CODCOLIGADA = I.CODCOLIGADA
		INNER JOIN CorporeRM.dbo.TMOV T (NOLOCK) ON T.IDMOV = I.IDMOV AND T.CODCOLIGADA = I.CODCOLIGADA AND T.CODTMV = '2.1.04'

		WHERE T.NUMEROMOV = :NUMEROMOV
		AND P.CODIGOPRD = :SKU
		AND F.NUMPEDIDO = :POCLIENTE
		AND F.NUMITEMPEDIDO = :NUMITEMPEDIDO";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':NUMEROMOV', $numeromov);	
		$stmt->bindParam(':SKU', $sku);	
		$stmt->bindParam(':POCLIENTE', $pocliente);	
		$stmt->bindParam(':NUMITEMPEDIDO', $numitempedido);	
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row;
	}

	public static function getSKU($sku, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "SELECT IDPRD, CODIGOPRD AS [SKU], NUMNOFABRIC AS [REF] FROM CorporeRM.dbo.TPRD WHERE CODIGOPRD = :SKU AND CODCOLIGADA = 1";

		$stmt = $con->prepare($sql);	
		$stmt->bindParam(':SKU', $sku);
		$stmt->execute();

		$sku = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row : ['IDPRD' => '', 'SKU' => '', 'REFERENCIA' => ''];

		return $sku;
	}

	public static function updateSKU($infoSKU, $novosku, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "UPDATE CorporeRM.dbo.TITMMOV SET IDPRD = :IDPRDNOVO WHERE IDMOV = :IDMOV AND CODCOLIGADA = 1 AND NSEQITMMOV = :NSEQITMMOV AND IDPRD = :IDPRD";
		//print_r($novosku);
		//print_r($infoSKU);
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':IDPRDNOVO', $novosku['IDPRD']);
		$stmt->bindParam(':IDMOV', $infoSKU['IDMOV']);
		$stmt->bindParam(':NSEQITMMOV', $infoSKU['NSEQITMMOV']);
		$stmt->bindParam(':IDPRD', $infoSKU['IDPRD']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateEtapasEng($novosku, $item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "UPDATE ETAPAITEMOTIF SET IDPRD = :IDPRDNOVO, SKU = :SKUNOVO WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':IDPRDNOVO', $novosku['IDPRD']);
		$stmt->bindParam(':SKUNOVO', $novosku['SKU']);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateItemEng($novosku, $item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "UPDATE TTB1BLINDAITEM SET SKU = :SKUNOVO WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':SKUNOVO', $novosku['SKU']);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateDocEng($novosku, $item, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "UPDATE TBLINDADOCITEM SET SKU = :SKUNOVO WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE AND NUMITEMPEDIDO = :NUMITEMPEDIDO AND SKU = :SKU";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':SKUNOVO', $novosku['SKU']);
		$stmt->bindParam(':NUMEROMOV', $item['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $item['POCLIENTE']);
		$stmt->bindParam(':NUMITEMPEDIDO', $item['NUMITEMPEDIDO']);
		$stmt->bindParam(':SKU', $item['SKU']);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateNomeCKL($infoSKU, $skunovo, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = "UPDATE Conteudo.dbo.TCHECKCAMPOS SET NOME = :NOMENOVO WHERE TIPO = 'T' AND PROCESSO = :PROCESSO AND COLIGADA = 1 AND POCLIENTE = :POCLIENTE AND NOME = :NOME";

		$nome = "txtAnaliseCritica{$infoSKU['NUMEROMOV']}-{$infoSKU['SKU']}-{$infoSKU['NSEQITMMOV']}";
		$nomeNovo = "txtAnaliseCritica{$infoSKU['NUMEROMOV']}-{$skunovo['SKU']}-{$infoSKU['NSEQITMMOV']}";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':NOMENOVO', $nomeNovo);
		$stmt->bindParam(':PROCESSO', $infoSKU['NUMEROMOV']);
		$stmt->bindParam(':POCLIENTE', $infoSKU['POCLIENTE']);
		$stmt->bindParam(':NOME', $nome);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function updateValorCKL($processo, $pocliente, $sku, $skunovo, $nseq, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		
		$sqlCF = "SELECT VALOR FROM Conteudo.dbo.TCHECKCAMPOS WHERE TIPO = 'C' AND NOME = 'checksFormulario' AND PROCESSO = :PROCESSO AND COLIGADA = 1 AND POCLIENTE = :POCLIENTE";
		$stmtCF = $con->prepare($sqlCF);

		$stmtCF->bindParam(':PROCESSO', $processo);
		$stmtCF->bindParam(':POCLIENTE', $pocliente);

		$stmtCF->execute(); 
		$rCF = $stmtCF->fetch(PDO::FETCH_ASSOC);

		$sqlUCF = "UPDATE Conteudo.dbo.TCHECKCAMPOS SET VALOR = :VALORNOVO WHERE TIPO = 'C' AND NOME = 'checksFormulario' AND PROCESSO = :PROCESSO AND COLIGADA = 1 AND POCLIENTE = :POCLIENTE";
		$stmtUCF = $con->prepare($sqlUCF);

		$valor = $rCF['VALOR'];
		$valornovo = str_replace( "{$processo}-{$sku}-{$nseq}", "{$processo}-{$skunovo}-{$nseq}", $valor);

		$stmtUCF->bindParam(':VALORNOVO', $valornovo);
		$stmtUCF->bindParam(':PROCESSO', $processo);
		$stmtUCF->bindParam(':POCLIENTE', $pocliente);

		return $stmtUCF->execute() ? true : $stmtUCF->errorInfo();
	}

	public static function OTIFPO($numeromov, $pocliente, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$codcoligada = 1;

		$sql = "SET NOCOUNT ON; EXEC PROC_CALC_OTIF_PO :NUMEROMOV, :CODCOLIGADA, :POCLIENTE";

		$stmt = $con->prepare($sql);
		
		$stmt->bindParam(':NUMEROMOV', $numeromov);
		$stmt->bindParam(':CODCOLIGADA', $codcoligada);
		$stmt->bindParam(':POCLIENTE', $pocliente);

		//echo "{$numeromov}-{$codcoligada}-{$pocliente}";

		return $stmt->execute();

		//$row = $stmt->fetch(PDO::FETCH_ASSOC);
		//return $row;
	}

	public static function updateTempoCliente(string $numeromov, string $pocliente, int $numitempedido, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);

		$sql =
		"WITH APROVCLIENTE AS (
			SELECT NUMEROMOV, POCLIENTE, NUMITEMPEDIDO, DATEDIFF(DAY, MIN(DTINICIO), MAX(ISNULL(DTFIM, GETDATE()))) AS [TEMPO]
			FROM ETAPAITEMOTIF
			WHERE IDETAPA = 102
			GROUP BY NUMEROMOV, POCLIENTE, NUMITEMPEDIDO
		)

		UPDATE I SET TEMPOCLIENTE = A.TEMPO
		FROM TTB1BLINDAITEM I
		INNER JOIN APROVCLIENTE A (NOLOCK) ON A.NUMEROMOV = I.NUMEROMOV AND A.POCLIENTE = I.POCLIENTE AND A.NUMITEMPEDIDO = I.NUMITEMPEDIDO
		--INNER JOIN ETAPAITEMOTIF E (NOLOCK) ON E.NUMEROMOV = I.NUMEROMOV AND E.POCLIENTE = I.POCLIENTE AND E.NUMITEMPEDIDO = I.NUMITEMPEDIDO
		WHERE I.NUMEROMOV = :NUMEROMOV 
		AND I.POCLIENTE = :POCLIENTE 
		AND I.NUMITEMPEDIDO = :NUMITEMPEDIDO";

		/*$sql = 
		"UPDATE I SET TEMPOCLIENTE = DATEDIFF(DAY, MIN(E.DTINICIO), MAX(ISNULL(E.DTFIM, GETDATE()))) 
		FROM TTB1BLINDAITEM I
		INNER JOIN ETAPAITEMOTIF E (NOLOCK) ON E.NUMEROMOV = I.NUMEROMOV AND E.POCLIENTE = I.POCLIENTE AND E.NUMITEMPEDIDO = I.NUMITEMPEDIDO
		WHERE IDETAPA = 102 
		AND I.NUMEROMOV = :NUMEROMOV
		AND I.POCLIENTE = :POCLIENTE
		AND I.NUMITEMPEDIDO = :NUMITEMPEDIDO";*/

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':NUMEROMOV', $numeromov);
		$stmt->bindParam(':POCLIENTE', $pocliente);
		$stmt->bindParam(':NUMITEMPEDIDO', $numitempedido);

		return $stmt->execute() ? true : $stmt->errorInfo();
	}

	public static function getVerificaItensEngenharia($numeromov, $pocliente, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);
		$sql = 
		"SELECT 
			COUNT(*) AS [TOTAL], 
			SUM(CASE WHEN DATACOMPRAS IS NOT NULL THEN 1 ELSE 0 END) AS [ITENSOK],
			(SELECT TOP 1 IDMOV FROM CorporeRM.dbo.TMOV T WHERE T.CODTMV = '2.1.04' AND T.STATUS != 'C' AND T.NUMEROMOV = I.NUMEROMOV) AS [IDMOV]

		FROM TTB1BLINDAITEM 
		WHERE NUMEROMOV = :NUMEROMOV AND POCLIENTE = :POCLIENTE";

		$stmt = $con->prepare($sql);
		$stmt->bindParam(':NUMEROMOV', $numeromov);
		$stmt->bindParam(':POCLIENTE', $pocliente);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if($row['TOTAL'] == $row['ITENSOK'])
			return $row['IDMOV'];
		else
			return 0;
	}

	public static function abrirEtapaProcesso($idetapa, $idmov, $processo, $pocliente, $usuario, $dtInicio = '')
	{
		$sql = ($dtInicio != '')
		? 'INSERT ETAPAPROCESSOOTIF (IDETAPA, IDMOV, PROCESSO, POCLIENTE, DTINICIO, USUARIOINICIO) VALUES (:idetapa, :idmov, :processo, :pocliente, :dtinicio, :usuario)'
		: 'INSERT ETAPAPROCESSOOTIF (IDETAPA, IDMOV, PROCESSO, POCLIENTE, DTINICIO, USUARIOINICIO) VALUES (:idetapa, :idmov, :processo, :pocliente, GETDATE(), :usuario)';

		$con = ConexaoRM();
		$stmt = $con->prepare($sql);

		$stmt->bindParam(':idetapa', $idetapa);
		$stmt->bindParam(':idmov', $idmov);
		$stmt->bindParam(':processo', $processo);
		$stmt->bindParam(':pocliente', $pocliente);
		if($dtInicio != ''){
			$stmt->bindParam(':dtinicio', $dtInicio);
		}
		$stmt->bindParam(':usuario', $usuario);

		if($stmt->execute())
			return true;
		else
			return $stmt->errorInfo();
	}

	public static function fecharEtapaProcesso($idetapa, $processo, $pocliente, $usuario)
	{
		$sql = 'UPDATE ETAPAPROCESSOOTIF SET DTFIM = GETDATE(), USUARIOFIM = :usuario WHERE DTFIM IS NULL AND USUARIOFIM IS NULL AND PROCESSO = :processo AND POCLIENTE = :pocliente AND IDETAPA = :idetapa';
		$con = ConexaoRM();
		$stmt = $con->prepare($sql);

		$stmt->bindParam(':usuario', $usuario);
		$stmt->bindParam(':processo', $processo);
		$stmt->bindParam(':pocliente', $pocliente);
		$stmt->bindParam(':idetapa', $idetapa);

		if($stmt->execute())
			return true;
		else
			return $stmt->errorInfo();
	}
}