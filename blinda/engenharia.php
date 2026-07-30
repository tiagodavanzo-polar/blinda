<?php
    ob_start();
	session_start();
	require_once("../config.php");
	require_once("../php/verifica.php");
	extract($_POST);

    $row = BlindaEngenharia::getRow($_POST['numeromov'], $_POST['sku'], $_POST['numitempedido']);
    $btnIniciar = "EtapasEng('I', '{$row['NUMEROMOV']}', '{$row['SKU']}', '{$row['REFERENCIA']}', '{$row['NUMITEMPEDIDO']}')";
    $btnPausar = "EtapasEng('P', '{$row['NUMEROMOV']}', '{$row['SKU']}', '{$row['REFERENCIA']}', '{$row['NUMITEMPEDIDO']}')";
    $btnFinalizar = "EtapasEng('F', '{$row['NUMEROMOV']}', '{$row['SKU']}', '{$row['REFERENCIA']}', '{$row['NUMITEMPEDIDO']}')";
    $btnSalvar = "EtapasEng('S', '{$row['NUMEROMOV']}', '{$row['SKU']}', '{$row['REFERENCIA']}', '{$row['NUMITEMPEDIDO']}')";

    $css = [
        'NI' => 'style="background-color: #696969; color: #FFFFFF"',
        'I' => 'style="background-color: #800080; color: #FFFFFF"',
        'P' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'PE' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'PF' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'PM' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'PP' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'PU' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'PD' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'FP' => 'style="background-color: #ffa500; color: #FFFFFF"',
        'F' => 'style="background-color: #008000; color: #FFFFFF"',
        'FT' => 'style="background-color: #008000; color: #FFFFFF"'
    ];

    $grupos = BlindaEngenharia::getGrupos();
    $grupoItem = BlindaEngenharia::getGrupoItem($row);
    $tipoTempo = ['P' => 'Padrão', 'C' => 'Customizado'];

    $documentos = BlindaEngenharia::getDocumentos();
    $documentosItem = BlindaEngenharia::getDocumentosItem($row);

    $htmlDocs = '';
    $htmlChecklists = '';
    $nomesDocumentos = '';
    foreach($documentos as $documento)
    {
        $checked = (in_array($documento['ID'], $documentosItem[0])) ? 'checked' : '';    
        
        $htmlChecklists .= 
        '<input class="form-check-input ml-0" type="checkbox" id="cbDoc'.$documento['ID'].'" name="cbDoc" value="'.$documento['ID'].'" '.$checked.'>
            <label class="form-check-label small" for="cbDoc'.$documento['ID'].'">'.$documento['NOME'].'</label><br />';

        $selected = (isset($documentosItem[1][$documento['ID']]) && $documentosItem[1][$documento['ID']] == 'S') ? 'selected' : '';
        $htmlDocs .= (in_array($documento['ID'], $documentosItem[0])) ?
        '<label class="form-check-label small pl-0" for="ddlDoc'.$documento['ID'].'">'.$documento['NOME'].'</label><br />
         <select id="ddlDoc'.$documento['ID'].'" name="ddlDoc[]" class="form-control form-control-sm">
            <option value="N">Não Concluído</option>
            <option value="S" '.$selected.'>Concluído</option>
         </select>' : '';

        $nomesDocumentos .= ($checked != '') ? "{$documento['NOME']}<br />" : '';
    }

    $condDataComprasProd = ($grupoItem['DATAOTIFCOMPRAS'] != '' && $grupoItem['DATAOTIFPRODUCAO'] != '') ? (new DateTime($grupoItem['DATAOTIFCOMPRAS'])) > (new DateTime($grupoItem['DATAOTIFPRODUCAO'])) : false;

    $showEng = (!in_array($_SESSION[Config::$uniqid]['USUARIO'], SHOWENGENHARIA)) ? 'style="display:none;"' : '';
?>

<input type="hidden" id="hdDoc" value="<?= $row['DOC_ENG']; ?>" />
<input type="hidden" id="hdAprov" value="<?= $row['APROV_ENG']; ?>" />
<input type="hidden" id="hdVendEng" value="<?= $row['VEND_ENG']; ?>" />
<input type="hidden" id="hdCliEng" value="<?= $row['CLI_ENG']; ?>" />

<div class="row">
    <div class="col-12 pb-2">
        <button type="button" class="btn btn-sm btn-outline-dark" style="margin-right: 15px" onclick="Actions('<?= $row['NUMEROMOV'];?>','<?= $row['SKU'];?>', '<?= $row['REFERENCIA'];?>', '<?= $row['NUMITEMPEDIDO'];?>')">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
            </svg>
            Atualizar
        </button>
    </div>
    <div class="col-12 pb-2">
        
        <div class="row">
            <div class="col-sm-11">
                <?= ($condDataComprasProd) ? '<div class="alert alert-danger small" role="alert">A data de liberação de compras é maior do que a data de liberação para produção</div>' : ''; ?>
            </div>
        </div>

        <details open>
            <summary class="small text-primary font-weight-bold" style="cursor:pointer; text-decoration:underline;">
                Clique para exibir/ocultar informações da linha
            </summary>
            <div class="row">
                <div class="col-sm-5">
                    <table id="info-eng" class="table table-striped small">
                        <tbody>
                            <tr>
                                <th scope="col">CATEGORIA</th>
                                <td scope="row"><?= ($grupoItem['CODTB1FAT'] != '') ? $grupoItem['CODTB1FAT'] : '-'; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TIPO TEMPO</th>
                                <td scope="row"><?= ($grupoItem['TIPO'] != '') ? $tipoTempo[$grupoItem['TIPO']] : '-'; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TEMPO ENGENHARIA</th>
                                <td scope="row"><?= ($grupoItem['TEMPO'] != '') ? "{$grupoItem['TEMPO']}h" : '-'; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TEMPO APROVAÇÃO</th>
                                <td scope="row"><?= ($grupoItem['TEMPOAPROVACAO'] > 0) ? "{$grupoItem['TEMPOAPROVACAO']} dia(s)" : 'Não tem'; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">QTDE</th>
                                <td scope="row"><?= $row['QTDE']; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TEMPO SEPARAÇÃO</th>
                                <td scope="row"><?= ($grupoItem['TEMPO44'] != '') ? "{$grupoItem['TEMPO44']}min" : '-' ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TEMPO MONTAGEM</th>
                                <td scope="row"><?= ($grupoItem['TEMPO45'] != '') ? "{$grupoItem['TEMPO45']}min" : '-' ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TEMPO INSPEÇÃO</th>
                                <td scope="row"><?= ($grupoItem['TEMPO46'] != '') ? "{$grupoItem['TEMPO46']}min" : '-' ?></td>
                            </tr>
                            <tr>
                                <th scope="col">TEMPO EMBALAGEM</th>
                                <td scope="row"><?= ($grupoItem['TEMPO47'] != '') ? "{$grupoItem['TEMPO47']}min" : '-' ?></td>
                            </tr>
                            <tr>
                                <th scope="col">UF</th>
                                <td scope="row"><?= ($grupoItem['UF'] != '') ? "{$grupoItem['UF']} - {$grupoItem['TEMPOUF']} dia(s)" : '-'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-sm-5">
                    <table id="info-eng" class="table table-striped small">
                        <tbody>
                            <tr>
                                <th scope="col">DATA START</th>
                                <td scope="row"><?= ($grupoItem['DATASTART'] != '') ? (new DateTime($grupoItem['DATASTART']))->format('d/m/Y') : ''; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">DATA CHECKLIST</th>
                                <td scope="row"><?= ($grupoItem['DATACHECKLIST'] != '') ? (new DateTime($grupoItem['DATACHECKLIST']))->format('d/m/Y') : ''; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">DATA OTIF</th>
                                <td scope="row"><?= ($grupoItem['DATAOTIF'] != '') ? (new DateTime($grupoItem['DATAOTIF']))->format('d/m/Y') : ''; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">DATA LIB APROVCLI</th>
                                <td scope="row"><?= ($grupoItem['DATAOTIFAPROV'] != '') ? (new DateTime($grupoItem['DATAOTIFAPROV']))->format('d/m/Y') : (($grupoItem['TEMPOAPROVACAO'] > 0) ? '' : 'Não tem'); ?></td>
                            </tr>
                            <tr>
                                <th scope="col">DATA APROVCLI</th>
                                <td scope="row"><?= ($grupoItem['DATAAPROV'] != '') ? (new DateTime($grupoItem['DATAAPROV']))->format('d/m/Y') : ''; ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><span <?= ($condDataComprasProd) ? 'class="text-danger"' : ''; ?>>DATA LIB COMPRAS</span></th>
                                <td scope="row"><span <?= ($condDataComprasProd) ? 'class="text-danger"' : ''; ?>><?= ($grupoItem['DATAOTIFCOMPRAS'] != '') ? (new DateTime($grupoItem['DATAOTIFCOMPRAS']))->format('d/m/Y')."  - {$grupoItem['TEMPOCOMPRAS']} dia(s)" : ''; ?></span></td>
                            </tr>
                            <tr>
                                <th scope="col">DATA COMPRAS</th>
                                <td scope="row"><?= ($grupoItem['DATACOMPRAS'] != '') ? (new DateTime($grupoItem['DATACOMPRAS']))->format('d/m/Y') : $grupoItem['DATACOMPRAS']; ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><span <?= ($condDataComprasProd) ? 'class="text-danger"' : ''; ?>>DATA LIB PRODUÇÃO</span></th>
                                <td scope="row"><span <?= ($condDataComprasProd) ? 'class="text-danger"' : ''; ?>><?= ($grupoItem['DATAOTIFPRODUCAO'] != '') ? (new DateTime($grupoItem['DATAOTIFPRODUCAO']))->format('d/m/Y')."  - {$grupoItem['TEMPOPRODUCAO']} dia(s)" : ''; ?></span></td>
                            </tr>
                            <tr>
                                <th scope="col">DATA PRODUÇÃO</th>
                                <td scope="row"><?= ($grupoItem['DATAPRODUCAO'] != '') ? (new DateTime($grupoItem['DATAPRODUCAO']))->format('d/m/Y') : $grupoItem['DATAPRODUCAO']; ?></td>
                            </tr>
                            <tr>
                                <th scope="col">DOCUMENTOS</th>
                                <td scope="row"><?= $nomesDocumentos; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
</div>
    <div class="col-10">
        <table id="item-eng" class="table table-striped table-bordered text-center" cellspacing="0">
            <thead>
                <tr>
                    <th class="text-center">ENG</th>
                    <th class="text-center">PRE</th>
                    <th class="text-center">CKL</th>
                    <th class="text-center">DOC</th>
                    <th class="text-center">APR</th>
                    <th class="text-center">SKU</th>
                    <th class="text-center">BOM</th>
                    <th class="text-center">VEN</th>
                    <th class="text-center">CLI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td <?= $css[$row['ENGENHARIA']]; ?>><?= $row['ENGENHARIA']; ?></td>
                    <td <?= $css[$row['PRE_ENG']]; ?>><?= $row['PRE_ENG']; ?></td>
                    <td <?= $css[$row['CKL_ENG']]; ?>><?= $row['CKL_ENG']; ?></td>
                    <td <?= $css[$row['DOC_ENG']]; ?>><?= $row['DOC_ENG']; ?></td>
                    <td <?= $css[$row['APROV_ENG']]; ?>><?= $row['APROV_ENG']; ?></td>
                    <td <?= $css[$row['SKU_ENG']]; ?>><?= $row['SKU_ENG']; ?></td>
                    <td <?= $css[$row['BOM_ENG']]; ?>><?= $row['BOM_ENG']; ?></td>
                    <td <?= $css[$row['VEND_ENG']]; ?>><?= $row['VEND_ENG']; ?></td>
                    <td <?= $css[$row['CLI_ENG']]; ?>><?= $row['CLI_ENG']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row" <?= $showEng; ?>>
    <div class="col-12 text-left pb-2 pt-0 ml-3">
        <span class="small font-weight-bold">Clique nos botões abaixo para iniciar, pausar ou finalizar uma etapa selecionada.</span>
    </div>
</div>

<div class="row" <?= $showEng; ?>>
    <div class="col-12">
        <div class="row ml-0 mr-0">
            <div class="col-sm-8 form-group row pr-1">
                <label for="ddlEtapa" class="col-sm-5 col-form-label small font-weight-bold pr-0">Etapa: </label>
                <div class="col-sm-7">
                    <select id="ddlEtapa" class="form-control form-control-sm" onchange="CheckEtapa()">
                        <option selected value="">Selecione uma etapa</option>
                        <option value="100">Engenharia</option>
                        <option value="94">Pré-Analise</option>
                        <option value="95">Checklist</option>
                        <option value="96">Documentação</option>
                        <?php if($grupoItem['TEMPOAPROVACAO'] > 0){ ?>
                        <option value="102">Aprovação</option>
                        <?php } ?>
                        <option value="98">SKU</option>
                        <option value="97">BOM</option>
                        <option value="99">Vendedor</option>
                        <option value="101">Cliente</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-8 form-group row pr-1 pausa">
                <label for="ddlMotivoPausa" class="col-sm-5 col-form-label small font-weight-bold pr-0">Tipo de Pausa: </label>
                <div class="col-sm-7">
                    <select id="ddlMotivoPausa" class="form-control form-control-sm">
                        <option selected value="">Selecione uma opção</option>
                        <option value="P">Processo</option>
                        <option value="D">Falta Dados</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 pausa">
                <label for="txtMotivoPausa" class="col-sm-5 col-form-label small font-weight-bold pr-0">Motivo da Pausa: </label>
                <div class="col-sm-7">
                    <textarea id="txtMotivoPausa" class="form-control form-control-sm" cols="10" rows="5"></textarea>
                </div>
            </div>

            <div class="col-sm-8 form-group row pr-1 finaliza">
                <label for="ddlFinalizar" class="col-sm-5 col-form-label small font-weight-bold">Finalizar: </label>
                <div class="col-sm-7">
                    <select id="ddlFinalizar" class="form-control form-control-sm" onchange="checkFinalizar()">
                        <option selected value="">Selecione uma opção</option>
                        <option value="P">Parcial</option>
                        <option value="T">Total</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-8 form-group row pr-1 motivovendedor">
                <label for="txtMotivoVendedor" class="col-sm-5 col-form-label small font-weight-bold pr-0">Motivo Vendedor: </label>
                <div class="col-sm-7">
                    <textarea id="txtMotivoVendedor" class="form-control form-control-sm" cols="10" rows="5"></textarea>
                </div>
            </div>

            <div class="col-sm-8 form-group row pr-1 motivofinaliza">
                <label for="txtMotivoFinaliza" class="col-sm-5 col-form-label small font-weight-bold pr-0">Motivo Finalizar Parcial: </label>
                <div class="col-sm-7">
                    <textarea id="txtMotivoFinaliza" class="form-control form-control-sm" cols="10" rows="5"></textarea>
                </div>
            </div>

            <div class="col-sm-8 form-group row pr-1 e94">
                <label for="ddlCategoria" class="col-sm-5 col-form-label small font-weight-bold">Categoria: </label>
                <div class="col-sm-7">
                    <select id="ddlCategoria" class="form-control form-control-sm" onchange="GetTempo()">
                        <option value="">Selecione uma categoria</option>
                        <?php
                            $optionsCategoria = '';
                            foreach($grupos as $grupo)
                            {
                                $selected = ($grupo['CODTB1FAT'] == $grupoItem['CODTB1FAT']) ? 'selected' : '';
                                $optionsCategoria .= '<option value="'.$grupo['CODTB1FAT'].'" '.$selected.'>'.$grupo['CODTB1FAT'].' - '.$grupo['DESCRICAO'].' - '.$grupo['TEMPO'].'h</option>';
                            }

                            echo $optionsCategoria;
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e94">
                <label for="ddlTipoTempo" class="col-sm-5 col-form-label small font-weight-bold">Tipo Tempo: </label>
                <div class="col-sm-7">
                    <select id="ddlTipoTempo" class="form-control form-control-sm" onchange="GetTempo()">
                        <option selected value="">Selecione uma opção</option>
                        <option value="P" <?= ($grupoItem['TIPO'] == 'P') ? 'selected="selected"' : ''; ?>>Padrão</option>
                        <option value="C" <?= ($grupoItem['TIPO'] == 'C') ? 'selected="selected"' : ''; ?>>Customizado</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e94">
                <label for="txtTempo" class="col-sm-5 col-form-label small font-weight-bold pr-0">Tempo Engenharia (h): </label>
                <div class="col-sm-7">
                    <input type="text" id="txtTempo" class="form-control form-control-sm" value="<?= $grupoItem['TEMPO']; ?>" <?= ($grupoItem['TIPO'] == 'P') ? 'disabled' : ''; ?>>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e94">
                <label for="txtTempoAprovacao" class="col-sm-5 col-form-label small font-weight-bold pr-0">Tempo Aprovação (dias): </label>
                <div class="col-sm-7">
                    <input type="text" id="txtTempoAprovacao" class="form-control form-control-sm" value="<?= $grupoItem['TEMPOAPROVACAO']; ?>" disabled>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e95">
                <label for="etapaChecklist" class="col-sm-5 col-form-label small font-weight-bold pr-0">Documentos: </label>
                <div class="col-sm-7" id="etapaChecklist">
                    <?= $htmlChecklists; ?>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e96">
                <label for="etapaDocumentacao" class="col-sm-5 col-form-label small font-weight-bold pr-0">Documentos: </label>
                <div class="col-sm-7" id="etapaDocumentacao">
                    <?= $htmlDocs; ?>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e102">
                <label for="ddlClienteAprov" class="col-sm-5 col-form-label small font-weight-bold">Cliente Aprovou? </label>
                <div class="col-sm-7">
                    <select id="ddlClienteAprov" class="form-control form-control-sm" onchange="checkAprov()">
                        <option value="">Selecione uma opção</option>
                        <option value="N">Não</option>
                        <option value="S">Sim</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e102">
                <label for="fileEmailCliente" class="col-sm-5 col-form-label small font-weight-bold pr-0">E-mail Aprovação Cliente (formato eml): </label>
                <div class="col-sm-7">
                    <input type="file" id="fileEmailCliente" class="form-control form-control-sm" value="" disabled>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e97">
                <label for="ddlDataCompra" class="col-sm-5 col-form-label small font-weight-bold">Seguir para Compras? </label>
                <div class="col-sm-7">
                    <select id="ddlDataCompra" class="form-control form-control-sm" <?//= ($grupoItem['DATACOMPRAS'] != '' && $row['SKU_ENG'] == 'FT') ? 'disabled' : ''; ?>>
                        <option value="">Selecione uma opção</option>
                        <option value="N">Não</option>
                        <option value="S" <?//= ($grupoItem['DATACOMPRAS'] != '') ? 'selected' : ''; ?>>Sim</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e98">
                <label for="txtSKUNovo" class="col-sm-5 col-form-label small font-weight-bold pr-0">SKU Atual: </label>
                <div class="col-sm-7">
                    <input type="text" id="txtSKUAtual" class="form-control form-control-sm" value="<?= $row['SKU']; ?>" disabled />
                </div>
            </div>
            <div class="col-sm-8 form-group row pr-1 e98">
                <label for="txtSKUNovo" class="col-sm-5 col-form-label small font-weight-bold pr-0">Novo SKU: </label>
                <div class="col-sm-7">
                    <input type="text" id="txtSKUNovo" class="form-control form-control-sm" value="<?//= $row['SKU']; ?>" <?= ($row['CODTMV'] != '2.1.04') ? 'disabled' : ''; ?> />
                </div>
            </div>
            <div class="col-sm-12 form-group pr-1">
                <button id="btnIniciar" type="button" class="btn btn-sm" style="border-radius: 10%; background-color: #800080; color: #FFFFFF;" onclick="CheckBtn('I')">INICIAR</button>
                <button id="btnConfIniciar" type="button" class="btn btn-sm" style="border-radius: 10%; background-color: #800080; color: #FFFFFF;" onclick="<?= $btnIniciar; ?>">INICIAR ETAPA</button>
                
                <button id="btnPausar" type="button" class="btn btn-sm" style="border-radius: 10%; background-color: #fe9900; color: #FFFFFF;" onclick="CheckBtn('P')">PAUSAR</button>
                <button id="btnConfPausar" type="button" class="btn btn-sm" style="border-radius: 10%; background-color: #fe9900; color: #FFFFFF;" onclick="<?= $btnPausar; ?>">PAUSAR ETAPA</button>
                
                <button id="btnFinalizar" type="button" class="btn btn-sm" style="border-radius: 10%; background-color: #008000; color: #FFFFFF;" onclick="CheckBtn('F')">FINALIZAR</button>
                <button id="btnConfFinalizar" type="button" class="btn btn-sm" style="border-radius: 10%; background-color: #008000; color: #FFFFFF;" onclick="<?= $btnFinalizar; ?>">FINALIZAR ETAPA</button>

                <button id="btnSalvar" type="button" class="btn btn-sm btn-primary" style="border-radius: 10%;" onclick="<?= $btnSalvar; ?>">SALVAR</button>

                <div id="atualizando" class="small text-center font-weight-bold"><img id="loading" src="img/load.gif" width="16"> Atualizando...</div>
            </div>
        </div>
    </div>
</div>

<style>

    #item-eng th, #item-eng td, #btnIniciar, #btnPausar, #btnConfPausar, #btnFinalizar {
        font-size:12.5px !important;
    }

    #item-eng th {
        width:150px;
    }

</style>

<script type="text/javascript">

    $('.e94,.e95,.e96,.e97,.e98,.e102,#btnConfIniciar,.pausa,#btnConfPausar,.motivofinaliza,.finaliza,#btnConfFinalizar,#atualizando,#btnSalvar,.motivovendedor').hide();

    /*if(document.getElementById('hdAprov').value != 'F')
        $('.e102').hide();
    else
        $('.e102').show();*/

    function CheckBtn(action = 'I')
    {
        const etapa = parseInt(document.getElementById('ddlEtapa').value)
        
        switch(action)
        {
            case 'I': 
                $('#btnConfIniciar').show(); 
                $('.e97,.motivofinaliza,#btnIniciar,.pausa,#btnConfPausar,.finaliza,#btnPausar,#btnFinalizar,#btnConfFinalizar').hide(); 
                break;
            case 'P': 
                $('.pausa,#btnConfPausar,#btnFinalizar').show(); 
                $('.e97,.motivofinaliza,#btnConfIniciar,#btnPausar,.finaliza,#btnIniciar,#btnConfFinalizar').hide(); 
                document.getElementById('ddlFinalizar').value = ''
                break;
            case 'F': 
                const show = (etapa == 97) ? '.e97,#btnIniciar,#btnPausar,.finaliza,#btnConfFinalizar' : '#btnIniciar,#btnPausar,.finaliza,#btnConfFinalizar';
                const hide = (etapa != 97) ? '.e97,#btnConfIniciar,.pausa,#btnConfPausar,#btnFinalizar' : '#btnConfIniciar,.pausa,#btnConfPausar,#btnFinalizar';
                $(show).show();
                $(hide).hide();
                break;
        }
    }

    function CheckEtapa()
    {
        const idetapa = parseInt(document.getElementById('ddlEtapa').value)
        const etapas = [102,99,101]
        
        if(etapas.includes(idetapa))
        {
            $('#btnIniciar,#btnPausar,.pausa,#btnConfPausar,.finaliza,#btnFinalizar').hide();
            $('#btnConfIniciar,#btnConfFinalizar').show();
        }
        else
        {
            $('#btnIniciar,#btnPausar,#btnFinalizar').show();
            $('#btnConfIniciar,.pausa,#btnConfPausar,.finaliza,#btnConfFinalizar').hide();
        }   
    
        if(idetapa == 94)
        {
            $('.e94,#btnSalvar').show();
            $('.e95,.e96,.e97,.e98,.e102').hide();
            $('#btnIniciar,#btnPausar,#btnFinalizar,#btnConfIniciar,.pausa,#btnConfPausar,.finaliza,#btnConfFinalizar').hide();
        }
        else if(idetapa == 95)
        {
            $('.e95,#btnSalvar').show();
            $('.e94,.e96,.e97,.e98,.e102').hide();
            $('#btnIniciar,#btnPausar,#btnFinalizar,#btnConfIniciar,.pausa,#btnConfPausar,.finaliza,#btnConfFinalizar').hide();
        }
        else if(idetapa == 96)
        {
            const statusEtapa = ['NI','FP','FT','PP','PD']
        
            if(!statusEtapa.includes(document.getElementById('hdDoc').value))
            //if(document.getElementById('hdDoc').value != 'NI' && document.getElementById('hdDoc').value != 'FP' && document.getElementById('hdDoc').value != 'FT')
            {
                $('#btnPausar,#btnFinalizar').show();
                $('#btnIniciar,#btnConfIniciar,#btnConfPausar,#btnConfFinalizar').hide();
            }
            else
            {
                $('#btnIniciar,#btnPausar,#btnConfPausar,#btnFinalizar,#btnConfFinalizar').hide();
                $('#btnConfIniciar').show();
            }    
        
            $('.e96').show();
            $('.e94,.e95,.e97,.e98,.e102,#btnSalvar').hide();
        }
        else if(idetapa == 97)
        {
            //$('.e97').show();
            $('.e94,.e95,.e97,.e96,.e98,.e102,#btnSalvar').hide();
        }
        else if(idetapa == 98)
        {
            $('.e98').show();
            $('.e94,.e95,.e96,.e97,.e102,#btnSalvar').hide();
        }
        else if(idetapa == 102)
        {
            if(document.getElementById('hdAprov').value == 'I')
            {
                $('.e102,#btnConfFinalizar').show();
                $('#btnConfIniciar').hide();
            }
            else
            {
                $('.e102,#btnConfFinalizar').hide();
                $('#btnConfIniciar').show();
            }

            $('.e94,.e95,.e96,.e97,.e98,#btnSalvar').hide();
        }
        else if(idetapa == 99)
        {
            if(document.getElementById('hdVendEng').value == 'I')
            {
                $('#btnConfFinalizar').show();
                $('#btnConfIniciar,.motivovendedor').hide();
            }
            else
            {
                $('#btnConfFinalizar').hide();
                $('#btnConfIniciar,.motivovendedor').show();
            }

            $('.e94,.e95,.e96,.e97,.e98,.e102,#btnSalvar').hide();
        }
        else if(idetapa == 101)
        {
            if(document.getElementById('hdCliEng').value == 'I')
            {
                $('#btnConfFinalizar').show();
                $('#btnConfIniciar').hide();
            }
            else
            {
                $('#btnConfFinalizar').hide();
                $('#btnConfIniciar').show();
            }

            $('.e94,.e95,.e96,.e97,.e98,.e102,#btnSalvar').hide();
        }
        else
            $('.e94,.e95,.e96,.e97,.e98,.e102,#btnSalvar').hide();
    }

    function checkFinalizar()
    {
        const idetapa = parseInt(document.getElementById('ddlEtapa').value)
        const tipoFin = document.getElementById('ddlFinalizar').value

        if(idetapa == 97 && tipoFin == 'P')
            $('.motivofinaliza').show();
        else
            $('.motivofinaliza').hide();
    }

    function GetTempo()
    {
        const categoria = document.getElementById('ddlCategoria');
        const textoCategoria = categoria.options[categoria.selectedIndex].text.split(' - ');
        const tempo = (textoCategoria.length == 3) ? textoCategoria[2].replace('h', '') : '';
        const tipoTempo = document.getElementById('ddlTipoTempo').value
        const txtTempo = document.getElementById('txtTempo')

        if(tipoTempo == 'P' && tempo != '' && tempo > 0)
        {
            txtTempo.value = parseInt(tempo) + 10
            txtTempo.disabled = true
        }
        else
        {
            txtTempo.value = ''
            txtTempo.disabled = false
        }
    }

    function checkAprov()
    {
        const fileEmailCliente =  document.getElementById('fileEmailCliente')
        
        if(document.getElementById('ddlClienteAprov').value == 'S')
            fileEmailCliente.disabled = false
        else
            fileEmailCliente.disabled = true

        fileEmailCliente.value = ''
    }

    function EtapasEng(acao, numeromov, sku, referencia, numitempedido)
    {
        const acoes = {'I': 'iniciar', 'P': 'pausar', 'F': 'finalizar', 'S': 'salvar'}
        const idetapa = document.getElementById('ddlEtapa')
        const tipomotivo = document.getElementById('ddlMotivoPausa')
        const motivo = document.getElementById('txtMotivoPausa')
        const motivoFinaliza = document.getElementById('txtMotivoFinaliza')
        const etapas = [102,99,101]
        const tipoFinalizacao = document.getElementById('ddlFinalizar')
        const ddlDataCompra = document.getElementById('ddlDataCompra')
        const skunovo = document.getElementById('txtSKUNovo')
        const clienteAprov = document.getElementById('ddlClienteAprov')
        const fileEmailCliente = document.getElementById('fileEmailCliente')
        let categoria = ''
        let tipoTempo = ''
        let tempo = ''
        let tempoAprovacao = ''
    
        if(idetapa.value == '')
        {
            alert('Selecione uma etapa')
            idetapa.focus()
            return false
        }
        else
        {
            if(acao == 'P' && (tipomotivo.value == '' || motivo.value == ''))
            {
                if(tipomotivo.value == '')
                {
                    alert('Selecione o motivo da pausa.')
                    tipomotivo.focus()
                }
                else if(motivo.value == '')
                {
                    alert('Descreva o motivo da pausa.')
                    motivo.focus()
                }

                return false
            }
            else if(acao == 'F' && tipoFinalizacao.value == '' && !etapas.includes(parseInt(idetapa.value)))
            {
                tipoFinalizacao.focus()

                return false
            }
            else
            {
                if(confirm(`Tem certeza que deseja ${acoes[acao]} essa etapa?`))
                {
                    if(idetapa.value == 94)
                    {
                        categoria = document.getElementById('ddlCategoria')
                        tipoTempo = document.getElementById('ddlTipoTempo')
                        tempo = document.getElementById('txtTempo')
                        tempoAprovacao = document.getElementById('txtTempoAprovacao').value

                        if(categoria.value == '')
                        {
                            alert('Selecione uma categoria.')
                            categoria.focus()
                            return false
                        }
                        else if(tipoTempo.value == '')
                        {
                            alert('Selecione se o tempo será padrão ou customizado.')
                            tipoTempo.focus()
                            return false
                        }  
                        else if(tipoTempo.value != '' && (tempo.value == '' || parseInt(tempo.value) <= 0))
                        {
                            alert('Informe o tempo da engenharia.')
                            tempo.focus()
                            return false
                        }   
                    }
                
                    const cbDoc = document.querySelectorAll('input[name="cbDoc"]:checked');
                    const valuesDoc = [];

                    if(idetapa.value == 95)
                    {
                        cbDoc.forEach((checkbox) => {
                            valuesDoc.push(checkbox.value);
                        });
                    }
                    
                    const ddlDoc = document.querySelectorAll('select[name="ddlDoc[]"]');
                    const valuesddlDoc = [];
                    let contDocConc = 0

                    if(idetapa.value == 96)
                    {
                        ddlDoc.forEach((docId) => {
                            const idDoc = docId.id.replace('ddlDoc', '');
                            valuesddlDoc.push([idDoc + '-' + docId.value ]);
                            //valuesddlDoc.push({ id: idDoc, value: docId.value });

                            contDocConc = (docId.value == 'S') ? contDocConc + 1 : contDocConc;
                        });

                        if(tipoFinalizacao.value == 'T' && ddlDoc.length > contDocConc)
                        {
                            alert('Todos os documentos precisam ser concluídos para finalizar totalmente a etapa Documentação.')

                            return false
                        }
                    }

                    if(idetapa.value == 102 && acao == 'F' && clienteAprov.value == '')
                    {
                        alert('Selecione uma opção para informar se o cliente aprovou ou não.')
                        clienteAprov.focus()
                        return false
                    }
                    else if(idetapa.value == 102 && acao == 'F' && clienteAprov.value == 'S' && fileEmailCliente.files.length === 0)
                    {
                        alert('Anexe o e-mail do cliente para finalizar a etapa.')
                        fileEmailCliente.focus()
                        return false
                    }

                    //console.log(valuesddlDoc);

                    /*console.log(ddlDoc)
                    console.log(valuesddlDoc);

                    return false*/

                    var form_data = new FormData();

                    form_data.append('acao', 'etapaseng');

                    form_data.append('acaoEng', acao);
                    form_data.append('idetapa', idetapa.value);
                    form_data.append('tipoFinalizacao', tipoFinalizacao.value);

                    form_data.append('numeromov', numeromov);
                    form_data.append('sku', sku);
                    form_data.append('numitempedido', numitempedido);
                    
                    form_data.append('tipomotivo', tipomotivo.value);
                    form_data.append('motivo', motivo.value);
                    form_data.append('motivoFinaliza', motivoFinaliza.value);

                    form_data.append('categoria', categoria.value);
                    form_data.append('tipoTempo', tipoTempo.value);
                    form_data.append('tempo', tempo.value);
                    form_data.append('tempoAprovacao', tempoAprovacao);

                    form_data.append('valuesDoc', valuesDoc);
                    form_data.append('valuesddlDoc', valuesddlDoc);
                    form_data.append('dataCompra', ddlDataCompra.value);
                    form_data.append('skunovo', skunovo.value);

                    form_data.append('clienteAprov', clienteAprov.value);

                    if (fileEmailCliente.files.length > 0)
                        form_data.append('fileEmailCliente', $('#fileEmailCliente').prop('files')[0]);

                    $.ajax({
                        type: 'POST',
                        url: `php/blinda.php`,
                        data: form_data,
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        //async: false,
                        async: true,
                        beforeSend: function() {
                            $('#atualizando').show() 
                        },
                        success: function (data) {                            
                            //data = jQuery.parseJSON(response);
                            
                            alert(data.msg)
                            
                            if(data.erro == 'N'){
                                Loading()
                                referencia = (data.referencia != '') ? data.referencia : referencia
                                $('#modalDashboardCS .modal-body').load('blinda/engenharia.php', { 'numeromov': numeromov, 'sku': data.sku, 'numitempedido': numitempedido });
                                $(`#modalDashboardCS .modal-title`).html(`Ações Engenharia | ${numeromov} | ${data.sku} | ${referencia} | Item ${numitempedido}`);
                            }
                        }
                    });
                
                    /*$.post(`php/blinda.php`, { 
                            'pag': 'etapaseng', 'acao': acao, 'idetapa': idetapa.value, 'tipoFinalizacao': tipoFinalizacao.value,
                            'numeromov': numeromov, 'sku': sku, 'numitempedido': numitempedido, 
                            'tipomotivo': tipomotivo.value, 'motivo': motivo.value, 'motivoFinaliza': motivoFinaliza.value,
                            'categoria': categoria, 'tipoTempo': tipoTempo, 'tempo': tempo, 'tempoAprovacao': tempoAprovacao,
                            'valuesDoc': valuesDoc, 'valuesddlDoc': valuesddlDoc, 'dataCompra': ddlDataCompra.value, 'skunovo': skunovo.value,
                            'clienteAprov': clienteAprov.value, 'fileEmailCliente': $('#fileEmailCliente').prop('files')[0]
                        },
                        function(data){
                            data = jQuery.parseJSON(data);
                            
                            alert(data.msg)
                            
                            if(data.erro == 'N'){
                                Loading()
                                referencia = (data.referencia != '') ? data.referencia : referencia
                                $('#modalDashboardCS .modal-body').load('blinda/engenharia.php', { 'numeromov': numeromov, 'sku': data.sku, 'numitempedido': numitempedido });
                                $(`#modalDashboardCS .modal-title`).html(`Ações Engenharia | ${numeromov} | ${data.sku} | ${referencia} | Item ${numitempedido}`);
                            }
                        }
                    );*/
                }
            }
        }
    }

</script>