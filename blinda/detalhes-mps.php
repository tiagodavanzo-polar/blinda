<?php
    ob_start();
	session_start();
	require_once("../config.php");
	require_once("../php/verifica.php");
	extract($_POST);

    $mps = BlindaCompras::MPS(3, $_POST['sku']);
    $sku = [
        'SKU' => $_POST['sku'], 
        'NUMNOFABRIC' => $mps[0]['NUMNOFABRIC'], 
        'NOMEFANTASIA' => $mps[0]['NOMEFANTASIA'], 
        'ESTOQUE' => $mps[0]['ESTOQUE_INI'],
        'RASCUNHO' => $mps[0]['RASCUNHO_INI'],
        'COMPRAS' => $mps[0]['COMPRAS_INI'],
        'TOTALDEMANDA' => 0
    ];

    $totalDemanda = 0;

    $htmlSKUs = '';
    foreach ($mps as $item) 
    {
        $class = '';

        $totalDemanda += $item['QTDE'];

        switch(true)
        {
            case $item['COMPRAR'] > 0: $class = 'bg-danger'; break;
            case $item['COMPRAS'] != $item['COMPRAS_INI']: $class = 'bg-primary'; break;
            case $item['RASCUNHO'] != $item['RASCUNHO_INI']: $class = 'bg-warning'; break;
            case $item['ESTOQUE'] != $item['ESTOQUE_INI']: $class = 'bg-success'; break;
        }

        $acao = ($item['COMPRAR'] > 0) ? $item['TIPOSKU'] : '';
    
        $htmlSKUs .= '<tr class="' . $class . ' font-weight-bold">';
        $htmlSKUs .= '<td class="text-center">' . $item['PROCESSO'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['BU'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['CODTMV'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['POCLIENTE'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['NUMITEMPEDIDO'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['PAI_SKU'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['SKUCOMP'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['NIVEL'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['DTENTREGAITEM'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['QTDE'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['ESTOQUE'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['RASCUNHO'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['COMPRAS'] . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $acao . '</td>';
        $htmlSKUs .= '<td class="text-center">' . $item['COMPRAR'] . '</td>';
        $htmlSKUs .= '</tr>';
    }

    $sku['TOTALDEMANDA'] = $totalDemanda;
?>

<style>
    
    .tooltip-sku {
        text-align: left;
        cursor: pointer;
        --balloon-font-size: 13px;
        --balloon-width: 800px;
    }

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

<div class="row">

    <div class="col-12 ml-5">
        <div class="row font-weight-bold">
            <div class="col-12 pb-2">
                <?= "<strong>SKU:</strong> {$sku['SKU']} | <strong>REFERÊNCIA:</strong> {$sku['NUMNOFABRIC']} | <strong>PRODUTO:</strong> {$sku['NOMEFANTASIA']}<br />"; ?>
                <?= "<span class=\"font-weight-bold text-success\">ESTOQUE ATUAL: {$sku['ESTOQUE']}</span><br />" ?>
                <?= "<span class=\"font-weight-bold text-warning\">RASCUNHO ATUAL: {$sku['RASCUNHO']}</span><br />" ?>
                <?= "<span class=\"font-weight-bold text-primary\">COMPRAS ATUAL: {$sku['COMPRAS']}</span><br />" ?>
                <?= "<span class=\"font-weight-bold text-danger\">DEMANDA: {$sku['TOTALDEMANDA']}</span><br />" ?>
            </div>
        </div>
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
                    <th scope="col" class="text-center">AÇÃO</th>
                    <th scope="col" class="text-center">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?= $htmlSKUs; ?>
            </tbody>
        </table>
    </div>
</div>