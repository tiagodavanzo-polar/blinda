<?php

class BLBlinda
{

	public static function getAll($status = '', $db = 'Polar')
	{
		$condStatus = ($status != '') ? "WHERE PLANEJAMENTO = 'I' OR (PLANEJAMENTO = 'F' AND ISNULL(RTS, '-') NOT IN ('F', 'N'))" : "";

		$sql = 
		"SELECT 
			[LINHA], [PRIORIDADE], [PR], [MOV], [CCUSTO], [NUMEROMOV], [STATUSMOV], [NUMITEMPEDIDO], [SKU], [ITEM], [REFERENCIA], [QTDE], [DT ENTREGA], [DATAENTREGA], 
			[PLANEJAMENTO], [SEPARACAO], [MONTAGEM], [INSPECAO], [EMBALAGEM], [OP], [SD], [RTS], [TRANSPORTE]
		FROM zvw_Blinda_PainelProducao
		$condStatus
		ORDER BY [PRIORIDADE], [DT ENTREGA]";
		
		$condStatus = ($status != '') ? "WHERE PLANEJAMENTO = 'I'" : "";
		//$condStatus = ($status != '') ? "WHERE PLANEJAMENTO = 'I'" : "";

		//TEMPORÁRIO
		$sql = 
		"SELECT 
			[LINHA], [PRIORIDADE], 
			CASE 
				WHEN PRIORIDADE = 1 THEN 'ALTA'
				WHEN PRIORIDADE = 2 THEN 'MÉDIA'
				WHEN PRIORIDADE = 3 THEN 'NORMAL'
				WHEN PRIORIDADE = 4 THEN 'BAIXA'
			END AS [PR], 
			[MOV], [CCUSTO], [NUMEROMOV], [STATUSMOV], [NUMITEMPEDIDO], [SKU], [ITEM], [REFERENCIA], [QTDE], [DT ENTREGA], FORMAT([DT ENTREGA], 'dd/MM/yyyy') AS [DATAENTREGA], 
			[PLANEJAMENTO], [SEPARACAO], [MONTAGEM], [INSPECAO], [EMBALAGEM], 'NI' AS [OP], 'NI' AS [SD], 'NI' AS [RTS], [TRANSPORTE]
		FROM TBLINDAPRODUCAO
		--WHERE MOV != 'NF'
		$condStatus
		ORDER BY [PRIORIDADE], [DT ENTREGA]";
		
		//$sql = "SELECT LINHA, PR, MOV, CCUSTO, NUMEROMOV, SKU, ITEM, REFERENCIA, QTDE, DATAENTREGA, PLANEJAMENTO, SEPARACAO, MONTAGEM, INSPECAO, EMBALAGEM, INDUSTRIALIZACAO, TRANSPORTE, STATUSFINAL FROM Homolog.dbo.TBLINDAPRODUCAO";
		
		/*$sql =
		"SELECT 
			ROW_NUMBER() OVER (ORDER BY [DT ENTREGA]) AS [LINHA], 
			CASE 
				WHEN PRIORIDADE = 1 THEN 'ALTA'
				WHEN PRIORIDADE = 2 THEN 'MÉDIA'
				WHEN PRIORIDADE = 3 THEN 'NORMAL'
				WHEN PRIORIDADE = 4 THEN 'BAIXA'
			END AS [PR],
			CODTMV AS [MOV],
			[CCUSTO],
			[NUMEROMOV],
			[SKU],
			[ITEM],
			[REFERENCIA],
			CAST(QTDE AS INT) AS [QTDE],
			FORMAT([DT ENTREGA], 'dd/MM/yyyy') AS [DATAENTREGA],
			[PLANEJAMENTO],
			[SEPARACAO],
			[MONTAGEM],
			[INSPECAO],
			[EMBALAGEM],
			[INDUSTRIALIZACAO],
			[TRANSPORTE],
			[IDMOV],
			1 AS [COLIGADA],
			[STATUSMOV]
			
		FROM ZVBLINDAPRODUCAO
		WHERE (CODTMV = '2.1.06' OR (CODTMV = '2.1.40' AND XPED IS NULL))
		AND (STATUSMOV IN ('A','G') OR (STATUSMOV IN ('F','Q') AND [DT ENTREGA] >= '2025-01-01'))
		AND CANCELADO = 'N'
		$condStatus
		ORDER BY [PRIORIDADE], [DT ENTREGA]";*/

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute();

		$itens = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

			$btnPlanejamento = '';

			if($row['PLANEJAMENTO'] == 'NI')
				$btnPlanejamento = '<button type="button" class="btn btn-sm btn-primary p-0 pl-1 pr-1" onclick="ProducaoBlinda(\'iniciarPlanejamento\', \''.$row['NUMEROMOV'].'\', '.$row['NUMITEMPEDIDO'].', \''.$row['SKU'].'\')">Iniciar</button>';

			if($row['PLANEJAMENTO'] == 'I' && $row['SEPARACAO'] == 'F' && $row['MONTAGEM'] == 'F' && $row['INSPECAO'] == 'F' && $row['EMBALAGEM'] == 'F' && ($row['TRANSPORTE'] == 'NI' || $row['TRANSPORTE'] == 'F'))
				$btnPlanejamento = '<button type="button" class="btn btn-sm btn-success p-0 pl-1 pr-1" onclick="ProducaoBlinda(\'finalizarPlanejamento\', \''.$row['NUMEROMOV'].'\', '.$row['NUMITEMPEDIDO'].', \''.$row['SKU'].'\')">Finalizar</button>';
			
			if($row['PLANEJAMENTO'] == 'F')
				$btnPlanejamento = '<button type="button" class="btn btn-sm btn-warning p-0 pl-1 pr-1" onclick="ProducaoBlinda(\'reabrirPlanejamento\', \''.$row['NUMEROMOV'].'\', '.$row['NUMITEMPEDIDO'].', \''.$row['SKU'].'\')">Reabrir</button>';
			
			$statusOP = ['' => 'NI', '-' => 'NI', 'NI' => 'NI', 'A' => 'I', 'U' => 'I', 'I' => 'I', 'N' => 'F', 'F' => 'F'];

			if($row['NUMEROMOV'] != '316381' || ($row['NUMEROMOV'] == '316381' && $_SESSION[Config::$uniqid]['USUARIO'] == 'tiago.davanzo'))
			{

			$itens[] = [
				'LINHA' => $row['LINHA'],
				'IDMOV' => '',//$row['IDMOV'],
				'COLIGADA' => '', //$row['COLIGADA'],
				'PR' => substr($row['PR'], 0, 1),
				'MOV' => $row['MOV'],
				'CCUSTO' => $row['CCUSTO'],
				'NUMEROMOV' => $row['NUMEROMOV'],
				'SKU' => $row['SKU'],
				'NUMITEMPEDIDO' => $row['NUMITEMPEDIDO'],
				'REFERENCIA' => $row['REFERENCIA'],
				'QTDE' => $row['QTDE'],
				'ACAO' => $btnPlanejamento,
				'DATAENTREGA' => $row['DATAENTREGA'],
				'PLANEJAMENTO' => $row['PLANEJAMENTO'],
				'SEPARACAO' => $row['SEPARACAO'],
				'MONTAGEM' => $row['MONTAGEM'],
				'INSPECAO' => $row['INSPECAO'],
				'EMBALAGEM' => $row['EMBALAGEM'],
				'OP' => $statusOP[$row['OP']],
				'SD' =>  $statusOP[$row['SD']],
				'RTS' =>  $statusOP[$row['RTS']],
				'TRANSPORTE' => $row['TRANSPORTE']
			];

			}
		}

		return $itens;
	}

	public static function getItem($idmov, $coligada, $nseqitmmov, $db = 'Polar')
	{

		$sql = 
		"SELECT 
			[LINHA], [PRIORIDADE], [PR], [MOV], [CCUSTO], [IDMOV], [COLIGADA], [NUMEROMOV], [STATUSMOV], [SKU], [ITEM], [REFERENCIA], [QTDE], [DT ENTREGA], [DATAENTREGA], 
			[PLANEJAMENTO], [SEPARACAO], [MONTAGEM], [INSPECAO], [EMBALAGEM], [OP], [SD], [RTS], [INDUSTRIALIZACAO], [TRANSPORTE]
		FROM TBLINDAPRODUCAO
		WHERE IDMOV = ?
		AND ITEM = ?
		ORDER BY [PRIORIDADE], [DT ENTREGA]";

		/*$sql =
		"SELECT 
			ROW_NUMBER() OVER (ORDER BY [DT ENTREGA]) AS [LINHA], 
			CASE 
				WHEN PRIORIDADE = 1 THEN 'ALTA'
				WHEN PRIORIDADE = 2 THEN 'MÉDIA'
				WHEN PRIORIDADE = 3 THEN 'NORMAL'
				WHEN PRIORIDADE = 4 THEN 'BAIXA'
			END AS [PR],
			CODTMV AS [MOV],
			[CCUSTO],
			[NUMEROMOV],
			[SKU],
			[ITEM],
			[REFERENCIA],
			CAST(QTDE AS INT) AS [QTDE],
			FORMAT([DT ENTREGA], 'dd/MM/yyyy') AS [DATAENTREGA],
			[PLANEJAMENTO],
			[SEPARACAO],
			[MONTAGEM],
			[INSPECAO],
			[EMBALAGEM],
			[INDUSTRIALIZACAO],
			[TRANSPORTE],
			[IDMOV],
			1 AS [COLIGADA],
			[STATUSMOV]
			
		FROM ZVBLINDAPRODUCAO
		WHERE (CODTMV = '2.1.06' OR (CODTMV = '2.1.40' AND XPED IS NULL))
		AND (STATUSMOV IN ('A','G') OR (STATUSMOV IN ('F','Q') AND [DT ENTREGA] >= '2025-07-01'))
		AND IDMOV = ?
		AND ITEM = ?
		ORDER BY [PRIORIDADE], [DT ENTREGA]";*/

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute([$idmov, $nseqitmmov]);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}