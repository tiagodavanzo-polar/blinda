<?php

class BlindaCompras
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

	// Tipos: 0 - Todos | 1 - Blinda | 2 - Polar | 3 - SKU
	public static function MRP($tipo = 0, $sku = null, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    	$con->setAttribute(PDO::SQLSRV_ATTR_DIRECT_QUERY, true);

		$sql = ''; //"EXEC usp_MRP ?, ?";
		//$sql = "{CALL usp_MRP(?, ?)}"; 
		//$stmt = $con->prepare($sql);

		if (is_null($sku)) {
			$sql = "SET NOCOUNT ON; EXEC usp_MRP ? WITH RECOMPILE";
			$stmt = $con->prepare($sql);
			$stmt->bindValue(1, $tipo, PDO::PARAM_INT);
		} else {
			$sql = "SET NOCOUNT ON; EXEC usp_MRP ?, ? WITH RECOMPILE";
			$stmt = $con->prepare($sql);
			$stmt->bindValue(1, $tipo, PDO::PARAM_INT);
			$stmt->bindValue(2, $sku, PDO::PARAM_STR);
		}
		//$stmt->bindParam(':SKU', $sku, PDO::PARAM_STR);

		$stmt->execute();

		//$tabelas = [];

		$fetchSKU = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$temProximo = $stmt->nextRowset(); 
    	$fetchOrigemDemanda = $temProximo ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

		$stmt->closeCursor();

		return ['SKU' => $fetchSKU, 'ORIGEM_DEMANDA' => $fetchOrigemDemanda];

		// 2. Loop para capturar todas as tabelas com segurança
		/*do {
			// Ignora rowsets vazios gerados por contagens do SQL Server
			if ($stmt->columnCount() > 0) {
				$tabelas[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		} while ($stmt->nextRowset());

		return ['SKU' => $tabelas[0], 'ORIGEM_DEMANDA' => $tabelas[1]];*/
	}

	public static function MPS($tipo = 0, $sku = null, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    	$con->setAttribute(PDO::SQLSRV_ATTR_DIRECT_QUERY, true);

		$sql = '';

		if (is_null($sku)) {
			$sql = "SET NOCOUNT ON; EXEC usp_MPS ? WITH RECOMPILE";
			$stmt = $con->prepare($sql);
			$stmt->bindValue(1, $tipo, PDO::PARAM_INT);
		} else {
			$sql = "SET NOCOUNT ON; EXEC usp_MPS ?, ? WITH RECOMPILE";
			$stmt = $con->prepare($sql);
			$stmt->bindValue(1, $tipo, PDO::PARAM_INT);
			$stmt->bindValue(2, $sku, PDO::PARAM_STR);
		}

		$stmt->execute();

		$fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$stmt->closeCursor();

		return $fetch;
	}

	public static function OrdensCompra($sku, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$sql =
		"SELECT 
			I.OC, OC.OPERACAO, OC.FORNECEDOR, 
			
			FORMAT(COALESCE(I.QTDEREC, 0) + COALESCE(RNFC, 0) + COALESCE(INV, 0) + COALESCE(EMB, 0), '0.##') AS [QTDE], I.DATAENTREGA, 

			CONCAT_WS(' | ',
				CASE WHEN COALESCE(I.QTDEREC, 0) > 0 THEN 'OC (' + FORMAT(COALESCE(I.QTDEREC, 0), '0.##') + ')' END,
				CASE WHEN COALESCE(RNFC, 0) > 0 THEN 'RNFC (' + FORMAT(COALESCE(RNFC, 0), '0.##') + ')' END,
				CASE WHEN COALESCE(INV, 0) > 0 THEN 'INV (' + FORMAT(COALESCE(INV, 0), '0.##') + ')' END,
				CASE WHEN COALESCE(EMB, 0) > 0 THEN 'EMB (' + FORMAT(COALESCE(EMB, 0), '0.##') + ')' END
			) AS [STATUSITEM]
			
		FROM zvw_RastreamentoOC_Itens I
		INNER JOIN zvw_RastreamentoOC OC (NOLOCK) ON OC.IDMOV = I.IDMOV AND OC.COLIGADA = I.COLIGADA
		WHERE I.SKU = :SKU
		AND I.COLIGADA = 1
		AND 
		(	
			COALESCE(I.QTDEREC, 0) > 0 OR
			COALESCE(RNFC, 0) > 0 OR
			COALESCE(INV, 0) > 0 OR
			COALESCE(EMB, 0) > 0
		)
		ORDER BY I.DATAENTREGA";

		//$sql = "SELECT * FROM ZVCOMPRASFORNITEM WHERE SKU = :SKU AND COLIGADA = 1";
		/*$sql = 
		"SELECT I.OC, OC.FORNECEDOR, OC.OPERACAO, FORMAT(COALESCE(I.QTDE,0), '0.##') AS QTDE, I.DTENTREGA, I.STATUSITEM
		FROM ZVCOMPRASFORNITEM I
		INNER JOIN ZVCOMPRASFORN OC (NOLOCK) ON OC.IDMOV = I.IDMOV AND OC.COLIGADA = I.COLIGADA
		WHERE SKU = :SKU
		AND I.COLIGADA = 1
		ORDER BY I.DTENTREGA";*/


		$stmt = $con->prepare($sql);

		$stmt->bindParam(':SKU', $sku, PDO::PARAM_STR);

		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function BlindaMRP($tipo = 0, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$sql = "SET NOCOUNT ON; EXEC usp_Blinda_MRP_Teste :TIPO";
		$stmt = $con->prepare($sql);

		$stmt->bindParam(':TIPO', $tipo, PDO::PARAM_INT);

		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function getAll($status = 'blinda', $db = 'CorporeRM')
	{
		$where = ($status == 'blinda') ? "WHERE CCUSTO = '1.30'" : "";
	
		$sql = 
		"SELECT 
			MIN([LINHA]) AS [LINHA], [CODCOLIGADA], [IDPRD], [SKU], [REFERENCIA], [PRODUTO], [FABRICANTE], [GRUPOPRODUTO], [UN], SUM([QTDE]) AS [DEMANDA]
		FROM zvw_Carteira_MRP
		$where
		GROUP BY [CODCOLIGADA], [IDPRD], [SKU], [REFERENCIA], [PRODUTO], [FABRICANTE], [GRUPOPRODUTO], [UN]
		ORDER BY MIN([LINHA])";

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute();

		$itens = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

			$itens[] = [
				'LINHA' => $row['LINHA'],
				'CODCOLIGADA' => $row['CODCOLIGADA'],
				'IDPRD' => $row['IDPRD'],
				'SKU' => '<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="MRPSKU(\''.$row['SKU'].'\')">'.$row['SKU'].'</a>',
				'REFERENCIA' => $row['REFERENCIA'],
				'PRODUTO' => $row['PRODUTO'],
				'FABRICANTE' => $row['FABRICANTE'],
				'GRUPOPRODUTO' => $row['GRUPOPRODUTO'],
				'UN' => $row['UN'],
				'DEMANDA' => $row['DEMANDA']
			];
		}

		return $itens;
	}

	public static function getLinha($status = 'blinda', $db = 'CorporeRM')
	{
		$where = ($status == 'blinda') ? "WHERE CCUSTO = '1.30'" : "";
	
		$sql = 
		"SELECT 
			[LINHA], [IDMOV], [CODCOLIGADA], [CODTMV], [CCUSTO], [OPERACAO], [POCLIENTE], [PROCESSO], [CLIENTE], 
			[NUMITEMPEDIDO], [IDPRD], [SKU], [REFERENCIA], [PRODUTO], [FABRICANTE], [GRUPOPRODUTO], [UN], [QTDE], 
			[DTPEDIDO], [DTENTREGA], [DTREPROG], [DTENTREGAITEM], [DTREPROGITEM], PRAZO
		FROM zvw_Carteira_MRP
		$where
		ORDER BY [LINHA]"; //FORMAT([DTPEDIDO], 'dd/MM/yyyy') AS [DTPEDIDO]

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);
		$stmt->execute();

		$itens = [];
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){

			$dtPedido = ($row['DTPEDIDO'] != '') ? (new DateTime($row['DTPEDIDO']))->format('d/m/Y') : '';
			$dtEntrega = ($row['DTENTREGA'] != '') ? (new DateTime($row['DTENTREGA']))->format('d/m/Y') : '';
			$dtReprog = ($row['DTREPROG'] != '') ? (new DateTime($row['DTREPROG']))->format('d/m/Y') : '';
			$dtEntregaItem = ($row['DTENTREGAITEM'] != '') ? (new DateTime($row['DTENTREGAITEM']))->format('d/m/Y') : '';
			$dtReprogItem = ($row['DTREPROGITEM'] != '') ? (new DateTime($row['DTREPROGITEM']))->format('d/m/Y') : '';

			$itens[] = [
				'LINHA' => $row['LINHA'],
				'IDMOV' => $row['IDMOV'],
				'CODCOLIGADA' => $row['CODCOLIGADA'],
				'CODTMV' => $row['CODTMV'],
				'CCUSTO' => $row['CCUSTO'],
				'OPERACAO' => $row['OPERACAO'],
				'PROCESSO' => '<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="Compras('.$row['IDMOV'].','.$row['CODCOLIGADA'].', \''.$row['POCLIENTE'].'\', '.$row['NUMITEMPEDIDO'].')">'.$row['PROCESSO'].'</a>',
				'POCLIENTE' => $row['POCLIENTE'],
				'CLIENTE' => $row['CLIENTE'],
				'NUMITEMPEDIDO' => $row['NUMITEMPEDIDO'],
				'SKU' => '<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="MRPSKU(\''.$row['SKU'].'\')">'.$row['SKU'].'</a>',
				'REFERENCIA' => $row['REFERENCIA'],
				'PRODUTO' => $row['PRODUTO'],
				'FABRICANTE' => $row['FABRICANTE'],
				'GRUPOPRODUTO' => $row['GRUPOPRODUTO'],
				'UN' => $row['UN'],
				'QTDE' => $row['QTDE'],
				'DTPEDIDO' => $dtPedido,
				'DTENTREGA' => $dtEntrega,
				'DTREPROG' => $dtReprog,
				'DTENTREGAITEM' => $dtEntregaItem,
				'DTREPROGITEM' => $dtReprogItem,
				'PRAZO' => $row['PRAZO']
			];
		}

		return $itens;
	}

	public static function MRPSKU($sku, $output = 1, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$sql = "SET NOCOUNT ON; EXEC STP_MRP_SKU :SKU, :OUTPUT";
		$stmt = $con->prepare($sql);
		
		$stmt->bindParam(':SKU', $sku, PDO::PARAM_STR);
		$stmt->bindParam(':OUTPUT', $output, PDO::PARAM_INT);

		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function MRPSKUTotal($sku, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$sql = "SET NOCOUNT ON; EXEC STP_MRP_ANALISE_SKU_MAX_PERFORMANCE @SKU_FILTRO = :SKU";
		$stmt = $con->prepare($sql);
		
		$stmt->bindParam(':SKU', $sku, PDO::PARAM_STR);

		$stmt->execute();

		$tabelas = [];

		// 2. Loop para capturar todas as tabelas com segurança
		do {
			// Ignora rowsets vazios gerados por contagens do SQL Server
			if ($stmt->columnCount() > 0) {
				$tabelas[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		} while ($stmt->nextRowset());

		// Acessando as tabelas salvas
		$sku = isset($tabelas[0]) ? $tabelas[0] : [];
		$demanda = isset($tabelas[1]) ? $tabelas[1] : [];
		$demandaFilho = isset($tabelas[2]) ? $tabelas[2] : [];

		return ['SKU' => $sku, 'DEMANDA' => $demanda, 'DEMANDAFILHO' => $demandaFilho];
	}

	public static function BlindaEstoque($sku, $db = 'CorporeRM')
	{
		$con = ConexaoBlinda($db);

		$sql = "SELECT SKU, DISPONIVEL FROM ZVBLINDAESTOQUE_AUTOMATICO WHERE SKU = :SKU";
		$stmt = $con->prepare($sql);

		$stmt->bindParam(':SKU', $sku, PDO::PARAM_STR);

		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}