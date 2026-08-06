<?php
	ob_start();
	session_start();
	require_once "../config.php";
	require_once "../php/verifica.php"; 
	extract($_POST);

	/*$resultado = BlindaCompras::MPS(0);

	$skupai = [];
	foreach ($resultado['ORIGEM_DEMANDA'] as $demanda) {
		if($demanda['NIVEL'] == 'ROOT') 
			$skupai[$demanda['CHAVE_DEMANDA']] = $demanda;
		else
			$skupai[$demanda['CHAVE_DEMANDA']]['FILHOS'][] = $demanda;
	}

	echo '<pre>';
	print_r($skupai);
	echo'</pre>';*/
	
	if($acao == 'producao')
	{
		$status = ($status == 'todos') ? '' : 'I';
		$resultado = BLBlinda::getAll($status);
		
		echo json_encode(['data' => $resultado]);
	}
	elseif($acao == 'iniciarPlanejamento')
	{
		$item = BLPVProducao::getRow($numeromov, $numitempedido, $sku);

		//print_r($item);
		
		$resposta = (BLPVProducao::EtapasProducao($item, 90, 'I')) ? "A etapa de planejamento foi iniciada." : "Erro ao iniciar o planejamento.";

		echo json_encode(['resposta' => $resposta]);

	} 
	elseif($acao == 'finalizarPlanejamento')
	{
		$item = BLPVProducao::getRow($numeromov, $numitempedido, $sku);
		
		$resposta = (BLPVProducao::EtapasProducao($item, 90, 'F')) ? "A etapa de planejamento foi finalizada." : "Erro ao finalizar o planejamento.";
		//$resposta = BLPVProducao::EtapasProducao($item, 90, 'F');

		//print_r($resposta);

		echo json_encode(['resposta' => $resposta]);
	}  
	elseif($acao == 'reabrirPlanejamento')
	{
		$item = BLPVProducao::getRow($numeromov, $numitempedido, $sku);
		
		$resposta = (BLPVProducao::EtapasProducao($item, 90, 'I')) ? "A etapa de planejamento foi reaberta." : "Erro ao finalizar o planejamento.";

		echo json_encode(['resposta' => $resposta]);
	}
	elseif($acao == 'engenharia')
	{
		$resultado = BlindaEngenharia::getAll($status);
		
		echo json_encode(['data' => $resultado]);
	}
	elseif($acao == 'etapaseng')
	{
		$item = BlindaEngenharia::getRow($numeromov, $sku, $numitempedido);
		$itemGrupo = BlindaEngenharia::getGrupoItem($item);

		$tipomotivo = ($acaoEng == 'F') ? $tipoFinalizacao : $tipomotivo;
		$motivo = ($idetapa == 97 && $tipoFinalizacao == 'P') ? $motivoFinaliza : $motivo;
		$dataCompra = ($idetapa == 97 && $dataCompra == '') ? 'N' : $dataCompra;
		$dataCompra = ($idetapa == 97) ? $dataCompra : '';

		$clienteAprov = ($idetapa == 102 && $acaoEng == 'F') ? $clienteAprov : '';

		$r = !($idetapa == 102 && $acaoEng == 'F' && $clienteAprov == 'S') ? BlindaEngenharia::EtapasEngenharia($item, $idetapa, $acaoEng, $tipomotivo, $motivo, $dataCompra, $clienteAprov) : ['ERRO' => 'N'];
		$grupo = BlindaEngenharia::getGrupo($categoria);

		$docEng = [
			'NUMEROMOV' => $item['NUMEROMOV'],
			'POCLIENTE' => $item['POCLIENTE'],
			'NUMITEMPEDIDO' => $item['NUMITEMPEDIDO'],
			'SKU' => $item['SKU']
		];

		$referencia = '';

		if($idetapa == 94 && $r['ERRO'] == 'N')
		{
			$itemCategoria = [
				'NUMEROMOV' => $item['NUMEROMOV'],
				'POCLIENTE' => $item['POCLIENTE'],
				'NUMITEMPEDIDO' => $item['NUMITEMPEDIDO'],
				'SKU' => $item['SKU'],
				'CODTB1FAT' => $categoria,
				'TIPO' => $tipoTempo,
				'TEMPO' => $tempo,
				'TEMPOAPROVACAO' => $tempoAprovacao,
				'TEMPO44' => $grupo['TEMPO44'],
				'TEMPO45' => $grupo['TEMPO45'],
				'TEMPO46' => $grupo['TEMPO46'],
				'TEMPO47' => $grupo['TEMPO47'],
				'UF' => $item['UF'],
				'TEMPOUF' => $item['TEMPOUF'],
				'DATASTART' => '',
				'DATAOTIF' => '',
				'DATAOTIFAPROV' => '',
				'DATAOTIFCOMPRAS' => '',
				'DATAOTIFPRODUCAO' => ''
			];

			$getCategoriaItem = BlindaEngenharia::getGrupoItem($itemCategoria);

			$existeLinha = (($getCategoriaItem['NUMEROMOV'] == $item['NUMEROMOV']) && ($getCategoriaItem['POCLIENTE'] == $item['POCLIENTE']) && ($getCategoriaItem['SKU'] == $item['SKU']));

			if(!$existeLinha)
			{
				$dataStart = new DateTime($item['DATASTART']);
				$dataChecklist = new DateTime($item['DATACHECKLIST']);
				$dataOTIF = new DateTime($item['DATAOTIF']);
				$dataOTIFAPROV = new DateTime($item['DATACHECKLIST']);
				$dataOTIFCOMPRAS = new DateTime($item['DATACHECKLIST']);
				$tempoCompras = 3 + $tempoAprovacao; //Tempo processamento (2 dias) + etapa pré-análise (10h)
				$dataOTIFPRODUCAO = new DateTime($item['DATAOTIF']);
				$etapasProducao = ceil(($item['QTDE'] * ($grupo['TEMPO44'] + $grupo['TEMPO45'] + $grupo['TEMPO46'] + $grupo['TEMPO47']))/60);
				$tempoProducao = ceil(($itemCategoria['TEMPO'] + $etapasProducao)/10) + $itemCategoria['TEMPOUF'];
				
				$itemCategoria['DATASTART'] = $dataStart->format('Y-m-d');
				$itemCategoria['DATACHECKLIST'] = $dataChecklist->format('Y-m-d');
				$itemCategoria['DATAOTIF'] = $dataOTIF->format('Y-m-d');
				$itemCategoria['DATAOTIFAPROV'] = $dataOTIFAPROV->modify("+{$tempoAprovacao} days")->format('Y-m-d');
				$itemCategoria['TEMPOCOMPRAS'] = $tempoCompras;
				$itemCategoria['DATAOTIFCOMPRAS'] = $dataOTIFCOMPRAS->modify("+{$tempoCompras} days")->format('Y-m-d');
				$itemCategoria['TEMPOPRODUCAO'] = $tempoProducao;
				$itemCategoria['DATAOTIFPRODUCAO'] = $dataOTIFPRODUCAO->modify("-{$tempoProducao} days")->format('Y-m-d');

				BlindaEngenharia::insertGrupo($itemCategoria);

			} else {

				$dataStart = ($getCategoriaItem['DATASTART'] != '') ? new DateTime($getCategoriaItem['DATASTART']) : new DateTime($item['DATASTART']);
				$dataChecklist = ($getCategoriaItem['DATACHECKLIST'] != '') ? new DateTime($getCategoriaItem['DATACHECKLIST']) : new DateTime($item['DATACHECKLIST']);

				$dataOTIF = ($getCategoriaItem['DATAOTIF'] != '') ? new DateTime($getCategoriaItem['DATAOTIF']) : new DateTime($item['DATAOTIF']);
				$dataOTIFAPROV = ($getCategoriaItem['DATACHECKLIST'] != '') ? new DateTime($getCategoriaItem['DATACHECKLIST']) : new DateTime($item['DATACHECKLIST']);
				$tempoAprovCli = ($tempoAprovacao > 0) ? $tempoAprovacao : $tempo/10;

				$dataOTIFCOMPRAS = ($getCategoriaItem['DATACHECKLIST'] != '') ? new DateTime($getCategoriaItem['DATACHECKLIST']) : new DateTime($item['DATACHECKLIST']);
				$tempoCompras = $tempoAprovCli + 2; //2 - 10h para SKU e 10h para BOM

				$dataOTIFPRODUCAO = ($getCategoriaItem['DATAOTIF'] != '') ? new DateTime($getCategoriaItem['DATAOTIF']) : new DateTime($item['DATAOTIF']);
				$etapasProducao = ceil(($item['QTDE'] * ($grupo['TEMPO44'] + $grupo['TEMPO45'] + $grupo['TEMPO46'] + $grupo['TEMPO47']))/60);
				$tempoProducao = ceil(($etapasProducao)/10) + $itemCategoria['TEMPOUF'];				

				$itemCategoria['DATASTART'] = $dataStart->format('Y-m-d');
				$itemCategoria['DATACHECKLIST'] = $dataChecklist->format('Y-m-d');
				$itemCategoria['DATAOTIF'] = $dataOTIF->format('Y-m-d');

				$itemCategoria['DATAOTIFAPROV'] = ($tempoAprovacao > 0) ? $dataOTIFAPROV->modify("+{$tempoAprovCli} days")->format('Y-m-d') : null;

				$itemCategoria['TEMPOCOMPRAS'] = $tempoCompras;
				$itemCategoria['DATAOTIFCOMPRAS'] = $dataOTIFCOMPRAS->modify("+{$tempoCompras} days")->format('Y-m-d');

				$itemCategoria['TEMPOPRODUCAO'] = $tempoProducao;
				$itemCategoria['DATAOTIFPRODUCAO'] = $dataOTIFPRODUCAO->modify("-{$tempoProducao} days")->format('Y-m-d');

				BlindaEngenharia::updateGrupo($itemCategoria);
			}
		}

		if($idetapa == 95 && $r['ERRO'] == 'N')
		{
			/*$docEng = [
				'NUMEROMOV' => $item['NUMEROMOV'],
				'POCLIENTE' => $item['POCLIENTE'],
				'NUMITEMPEDIDO' => $item['NUMITEMPEDIDO'],
				'SKU' => $item['SKU']
			];*/

			$docsEngBD = BlindaEngenharia::getDocumentosItem($docEng);
			$docsBD = $docsEngBD[0];

			/*foreach($docsEngBD[0] as $row) {
				array_push($docsBD, $row['ID_TBLINDADOC']);
			}*/	
			
			//
			//print_r($docsEngBD);

			$valuesDoc = explode(',', $valuesDoc);
			//print_r($valuesDoc);
			
			//$docsInsert = (is_array($docsBD) && !empty($docsBD)) ? array_diff($valuesDoc, $docsBD) : $valuesDoc; //array_diff(is_array($valuesDoc) ? $valuesDoc : [], is_array($docsBD) ? $docsBD : []);
			//$docsDel = (is_array($docsBD) && !empty($docsBD)) ? array_diff($docsBD, $valuesDoc) : []; //array_diff(is_array($docsBD) ? $docsBD : [], is_array($valuesDoc) ? $valuesDoc : []);

			//print_r($docsInsert);
			//print_r($docsDel);

			//foreach($docsInsert as $doc)
				//echo $doc;

			$docsInsert = array_diff($valuesDoc, $docsBD);
			$docsDel = array_diff($docsBD, $valuesDoc);

			foreach($docsDel as $doc) {
				$docEng['ID_TBLINDADOC'] = $doc;
				BlindaEngenharia::deleteDoc($docEng);
			}

			foreach($docsInsert as $doc)
			{
				$docEng['ID_TBLINDADOC'] = $doc;
				BlindaEngenharia::insertDoc($docEng);
			}
		}

		if($idetapa == 96 /*&& $r['ERRO'] == 'N'*/)
		{
			//print_r($valuesddlDoc);
			$valuesddlDoc = explode(',', $valuesddlDoc);
			
			//print_r($valuesddlDoc);
			
			foreach($valuesddlDoc as $row) {
				$row = explode('-', $row);
				$docEng['ID_TBLINDADOC'] = $row[0];
				$docEng['STATUS'] = $row[1];
				//$docEng['ID_TBLINDADOC'] = $row['id'];
				//$docEng['STATUS'] = $row['value'];
				BlindaEngenharia::updateDoc($docEng);
			}
		}

		//$dataCompra == 'S'
		if($idetapa == 97 && $acaoEng == 'F' && $tipoFinalizacao == 'T' && $r['ERRO'] == 'N' && $item['SKU_ENG'] == 'FT' && $itemGrupo['DATACOMPRAS'] == '')
		{
			BlindaEngenharia::datasLinha($item, 'C');

			$idmov = BlindaEngenharia::getVerificaItensEngenharia($item['NUMEROMOV'], $item['POCLIENTE']);
			
			// SE IDMOV > 0, TODOS OS ITENS DAQUELE PROCESSO E PO JÁ FORAM LIBERADOS PARA COMPRAS, ENTÃO FECHA ETAPA 3 E ABRE ETAPA 29
			if($idmov > 0) {
				BlindaEngenharia::fecharEtapaProcesso(3, $item['NUMEROMOV'], $item['POCLIENTE'], $_SESSION[Config::$uniqid]['USUARIO']);
				BlindaEngenharia::abrirEtapaProcesso(29, $idmov, $item['NUMEROMOV'], $item['POCLIENTE'], $_SESSION[Config::$uniqid]['USUARIO']);
			}
		}

		//echo "{$idetapa} {$r['ERRO']} {$item['MOV']} {$sku} {$skunovo}";

		if($idetapa == 98 && $r['ERRO'] == 'N' && $item['CODTMV'] == '2.1.04' && $sku != $skunovo && $skunovo != '' && $acaoEng == 'F')
		{
			$infoSKU = BlindaEngenharia::getInfoSKU($item['NUMEROMOV'], $item['SKU'], $item['POCLIENTE'], $item['NUMITEMPEDIDO']);

			$sku = BlindaEngenharia::getSKU($item['SKU']);
			$skunovo = BlindaEngenharia::getSKU($skunovo);

			if($skunovo['IDPRD'] != '')
			{
				$sku = $skunovo['SKU'];
				$referencia = $skunovo['REF'];
				BlindaEngenharia::updateSKU($infoSKU, $skunovo);
				BlindaEngenharia::updateEtapasEng($skunovo, $item);
				BlindaEngenharia::updateItemEng($skunovo, $item);
				BlindaEngenharia::updateDocEng($skunovo, $item);
				BlindaEngenharia::updateNomeCKL($infoSKU, $skunovo);
				BlindaEngenharia::updateValorCKL($item['NUMEROMOV'], $item['POCLIENTE'], $item['SKU'], $skunovo['SKU'], $infoSKU['NSEQITMMOV']);
			}
		}

		if
		(
			$idetapa == 100 && $tipoFinalizacao == 'T' && 
			$item['PRE_ENG'] == 'F' && $item['CKL_ENG'] == 'F' && $item['DOC_ENG'] == 'FT' && 
			($item['APROV_ENG'] == 'NI' || $item['APROV_ENG'] == 'F') && $item['BOM_ENG'] == 'FT' && $item['SKU_ENG'] == 'FT' &&
			($item['VEND_ENG'] == 'NI' || $item['VEND_ENG'] == 'F') && ($item['CLI_ENG'] == 'NI' || $item['CLI_ENG'] == 'F') &&
			$itemGrupo['DATAPRODUCAO'] == ''
		)
		{
			BlindaEngenharia::datasLinha($item, 'P');
		}

		if($idetapa == 102 /*&& $r['ERRO'] == 'N'*/)
		{
			$firstAprov = BlindaEngenharia::getTotalAprov($item);
			if($firstAprov == 1) {
				BlindaEngenharia::updateDataAprov($item);
			}

			if($acaoEng == 'F' && $clienteAprov == 'S')
			{
				$nomeArquivo = NomeArquivo($_FILES['fileEmailCliente']['name']);
				UploadEngenharia($_FILES['fileEmailCliente'], "{$item['NUMEROMOV']}-{$item['NUMITEMPEDIDO']}-".date('Ymd'), $nomeArquivo);

				$filename = "../upload/aprovacao/{$item['NUMEROMOV']}-{$item['NUMITEMPEDIDO']}-".date('Ymd').'/'."anexo-{$nomeArquivo}";
				
				// Faz o parsing do arquivo eml
				$mail = mailparse_msg_parse_file($filename);
				$data = mailparse_msg_get_part_data($mail);

				// Extrai o cabeçalho 'Date'
				$dateHeader = $data['headers']['date'];

				// Converte para um formato legível
				$dtfim = new DateTime($dateHeader);
				$dtfim->modify("-3 hours");
				//$dateTime = strtotime($dateHeader);
				//echo "Data do E-mail: " . $dateTime->format('d/m/Y H:i:s');
				//echo "Data do E-mail: " . date('Y-m-d H:i:s', $dateTime);

				mailparse_msg_free($mail);

				$r = BlindaEngenharia::EtapasEngenharia($item, $idetapa, $acaoEng, $tipomotivo, $motivo, $dataCompra, $clienteAprov, $dtfim->format('Y-m-d H:i:s'), str_replace('../upload/aprovacao/', '', $filename));

				BlindaEngenharia::updateTempoCliente($item['NUMEROMOV'], $item['POCLIENTE'], $item['NUMITEMPEDIDO']);
				BlindaEngenharia::OTIFPO($item['NUMEROMOV'], $item['POCLIENTE']);
			}
		}
	
		echo json_encode(['erro' => $r['ERRO'], 'msg' => $r['MSG'], 'sku' => $sku, 'referencia' => $referencia]);
	}
	elseif($acao == 'compras')
	{
		$resultado = BlindaCompras::BlindaMRP($status);
		
		echo json_encode(['data' => $resultado]);
	}
	elseif($acao == 'mrp')
	{
		$resultado = BlindaCompras::MRP($tipo);

		$processosPorSku = [];

		foreach ($resultado['ORIGEM_DEMANDA'] as $demanda) {
			$sku = ($demanda['NIVEL'] == 'ROOT') ? $demanda['PAI_SKU'] : $demanda['SKUCOMP'];
			$processo = "{$demanda['PROCESSO']}-{$demanda['NUMITEMPEDIDO']}";
			
			// Agrupa os processos dentro da chave do SKU correspondente
			//$processosPorSku[$sku][] = $processo;
			$processosPorSku[$sku][$processo] = true;
		}

		$mrp = $resultado['SKU'];

		foreach ($mrp as &$produto) {
			$sku = $produto['SKU'];
			
			// Busca no mapa criado. Se não existir, retorna um array vazio.
			$processosEncontrados = $processosPorSku[$sku] ?? [];
			
			// Aqui você pode salvar como Array ou como String separada por vírgula.
			// Como no seu exemplo estava "1,2,3,4,5", vamos usar o implode():
			//$produto['PROCESSOS'] = implode(',', $processosEncontrados);
			$produto['PROCESSOS'] = implode(',', array_keys($processosEncontrados));
		}
		unset($produto);

		//$mrp = BlindaCompras::MRP(1);
		
		echo json_encode(['data' => $mrp]);
	} elseif($acao == 'mps')
	{
		$resultado = BlindaCompras::MPS($tipo);

		$skupai = [];
		$skufilho = [];
		foreach ($resultado as $demanda) {
			if($demanda['NIVEL'] == 'ROOT') 
				$skupai[] = $demanda;
			else
				$skufilho[] = $demanda;
		}

		// Percorre cada pai usando o & para conseguir modificar o array original diretamente
		foreach ($skupai as &$pai) {
			// Inicializa o array de filhos vazio para este pai
			$pai['FILHOS'] = [];
			
			// Varre todos os filhos para encontrar os correspondentes
			foreach ($skufilho as $filho) {
				// Verifica se a CHAVE_DEMANDA do pai é igual à do filho
				if ($pai['CHAVE_DEMANDA'] === $filho['CHAVE_DEMANDA']) {
					// Adiciona o filho encontrado dentro deste pai
					$pai['FILHOS'][] = $filho;
				}
			}
		}
		// Boa prática: destrói a referência para evitar bugs se a variável for usada depois
		unset($pai); 

		//echo '<pre>'.$skupai.'</pre>';

		/*$processosPorSku = [];

		foreach ($resultado['ORIGEM_DEMANDA'] as $demanda) {
			$sku = ($demanda['NIVEL'] == 'ROOT') ? $demanda['PAI_SKU'] : $demanda['SKUCOMP'];
			$processo = "{$demanda['PROCESSO']}-{$demanda['NUMITEMPEDIDO']}";

			$processosPorSku[$sku][$processo] = true;
		}

		$mrp = $resultado['SKU'];

		foreach ($mrp as &$produto) {
			$sku = $produto['SKU'];
			
			// Busca no mapa criado. Se não existir, retorna um array vazio.
			$processosEncontrados = $processosPorSku[$sku] ?? [];
			
			// Aqui você pode salvar como Array ou como String separada por vírgula.
			// Como no seu exemplo estava "1,2,3,4,5", vamos usar o implode():
			$produto['PROCESSOS'] = implode(',', array_keys($processosEncontrados));
		}
		unset($produto);
		
		echo json_encode(['data' => $mrp]);*/

		echo json_encode(['data' => $skupai]);
	}