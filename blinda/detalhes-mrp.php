<?php
    ob_start();
	session_start();
	require_once("../config.php");
	require_once("../php/verifica.php");
	extract($_POST);

    $mrp = BlindaCompras::MRP(3, $_POST['SKU']);
    $ocs = BlindaCompras::OrdensCompra($_POST['SKU']);
    $sku = $mrp['SKU'][0];
    $demandas = $mrp['ORIGEM_DEMANDA'];

    $fabricante = ($sku['CODFAB'] != '' && $sku['FABRICANTE'] != '') ? "Fabricante: {$sku['CODFAB']} | {$sku['FABRICANTE']} &#10;&#10;" : '';

    $estoques = [
        'ESTOQUE_SP_01_01' => $sku['ESTOQUE_SP_01_01'], 
        'ESTOQUE_SP_01_40_MAT_PRI' => $sku['ESTOQUE_SP_01_40_MAT_PRI'], 
        'ESTOQUE_SP_01_41_EMB' => $sku['ESTOQUE_SP_01_41_EMB'], 
        'ESTOQUE_SP_01_43_PROD_FINAL' => $sku['ESTOQUE_SP_01_43_PROD_FINAL'], 
        'ESTOQUE_SP_01_44_EVER' => $sku['ESTOQUE_SP_01_44_EVER'], 
        'ESTOQUE_SP' => $sku['ESTOQUE_SP'], 
        'ESTOQUE_ES' => $sku['ESTOQUE_ES'], 
        'ESTOQUE_M' => $sku['ESTOQUE_M'], 
        'ESTOQUE_RJ_3' => $sku['ESTOQUE_RJ_3']
    ];

    $compras = ['OC_SP' => $sku['OC_SP'], 'OC_M' => $sku['OC_M'], 'OC_ES' => $sku['OC_ES'], 'OC_RJ_3' => $sku['OC_RJ_3']];

    $sku['SALDO'] = $sku['SALDO'] + $sku['SALDO_RASCUNHO'];
    $sku['DISPONIVEL'] = $sku['DISPONIVEL'] + $sku['SALDO_RASCUNHO'];

    $alerta = '';

    if(($sku['RAS_ENTRADA'] > 0 || $sku['RAS_SAIDA'] > 0) && $sku['RAS_ENTRADA'] != $sku['RAS_SAIDA'])
        $alerta = "<div class='row text-danger font-weight-bold'>
                        <div class='col-12 pb-2 ml-5'>
                            <span>&#9888;</span>
                            <strong>ATENÇÃO:</strong> O rascunho de entrada ({$sku['RAS_ENTRADA']}) está diferente do rascunho de saída ({$sku['RAS_SAIDA']}).
                        </div>
                   </div>";
    elseif($sku['RAS_ENTRADA'] > 0 && $sku['RAS_SAIDA'] > 0 && $sku['RAS_ENTRADA'] == $sku['RAS_SAIDA'] && $sku['SALDO_RASCUNHO'] == 0)
    {
        $totalRascunho = ($sku['RAS_ENTRADA'] > 0) ? $sku['RAS_ENTRADA'] : $sku['RAS_SAIDA'];
        
        $alerta = "<div class='row text-warning font-weight-bold'>
                        <div class='col-12 pb-2 ml-5'>
                            <span>&#128712;</span>
                            <strong>AVISO:</strong> Existe rascunho para esse sku.
                        </div>
                   </div>";
    }

    $blindaEstoque = BlindaCompras::BlindaEstoque($sku['SKU']);
    $avisoDisponivel = ($blindaEstoque['DISPONIVEL'] != $sku['DISPONIVEL']) ? 'style="font-style: italic;"' : ''; // font-style: italic text-decoration:underline
?>

<style>
    
    .tooltip-sku {
        text-align: left;
        cursor: pointer;
        --balloon-font-size: 13px;
        --balloon-width: 800px;
    }

    /*#item-eng th, #item-eng td, #btnIniciar, #btnPausar, #btnConfPausar, #btnFinalizar {
        font-size:12.5px !important;
    }*/

    #item-eng th {
        width:150px;
    }

    .card a {
        color:#FFFFFF;
    }

    .card a:hover {
        color:#007bff;
    }

</style>

<input type="hidden" id="hdSKU" value="<?= $sku['SKU']; ?>" />

<?=  $alerta; ?>

<div class="row">
    <div class="col-12 pb-2 ml-5">
        <div class="card bg-light mb-2 mr-2 float-left" style="width:16rem; height: 130px;">
            <div class="card-header text-center">
                <h4 class="tooltip-sku" aria-label="<?= "Referência: {$sku['REFERENCIA']} &#10;&#10; {$fabricante} Descrição: {$sku['DESCRICAO']}" ?>" data-balloon-pos="right" data-balloon-length="xlarge" data-balloon-break="">SKU <?= $sku['SKU']; ?> *</h4>
            </div>
            <div class="card-body" style="font-size: 13px;">
                <!--<h5 class="card-title"></h5>-->
                <p class="card-text">
                    <?= ($sku['FABRICANTE'] != '') ? "<b>REF:</b> {$sku['REFERENCIA']}<br /><b>FAB:</b> {$sku['FABRICANTE']}" : "<b>REF:</b> {$sku['REFERENCIA']}"; ?>
                </p>
            </div>
        </div>
        <div class="card text-white bg-dark mb-3 mr-2 float-left" style="width: 12rem; height: 130px;">
            <div class="card-header text-center text-light"><h4>ESTOQUE</h4></div>
            <div class="card-body text-light">
                <h1 class="text-center"><?= ($sku['ESTOQUE'] > 0) ? "<a href='javascript: void(0)' onclick='viewCards(`card-estoques`)'>{$sku['ESTOQUE']}</a>" : $sku['ESTOQUE']; ?></h1>
            </div>
        </div>
        <div class="card text-white bg-danger mb-3 mr-2 float-left" style="width: 12rem; height: 130px;">
            <div class="card-header text-center text-light"><h4>DEMANDA</h4></div>
            <div class="card-body text-light">
                <h1 class="text-center"><?= (count($demandas) > 0) ? "<a href='javascript: void(0)' onclick='viewCards(`card-demandas`)'>{$sku['DEMANDA']}</a>" : $sku['DEMANDA']; ?></h1>
            </div>
        </div>
        <div class="card text-white bg-warning mb-3 mr-2 float-left" style="width: 12rem; height: 130px;">
            <div class="card-header text-center text-light"><h4>RASCUNHO</h4></div>
            <div class="card-body text-light">
                <h1 class="text-center">
                    <?php
                        $cardRascunho = '';

                        switch(true)
                        {
                            case ($sku['SALDO_RASCUNHO'] == 0 && $sku['RAS_ENTRADA'] == 0 && $sku['RAS_SAIDA'] == 0): echo $sku['SALDO_RASCUNHO']; break;
                            case ($sku['SALDO_RASCUNHO'] == 0 && ($sku['RAS_ENTRADA'] > 0 || $sku['SALDO_RASCUNHO'] > 0)): echo "<a href='javascript: void(0)' onclick='viewCards(`card-rascunho`)'>{$sku['SALDO_RASCUNHO']}*</a>"; break;
                            case ($sku['SALDO_RASCUNHO'] != 0): echo "<a href='javascript: void(0)' onclick='viewCards(`card-rascunho`)'>{$sku['SALDO_RASCUNHO']}</a>"; break;
                        }                        
                    ?>
                </h1>
            </div>
        </div>
        <div class="card text-white bg-secondary mb-3 mr-2 float-left" style="width: 12rem; height: 130px;">
            <div class="card-header text-center text-light"><h4>SALDO</h4></div>
            <div class="card-body text-light">
                <h1 class="text-center"><?= $sku['SALDO']; ?></h1>
            </div>
        </div>
        <div class="card text-white bg-info mb-3 mr-2 float-left" style="width: 12rem; height: 130px;">
            <div class="card-header text-center text-light"><h4>COMPRAS</h4></div>
            <div class="card-body text-light">
                <h1 class="text-center"><?= ($sku['COMPRAS'] > 0) ? "<a href='javascript: void(0)' onclick='viewCards(`card-compras`)'>{$sku['COMPRAS']}</a>" : $sku['COMPRAS']; ?></h1>
            </div>
        </div>
        <div class="card text-white bg-success mb-3 mr-2 float-left" style="width: 12rem; height: 130px;">
            <div class="card-header text-center text-light"><h4>DISPONÍVEL</h4></div>
            <div class="card-body text-light">
                <h1 class="text-center"><?= (count($demandas) > 0) ? "<a href='javascript: void(0)' onclick='viewCards(`card-disponivel`)' {$avisoDisponivel}>{$sku['DISPONIVEL']}</a>" : "<span {$avisoDisponivel}>{$sku['DISPONIVEL']}</span>"; ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div id="card-estoques" class="col-12 ml-5">
        <table class="table table-striped table-bordered bg-dark text-white" style="width:auto">
			<thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">ESTOQUE</th>
                    <th scope="col" class="text-center">LOCAL</th>
                    <th scope="col" class="text-center">QTDE</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $totalGeral = 0;
                foreach($estoques as $coluna => $estoque)
                {
                    if($estoque > 0)
                    {
                        if(in_array($coluna, ['ESTOQUE_SP', 'ESTOQUE_ES', 'ESTOQUE_M', 'ESTOQUE_RJ_3']))
                        {
                            $totalGeral += $estoque;
                            echo 
                            "<tr>
                                <th scope='col' colspan='2' class='text-right'>TOTAL {$coluna}</th>
                                <td scope='row' class='text-center'>{$estoque}</td>
                            </tr>
                            <tr>
                                <th scope='col' colspan='3'>&nbsp;</th>
                            </tr>";
                        }
                        else
                        {
                            $coluna = str_replace('ESTOQUE_SP_', '', $coluna);
                        
                            echo 
                            "<tr>
                                <th scope='col'>ESTOQUE_SP</th>
                                <th scope='col'>{$coluna}</th>
                                <td scope='row' class='text-center'>{$estoque}</td>
                            </tr>";
                        }
                    }
                }

                echo 
                "<tr>
                    <th scope='col' colspan='2' class='text-right'>TOTAL GERAL</th>
                    <td scope='row' class='text-center'>{$totalGeral}</td>
                </tr>";
                ?>
            </tbody>
        </table>
    </div>

    <div id="card-demandas" class="col-12 ml-5">
        <table class="table table-striped table-bordered bg-danger text-white" style="width:auto">
			<thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">PROCESSO | OP</th>
                    <th scope="col" class="text-center">BU</th>
                    <th scope="col" class="text-center">MOV</th>
                    <th scope="col" class="text-center">PO</th>
                    <th scope="col" class="text-center">ITEM</th>
                    <th scope="col" class="text-center">SKU PAI</th>
                    <th scope="col" class="text-center">SKU COMP</th>
                    <th scope="col" class="text-center">NIVEL</th>
                    <th scope="col" class="text-center">ENTREGA</th>
                    <th scope="col" class="text-center">QTDE</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $totalDemanda = 0;
                $htmlDemandas = '';
                $htmlDisponivel = '';
                $estoqueSKU = $sku['ESTOQUE'];
                $totalEstoque = 0;
                $rascunhoSKU = $sku['SALDO_RASCUNHO']; //150 040029
                $totalRascunho = 0;
                $comprasSKU = $sku['COMPRAS']; //50 040029
                $totalCompras = 0;
                $comprarLinha = 0;
                $totalComprar = 0;

                foreach ($demandas as $demanda) {
                    $dataEntrega = new DateTime($demanda['DTENTREGAITEM']);
                    $qtdeDemanda = $demanda['QTDE'];
                    $totalDemanda += $qtdeDemanda;

                    $cssClass = '';
                    $rascunhoLinha = 0;
                    $comprasLinha = 0;
                    $comprarLinha = 0;

                    switch (true) {
                        // 1. Atendido 100% pelo ESTOQUE (Verde)
                        case ($estoqueSKU >= $qtdeDemanda):
                            $cssClass = 'class="bg-success"';
                            $totalEstoque += $qtdeDemanda;
                            $estoqueSKU -= $qtdeDemanda;
                            break;

                        // 2. Atendido parcial ou totalmente pelo RASCUNHO (Laranja)
                        case (($estoqueSKU + $rascunhoSKU) >= $qtdeDemanda):
                            $cssClass = 'class="bg-warning"';
                            $restoDemanda = $qtdeDemanda - $estoqueSKU;
                            
                            $totalEstoque += $estoqueSKU;
                            $estoqueSKU = 0;

                            $rascunhoSKU -= $restoDemanda;
                            $rascunhoLinha = $restoDemanda;
                            $totalRascunho += $restoDemanda;
                            break;

                        // 3. Atendido parcial ou totalmente por COMPRAS (Azul)
                        case (($estoqueSKU + $rascunhoSKU + $comprasSKU) >= $qtdeDemanda):
                            $cssClass = 'class="bg-info"';
                            $restoDemanda = $qtdeDemanda - ($estoqueSKU + $rascunhoSKU);

                            $totalEstoque += $estoqueSKU;
                            $estoqueSKU = 0;

                            $totalRascunho += $rascunhoSKU;
                            $rascunhoLinha = $rascunhoSKU;
                            $rascunhoSKU = 0;

                            $comprasSKU -= $restoDemanda;
                            $comprasLinha = $restoDemanda;
                            $totalCompras += $restoDemanda;
                            break;

                        // 4. NÃO atende (Vermelho) - Precisa comprar a diferença restante
                        default:
                            $cssClass = 'class="bg-danger"';
                            $falta = $qtdeDemanda - ($estoqueSKU + $rascunhoSKU + $comprasSKU);

                            $totalEstoque += $estoqueSKU;
                            $estoqueSKU = 0;

                            $totalRascunho += $rascunhoSKU;
                            $rascunhoLinha = $rascunhoSKU;
                            $rascunhoSKU = 0;

                            $totalCompras += $comprasSKU;
                            $comprasLinha = $comprasSKU;
                            $comprasSKU = 0;

                            $comprarLinha = $falta;
                            $totalComprar += $falta;
                            break;
                    }

                    $htmlDemandas .= 
                    "<tr>
                        <th class='text-center'>{$demanda['PROCESSO']}</th>
                        <th class='text-center'>{$demanda['BU']}</th>
                        <th class='text-center'>{$demanda['CODTMV']}</th>
                        <th>{$demanda['POCLIENTE']}</th>
                        <th class='text-center'>{$demanda['NUMITEMPEDIDO']}</th>
                        <th class='text-center'>{$demanda['PAI_SKU']}</th>
                        <th class='text-center'>{$demanda['SKUCOMP']}</th>
                        <th class='text-center'>{$demanda['NIVEL']}</th>
                        <th class='text-center'>{$dataEntrega->format('d/m/Y')}</th>
                        <th class='text-center'>{$demanda['QTDE']}</th>
                    </tr>";

                    // Formatação visual para hifenizar zeros nas células das linhas
                    $exibirEstoque  = ($estoqueSKU <= 0)  ? '-' : $estoqueSKU;
                    $exibirRascunho = ($rascunhoLinha <= 0) ? '-' : $rascunhoLinha;
                    $exibirCompras  = ($comprasLinha <= 0)  ? '-' : $comprasLinha;
                    $exibirComprar  = ($comprarLinha <= 0)  ? '-' : $comprarLinha;

                    $htmlDisponivel .= 
                    "<tr {$cssClass}>
                        <th class='text-center'>{$demanda['PROCESSO']}</th>
                        <th class='text-center'>{$demanda['BU']}</th>
                        <th class='text-center'>{$demanda['CODTMV']}</th>
                        <th>{$demanda['POCLIENTE']}</th>
                        <th class='text-center'>{$demanda['NUMITEMPEDIDO']}</th>
                        <th class='text-center'>{$demanda['PAI_SKU']}</th>
                        <th class='text-center'>{$demanda['SKUCOMP']}</th>
                        <th class='text-center'>{$demanda['NIVEL']}</th>
                        <th class='text-center'>{$dataEntrega->format('d/m/Y')}</th>
                        <th class='text-center'>{$demanda['QTDE']}</th>
                        <th class='text-center'>{$exibirEstoque}</th>
                        <th class='text-center'>{$exibirRascunho}</th>
                        <th class='text-center'>{$exibirCompras}</th>
                        <th class='text-center'>{$exibirComprar}</th>
                    </tr>";
                }

                $totalDemanda = formataValor($totalDemanda);

                $htmlDemandas .= 
                "<tr>
                    <th scope='col' colspan='10'>&nbsp;</th>
                </tr>
                <tr>
                    <th scope='col' colspan='9' class='text-right'>TOTAL GERAL</th>
                    <th scope='col' class='text-center'>{$totalDemanda}</th>
                </tr>";

                $htmlDisponivel .= 
                "<tr>
                    <th scope='col' colspan='10'>&nbsp;</th>
                </tr>
                <tr>
                    <th scope='col' colspan='9' class='text-right'>TOTAL</th>
                    <th scope='col' class='text-center'>{$totalDemanda}</th>
                    <th scope='col' class='text-center'>{$totalEstoque}</th>
                    <th scope='col' class='text-center'>{$totalRascunho}</th>
                    <th scope='col' class='text-center'>{$totalCompras}</th>
                    <th scope='col' class='text-center'>{$totalComprar}</th>
                </tr>";

                echo $htmlDemandas;

                ?>
            </tbody>
        </table>
    </div>

    <div id="card-rascunho" class="col-12 ml-5">
        <table class="table table-striped table-bordered bg-warning text-white" style="width:auto">
			<thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">RASCUNHO</th>
                    <th scope="col" class="text-center">QTDE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope='col'>ENTRADA</th>
                    <td scope='row' class='text-center'><?= $sku['RAS_ENTRADA'] ?></td>
                </tr>
                <tr>
                    <th scope='col'>SAÍDA</th>
                    <td scope='row' class='text-center'><?= $sku['RAS_SAIDA'] ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="card-compras" class="col-12 ml-5">
        <table class="table table-striped table-bordered bg-info text-white" style="width:auto">
			<thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">LOCAL</th>
                    <th scope="col" class="text-center">QTDE</th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach($compras as $coluna => $compra)
                {
                    if($compra > 0)
                    {
                        echo 
                        "<tr>
                            <th scope='col'>{$coluna}</th>
                            <td scope='row' class='text-center'>{$compra}</td>
                        </tr>";
                    }
                }

                ?>
            </tbody>
        </table>

        <table class="table table-striped table-bordered bg-info text-white" style="width:auto">
			<thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">OC</th>
                    <th scope="col" class="text-center">OPERAÇÃO</th>
                    <th scope="col" class="text-center">FORNECEDOR</th>
                    <th scope="col" class="text-center">QTDE</th>
                    <th scope="col" class="text-center">DATA ENTREGA</th>
                    <th scope="col" class="text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach($ocs as $oc)
                {
                    $dataEntrega = new DateTime($oc['DATAENTREGA']);
                
                    echo 
                    "<tr>
                        <th scope='row' class='text-center'>{$oc['OC']}</th>
                        <th scope='row' class='text-center'>{$oc['OPERACAO']}</th>
                        <th scope='row' class='text-center'>{$oc['FORNECEDOR']}</th>
                        <td scope='row' class='text-center'>{$oc['QTDE']}</td>
                        <td scope='row' class='text-center'>{$dataEntrega->format('d/m/Y')}</td>
                        <td scope='row' class='text-center'>{$oc['STATUSITEM']}</td>
                    </tr>";
                }

                ?>
            </tbody>
        </table>
    </div>

    <div id="card-disponivel" class="col-12 ml-5">
        <table class="table table-striped table-bordered bg-light text-dark" style="width:auto">
			<thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">PROCESSO | OP</th>
                    <th scope="col" class="text-center">BU</th>
                    <th scope="col" class="text-center">MOV</th>
                    <th scope="col" class="text-center">PO</th>
                    <th scope="col" class="text-center">ITEM</th>
                    <th scope="col" class="text-center">SKU PAI</th>
                    <th scope="col" class="text-center">SKU COMP</th>
                    <th scope="col" class="text-center">NIVEL</th>
                    <th scope="col" class="text-center">ENTREGA</th>
                    <th scope="col" class="text-center">QTDE</th>
                    <th scope="col" class="text-center">ESTOQUE</th>
                    <th scope="col" class="text-center">RASCUNHO</th>
                    <th scope="col" class="text-center">COMPRAS</th>
                    <th scope="col" class="text-center">COMPRAR</th>
                </tr>
            </thead>
            <tbody>
                <?= $htmlDisponivel; ?>
            </tbody>
        </table>
    </div>
</div>

<script>

    $('#card-estoques,#card-demandas,#card-rascunho,#card-compras,#card-disponivel').hide();    

    function viewCards(idCard = ''){
        
        const elementCard = (idCard != '') ? document.getElementById(idCard) : '';
        const opacityCard = (idCard != '') ? window.getComputedStyle(elementCard).display : '';

        $('#card-estoques,#card-demandas,#card-rascunho,#card-compras,#card-disponivel').hide();

        if(opacityCard === 'none')
            $(`#${idCard}`).fadeIn(300);
    }

</script>