<?php
class BLPVProducao
{

	public static function EtapasProducao($item, $idetapa, $acao, $db = 'Polar')
	{
		$con = ConexaoBlinda($db);

		//$sql = "EXEC PROC_ETAPAS_PRODUCAO :idmov, :nseqitmmov, :idetapa, :acao, :usuario"; //EXEC PROC_ETAPAS_PRODUCAO 2239729 , 1, 90, 'I', 'si'
		$sql = "EXEC PROC_ETAPAS_PRODUCAO ?, ?, ?, ?, ?, ?"; //EXEC PROC_ETAPAS_PRODUCAO 2239729 , 1, 90, 'I', 'si'

		$stmt = $con->prepare($sql);

		if($stmt->execute([$item['NUMEROMOV'], $item['NUMITEMPEDIDO'], $item['SKU'], $idetapa, $acao, $_SESSION[Config::$uniqid]['USUARIO']]))
			return true;
		else
			return false;	
	}

	public static function getRow($numeromov, $numitempedido, $sku, $db = 'Polar')
	{
		$sql = "SELECT * FROM TBLINDAPRODUCAO WHERE NUMEROMOV = :numeromov AND NUMITEMPEDIDO = :numitempedido AND SKU = :sku"; //ZVBLINDAPRODUCAO

		$con = ConexaoBlinda($db);
		$stmt = $con->prepare($sql);

		$stmt->bindParam(':numeromov', $numeromov);
		$stmt->bindParam(':numitempedido', $numitempedido);
		$stmt->bindParam(':sku', $sku);

		$stmt->execute();

		return $stmt->fetch();
	}
}