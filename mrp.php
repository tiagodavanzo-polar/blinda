<?php 
	ob_start();
	session_start();
	require_once("config.php");
	require_once("php/verifica.php");
	
	$string = explode('.', $_SESSION[Config::$uniqid]['USUARIO']);
    $nome = ucfirst($string[0]);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>Intranet Polar | Compras MRP</title>
	<link rel="icon" type="image/x-icon" href="img/favicon.png">
    <!-- GLOBAL MAINLY STYLES-->
    <link href="./assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="./assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- PLUGINS STYLES-->
    <link href="./assets/vendors/DataTables/datatables.min.css" rel="stylesheet" />
    <link href="./css/open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
    <!-- THEME STYLES-->
    <link href="assets/css/main.min.css" rel="stylesheet" />
	<link href="assets/css/balloon.min.css" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES-->

	<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">-->

	<style>
	
		#tbPrecos th, #tbPrecos td
		{
			text-align: center;
		}

		.v
		{
			color: green;
			font-weight: bold;
			text-align: center;
		}

		.nv
		{
			color: red;
			font-weight: bold;
			text-align: center;
		}

		.table td, .table th
		{
			padding: 0.3rem
		}

		tr.selected{
            background-color: #007bff !important;
            color:#ffffff !important;
        }

		tr.selected td a{
            
            color:#ffffff !important;
        }

		.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
			color:#FFFFFF !important;
			background-color: #212529 !important;
		}

		div.dataTables_filter
        {
            text-align:left !important;
        }
        .dataTables_length
        {
            text-align: right !important;
        }

        div.dataTables_wrapper div.dataTables_processing
        {
            font-weight:bold;
            color: white;
            background-color: #e74c3c;
        }

		#mrp_wrapper th, #mrp td, #lblSKU, #txtSKU
		{
			white-space: nowrap;
			padding-top: 0.1rem !important;
			padding-bottom: 0.1rem !important;
			font-size:12.5px;
		}

		[data-balloon]:before, 
        [data-balloon]:after { 
            z-index: 9999; 
        }

		/* Remove as setas de navegadores baseados em WebKit (Chrome, Safari) */
		.campo-numero::-webkit-inner-spin-button,
		.campo-numero::-webkit-outer-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}

		 #mrp td:nth-child(3)
		{
			width:auto !important;
		}

		#mrp td:nth-child(4)
		{
			width:800px !important;
			text-wrap:inherit;
		}

		.table-container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
      
		/* Área de filtros externos */
		.toolbar-filtros { margin-bottom: 15px; display: flex; gap: 10px; align-items: center; }

		/* Popover */
		.filter-container { position: relative; display: inline-block; }
		.filter-trigger { padding: 8px 12px; font-size: 13px; cursor: pointer; border: 1px solid #ccc; background: #fff; border-radius: 4px; }
		.filter-popover { 
			display: none; position: absolute; top: 100%; left: 0; z-index: 100;
			background: white; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.15);
			padding: 8px; min-width: 160px; border-radius: 4px; max-height: 200px; overflow-y: auto;
			margin-top: 4px;
		}
		.filter-option { display: flex; align-items: center; gap: 8px; /*padding: 6px;*/ cursor: pointer; font-size: 13px; }
		.filter-option:hover { background: #eee; }

	</style>
</head>

<body class="fixed-navbar sidebar-mini">
    <div class="page-wrapper">
        <!-- START HEADER-->
        <header class="header">
            <div class="page-brand">
                <!--<a class="link" href="index.html">
                    <span class="brand">Intranet Polar</span>
                </a>-->
				<span class="brand">Intranet Polar</span>
            </div>
            <div class="flexbox flex-1">
                <!-- START TOP-LEFT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li>
                        <a class="nav-link sidebar-toggler js-sidebar-toggler"><i class="ti-menu"></i></a>
                    </li>
                </ul>
                <!-- END TOP-LEFT TOOLBAR-->
                <!-- START TOP-RIGHT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li class="dropdown dropdown-user">
                        <a class="nav-link dropdown-toggle link" data-toggle="dropdown">
                            <img src="./assets/img/admin-avatar.png" />
                            <span></span><?php echo $nome; ?><i class="fa fa-angle-down m-l-5"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="logout.php"><i class="fa fa-power-off"></i>Logout</a>
                        </ul>
                    </li>
                </ul>
                <!-- END TOP-RIGHT TOOLBAR-->
            </div>
        </header>
        <!-- END HEADER-->

		<?php
            require_once 'menu.php';
        ?>
        
        <div class="content-wrapper" style="min-height:1100px !important;">
			<div class="row">
				<div class="col-xl-12 pt-2">
					<h4 class="text-danger text-center">Compras MRP</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-xl-12 pl-5">
					<div class="toolbar-filtros">
						<!--<span>Filtrar por Fabricante:</span>-->
						<div class="filter-container">
							<button class="filter-trigger" id="btn-filtro-fabricante">⏳ Fabricantes</button>
							<div class="filter-popover" id="popover-filtro-fabricante"></div>
						</div>
						<div class="filter-container">
							<button class="filter-trigger" id="btn-filtro-tipo-sku">⏳ Tipo SKU</button>
							<div class="filter-popover" id="popover-filtro-tipo-sku"></div>
						</div>
						<div class="filter-container">
							<button class="filter-trigger" id="btn-filtro-processo">⏳ Processos</button>
							<div class="filter-popover" id="popover-filtro-processo"></div>
						</div>
						<div class="filter-container">
							<button id="btnLimparFiltros" class="btn btn-secondary" type="button">🧹Limpar</button>
						</div>
					</div>
				</div>
				<!--<div class="col-xl-12">
					<div class="pl-4">
						<div class="filtro-container">
							<select id="filtroFabricantes" class="form-control" multiple="multiple" style="">
								<option value="CEAG C&A">CEAG C&A</option>
								<option value="PARTES - BLINDA">PARTES - BLINDA</option>
							</select>
						</div>
					</div>
				</div>-->
			</div>
			<div class="row">
				<div class="col-xl-12">
					<span id="update" class="pl-4 small font-weight-bold">Carregando...</span><br />
					<div class="pl-4 float-left">
						<input type="hidden" id="hdTipo"value="1" />
						<input type="radio" id="painel" name="tipo" value="blinda" onclick="$('#hdTipo').val(1); $('#mrp').DataTable().ajax.reload(atualizarFiltros, false)" checked />
						<label for="painel" class="small">Blinda</label>
						<input type="radio" id="todos" name="tipo" value="todos" onclick="$('#hdTipo').val(2); $('#mrp').DataTable().ajax.reload(atualizarFiltros, false)" />
						<label for="todos" class="small">Polar</label>
						<input type="radio" id="todos" name="tipo" value="todos" onclick="$('#hdTipo').val(0); $('#mrp').DataTable().ajax.reload(atualizarFiltros, false)" />
						<label for="todos" class="small">Todos</label>
					</div>
					<button type="button" class="btn btn-sm btn-outline-dark float-right" style="margin-right: 15px" onclick="$('#mrp').DataTable().ajax.reload()">
						<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
						<path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
						</svg>
						Atualizar
					</button>
					<p>&nbsp;</p>
					<table id="mrp" class="table table-striped table-bordered table-hover" cellspacing="0">
						<thead>
							<tr>
								<th>LINHA</th>
								<th>SKU</th>
								<th>REFERÊNCIA</th>
								<th>PRODUTO</th>
								<th>CODFAB</th>
								<th>FABRICANTE</th>
								<th>ESTOQUE</th>
								<th>DEMANDA</th>
								<th>SALDO</th>
								<th>RASCUNHO</th>
								<th>COMPRAS</th>
								<th>DISPONÍVEL</th>
								<th>TIPO</th>
								<!--<th>PROCESSOS</th>-->
							</tr>
						</thead>
					</table>
				</div>
			</div>
            <footer class="page-footer">
                <div class="to-top"><i class="fa fa-angle-double-up"></i></div>
            </footer>
        </div>
    </div>

	<div id="modalDashboardCS" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:1400px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compras MRP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    
                </div>
            </div>
        </div>
    </div>

    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS-->
    <script src="assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
	<script src="assets/vendors/DataTables/datatables.min.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="assets/js/app.min.js" type="text/javascript"></script>
	<script src="assets/vendors/DataTables/moment.min.js" type="text/javascript"></script>
    <script src="assets/vendors/DataTables/datetime-moment.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->

	<!--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>-->

	<script type="text/javascript">
	
	//$(document).ready(function() {
		/*$('#filtroFabricantes').select2({
			placeholder: "Clique aqui para escolher os fabricantes...",
			allowClear: true
		});*/

		// Armazena as configurações de cada filtro ativo para podermos varrer todos em lote
		// Array global para armazenar os filtros ativos
		const filtrosRegistrados = [];

		function registrarFiltro(mrp, nomeFiltro, indiceColuna) {
			const $popover = $(`#popover-filtro-${nomeFiltro}`);
			const $trigger = $(`#btn-filtro-${nomeFiltro}`);

			if ($popover.length === 0 || $trigger.length === 0) {
				console.error(`Erro: Elementos HTML para o filtro "${nomeFiltro}" não foram encontrados.`);
				return;
			}

			// 1. Injeta o Input de Busca
			if ($popover.find('.input-busca-popover').length === 0) {
				$popover.prepend(`
					<div style="padding: 6px; border-bottom: 1px solid #eee;">
						<input type="text" 
							class="form-control form-control-sm input-busca-popover" 
							placeholder="Pesquisar..." 
							style="width: 100%; box-sizing: border-box;">
					</div>
					<div class="container-selecionar-todos" style="padding: 4px 8px; border-bottom: 1px solid #eee;">
						<label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:bold; margin:0; font-size:13px;">
							<input type="checkbox" class="chk-selecionar-todos"> Todos
						</label>
					</div>
				`);
			}

			// 2. Injeta o contêiner isolado dos Checkboxes
			if ($popover.find('.lista-checkboxes').length === 0) {
				$popover.append('<div class="lista-checkboxes" style="max-height: 180px; overflow-y: auto; padding: 4px 0;"></div>');
			}

			filtrosRegistrados.push({ mrp, nomeFiltro, indiceColuna, $popover, $trigger });

			// Evento: Abrir/Fechar Popover
			$trigger.off('click').on('click', function(e) {
				e.stopPropagation();
				filtrosRegistrados.forEach(function(outroFiltro) {
					if (outroFiltro.nomeFiltro !== nomeFiltro) {
						outroFiltro.$popover.hide();
					}
				});
				
				$popover.toggle();

				if ($popover.is(':visible')) {
					$popover.find('.input-busca-popover').focus();
				}
			});

			// Evento: Filtragem interna por texto digitado
			$popover.off('input', '.input-busca-popover').on('input', '.input-busca-popover', function() {
				const termo = $(this).val().toLowerCase().trim();
				
				$popover.find('.lista-checkboxes .filter-option').each(function() {
					const textoOpcao = $(this).text().toLowerCase();
					if (textoOpcao.includes(termo)) {
						$(this).css('display', 'flex');
					} else {
						$(this).hide();
					}
				});

				atualizarEstadoSelecionarTodos($popover);
			});

			// Evento: Clique em "(Selecionar Todos)"
			$popover.off('change', '.chk-selecionar-todos').on('change', '.chk-selecionar-todos', function() {
				const estaMarcado = $(this).is(':checked');

				if (estaMarcado) {
					// Marca apenas os checkboxes visíveis na busca
					$popover.find('.lista-checkboxes .filter-option:visible input[type="checkbox"]').prop('checked', true);
				} else {
					// Desmarca TODOS os checkboxes
					$popover.find('.lista-checkboxes input[type="checkbox"]').prop('checked', false);
				}

				aplicarFiltroDataTables(mrp, indiceColuna, $popover);
			});

			// Evento: Marcação/Desmarcação de um Checkbox individual
			$popover.off('change', '.lista-checkboxes input[type="checkbox"]').on('change', '.lista-checkboxes input[type="checkbox"]', function() {
				atualizarEstadoSelecionarTodos($popover);
				aplicarFiltroDataTables(mrp, indiceColuna, $popover);
			});
		}

		// Função auxiliar para aplicar a busca no DataTables e redesenhar
		function aplicarFiltroDataTables(mrp, indiceColuna, $popover) {
			const selecionados = $popover.find('.lista-checkboxes input:checked').map(function() {
				return $.fn.dataTable.util.escapeRegex($(this).val());
			}).get();

			if (selecionados.length > 0) {
				const buscaRegex = selecionados.join('|');
				const buscaRegexExata = '(^|,)\\s*(' + buscaRegex + ')\\s*(,|$)';
				mrp.column(indiceColuna).search(buscaRegexExata, true, false);
			} else {
				mrp.column(indiceColuna).search('');
			}

			mrp.draw();
			atualizarTodosOsPopovers();
		}

		// Função auxiliar para alinhar o estado do "Selecionar Todos" com as opções individuais
		function atualizarEstadoSelecionarTodos($popover) {
			const $visiveis = $popover.find('.lista-checkboxes .filter-option:visible input[type="checkbox"]');
			const $marcadosVisiveis = $visiveis.filter(':checked');

			const $chkTodos = $popover.find('.chk-selecionar-todos');

			if ($visiveis.length > 0 && $visiveis.length === $marcadosVisiveis.length) {
				$chkTodos.prop('checked', true).prop('indeterminate', false);
			} else if ($marcadosVisiveis.length > 0) {
				// Estado "meio marcado" quando alguns estão selecionados (opcional, deixa a UI mais moderna)
				$chkTodos.prop('checked', false).prop('indeterminate', true);
			} else {
				$chkTodos.prop('checked', false).prop('indeterminate', false);
			}
		}

		function atualizarTodosOsPopovers() {
			if (filtrosRegistrados.length === 0) return;

			filtrosRegistrados.forEach(function(config) {
				const { mrp, nomeFiltro, indiceColuna, $popover, $trigger } = config;
				const $lista = $popover.find('.lista-checkboxes');
				const $inputBusca = $popover.find('.input-busca-popover');

				const termoBuscaAtual = $inputBusca.val() || '';

				const marcadosAnteriormente = $popover.find('.lista-checkboxes input:checked').map(function() {
					return $(this).val();
				}).get();

				const buscaAtualDestaColuna = mrp.column(indiceColuna).search();
				mrp.column(indiceColuna).search('');

				$lista.empty();
				const itensUnicos = new Set();

				mrp.column(indiceColuna, { search: 'applied' }).data().each(function(valoresCelula) {
					if (valoresCelula) {
						if (typeof valoresCelula === 'string') {
							const partes = valoresCelula.split(',');
							partes.forEach(item => itensUnicos.add(item.trim()));
						} else if (Array.isArray(valoresCelula)) {
							valoresCelula.forEach(item => itensUnicos.add(String(item).trim()));
						}
					}
				});

				mrp.column(indiceColuna).search(buscaAtualDestaColuna);

				marcadosAnteriormente.forEach(item => itensUnicos.add(item));

				if (itensUnicos.size === 0) {
					$lista.append('<small style="color:#999; padding:4px 8px; display:block;">Nenhuma opção</small>');
				} else {
					Array.from(itensUnicos).sort().forEach(function(valor) {
						const isChecked = marcadosAnteriormente.includes(valor) ? 'checked' : '';
						const visivel = termoBuscaAtual === '' || valor.toLowerCase().includes(termoBuscaAtual.toLowerCase()) ? 'display: flex;' : 'display: none;';

						$lista.append(`
							<label class="filter-option" style="align-items:center; gap:8px; padding:4px 8px; cursor:pointer; ${visivel}">
								<input type="checkbox" value="${valor}" ${isChecked}> ${valor}
							</label>
						`);
					});
				}

				// Sincroniza a caixa "Selecionar Todos" após reconstruir a lista
				atualizarEstadoSelecionarTodos($popover);

				// Atualiza a contagem dos botões
				let lblFiltro = (nomeFiltro === 'tipo-sku') ? 'Tipo SKU' : nomeFiltro.charAt(0).toUpperCase() + nomeFiltro.slice(1);
				if (nomeFiltro === 'processo') lblFiltro = 'Processos';
				if (nomeFiltro === 'fabricante') lblFiltro = 'Fabricantes';

				if (marcadosAnteriormente.length > 0) {
					$trigger.text(`⏳ ${lblFiltro} (${marcadosAnteriormente.length})`);
				} else {
					$trigger.text(`⏳ ${lblFiltro}`);
				}
			});
		}

		// Inicialização dos 3 Filtros apontando para a instância do DataTables
		function atualizarFiltros() {
			// Limpa registros anteriores em caso de reinicialização
			filtrosRegistrados.length = 0; 

			// Registro dos 3 filtros com seus respectivos índices de coluna da tabela
			registrarFiltro($.fn.dataTable.Api('#mrp'), 'fabricante', 5);
			registrarFiltro($.fn.dataTable.Api('#mrp'), 'tipo-sku', 12);
			registrarFiltro($.fn.dataTable.Api('#mrp'), 'processo', 13);

			// Popula as listas de checkboxes pela primeira vez
			atualizarTodosOsPopovers();
		}

		// Fechamento genérico dos popovers ao clicar fora deles
		$(document).on('click', function(e) {
			filtrosRegistrados.forEach(function(config) {
				if (!config.$popover.is(e.target) && config.$popover.has(e.target).length === 0 && !config.$trigger.is(e.target)) {
					config.$popover.hide();
				}
			});
		});

		function limparMarcacoesPorTabela(idTabelaSemHash) {
			forcarResetVisual = true;

			let tabelaParaDesenhar = null;
			const idAlvo = String(idTabelaSemHash).trim().toLowerCase();

			filtrosRegistrados.forEach(function(config) {
				const idTabelaAtual = config.mrp.context[0]?.sTableId || $(config.mrp.table().node()).attr('id');
				
				if (idTabelaAtual && idTabelaAtual.trim().toLowerCase() === idAlvo) {
					// 1. Desmarca visualmente as caixas HTML (opções individuais e Selecionar Todos)
					config.$popover.find('input[type="checkbox"]').prop('checked', false).prop('indeterminate', false);

					// ---------------------------------------------------------------------------
					// 2. LINHAS ADICIONADAS: Limpa o input de busca interno e reexibe todas as opções
					// ---------------------------------------------------------------------------
					config.$popover.find('.input-busca-popover').val('');
					config.$popover.find('.lista-checkboxes .filter-option').show();
					// ---------------------------------------------------------------------------

					// 3. Zera a busca específica desta coluna
					config.mrp.column(config.indiceColuna).search('');
					
					tabelaParaDesenhar = config.mrp;
				}
			});

			// Reconstrói os popovers
			atualizarTodosOsPopovers();

			forcarResetVisual = false;

			// Se encontramos a tabela correspondente, aplica o redesenho
			if (tabelaParaDesenhar) {
				tabelaParaDesenhar.search(''); // Limpa a busca global
				tabelaParaDesenhar.draw(false); 
			} else {
				alert('Erro: Nenhuma tabela correspondente foi encontrada no array filtrosRegistrados.');
			}
		}


		$('#btnLimparFiltros').on('click', function() {
			limparMarcacoesPorTabela('mrp');
		});

		var mrpdt = $('#mrp').DataTable({
			
			processing: true,
			responsive: true,
			ajax: {
				'url': 'php/blinda.php',
				'type': 'POST',
				'data': function ( d ) {
					d.acao = 'mrp';
					d.tipo = $('#hdTipo').val();
				}
			},
			order: [[ 0, "asc" ]],
			scrollX: true,
			scrollY: '450px',
			paging: false,
			autoWidth: false,
			lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Todos']],
			language: { url: 'assets/vendors/DataTables/DataTables-1.10.16/js/Portuguese-Brasil.json', 'decimal': ',', 'thousands': '.'},
			columns: [
				{ data: "LINHA", class: 'text-center' },
				{ 
					data: 'SKU', class: 'text-center',
					render: function(data, type, row, meta) {

						 var rowString = JSON.stringify(row).replace(/"/g, '&quot;');

						return `<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="MRP(${rowString})">${row.SKU}</a>`
					}
				},
				{ data: "REFERENCIA" },
				{ data: "DESCRICAO" },
				{ data: "CODFAB", visible: false },
				{ data: "FABRICANTE" },
				{ data: "ESTOQUE", type: 'num', class: 'text-center' },
				{ data: "DEMANDA", type: 'num', class: 'text-center' },
				{ data: "SALDO", type: 'num', class: 'text-center' },
				{ data: "SALDO_RASCUNHO", type: 'num', class: 'text-center' },
				{ data: "COMPRAS", type: 'num', class: 'text-center' },
				{ data: "DISPONIVEL_RASCUNHO", type: 'num', class: 'text-center' },
				{ data: "TIPOSKU", class: 'text-center' },
				{ data: "PROCESSOS", visible: false  },
				/*{ data: "ESTOQUE_SP_01_01", visible: false },
				{ data: "ESTOQUE_SP_01_40_MAT_PRI", visible: false },
				{ data: "ESTOQUE_SP_01_41_EMB", visible: false },
				{ data: "ESTOQUE_SP_01_43_PROD_FINAL", visible: false },
				{ data: "ESTOQUE_SP_01_44_EVER", visible: false },
				{ data: "ESTOQUE_SP", visible: false },
				{ data: "ESTOQUE_ES", visible: false },
				{ data: "ESTOQUE_M", visible: false },
				{ data: "ESTOQUE_RJ_3", visible: false },
				{ data: "OC_SP", visible: false },
				{ data: "OC_M", visible: false },
				{ data: "OC_ES", visible: false },
				{ data: "OC_RJ_3", visible: false },
				{ data: "ORIGEM_DEMANDA", visible: false },*/
			],
			columnDefs: [
				//{ orderable: false, targets: [1,2,3,4,6,8,9,10,12,13,14,15,16,17,18]}
			],
			dom: 
			"<'row'<'col-sm-12 col-md-9'><\"col-sm-12 col-md-3\">>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3'>>",
			createdRow: function( row, data, dataIndex ) {
				
				UpdateMRP()
			},
			initComplete: function(settings, json) {
      
				// Agora sim, chamamos a função para ler a coluna oculta e montar o popover
				//const mrp2 = this.api(); 
				
				atualizarFiltros();
				/*registrarFiltro(mrpdt, 'fabricante', 5);
				registrarFiltro(mrpdt, 'processo', 11);
				atualizarTodosOsPopovers();*/
				/*atualizarFiltro(mrp, 'fabricante', 5);
				atualizarFiltro(mrp, 'processo', 11);*/
			
			}
		});

		mrpdt.on('click', 'tbody tr', (e) => {
			let classList = e.currentTarget.classList;
		
			if (classList.contains('selected')) {
				classList.remove('selected');
			}
			else {
				mrpdt.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
				classList.add('selected');
			}
		});

		//inicializarFiltrosMRP($.fn.dataTable.Api('#mrp'));

		/*$('#filtroFabricantes').on('change', function() {
			var valores = $(this).val(); // Retorna uma array com as opções selecionadas

			if (valores && valores.length > 0) {
				// Une os valores com o caractere pipe '|' (Ex: "Gerente|Analista")
				// Usamos a ancoragem ^ e $ para garantir que a busca encontre o termo exato na célula
				var queryRegex = '^(' + valores.join('|') + ')$';
				
				// Filtra a coluna 2 (Cargo) usando Expressão Regular (true) e desativando busca inteligente (false)
				mrp.column(5).search(queryRegex, true, false).draw();
			} else {
				// Se limpar a seleção, remove o filtro e redesenha a tabela limpa
				mrp.column(5).search('').draw();
			}
		});*/
	//});

	/*function atualizarFiltro(mrp, filtro, column)
	{
		const $popover = $(`#popover-filtro-${filtro}`);
		const $trigger = $(`#btn-filtro-${filtro}`);
		const fabricantes = new Set();

		// 1. Mesmo oculta, a API acessa os dados da coluna normalmente
		mrp.column(column).data().each(function(valoresCelula) {
			if(valoresCelula) {
			const partes = valoresCelula.split(',');
			partes.forEach(function(item) {
				fabricantes.add(item.trim());
			});
			}
		});

		// 2. Monta os checkboxes no popover externo
		Array.from(fabricantes).sort().forEach(function(fabricante) {
			$popover.append(`
			<label class="filter-option">
				<input type="checkbox" value="${fabricante}"> ${fabricante}
			</label>
			`);
		});

		// 3. Abrir/Fechar Popover
		$trigger.on('click', function(e) {
			e.stopPropagation();
			$popover.toggle();
		});

		// 4. Aplica o filtro na coluna oculta
		$popover.on('change', 'input[type="checkbox"]', function() {
			const selecionados = $popover.find('input:checked').map(function() {
			return $.fn.dataTable.util.escapeRegex($(this).val());
			}).get();

			if (selecionados.length > 0) {
				const buscaRegex = selecionados.join('|');
				const buscaRegexExata = '(^|,)\\s*(' + buscaRegex + ')\\s*(,|$)';
				// O filtro roda na coluna oculta e atualiza as linhas visíveis instantaneamente
				mrp.column(column).search(buscaRegexExata, true, false).draw();
				$trigger.text(`⏳ ${filtro.charAt(0).toUpperCase() + filtro.slice(1)}s (${selecionados.length})`);
			} else {
				mrp.column(column).search('').draw();
				$trigger.text('⏳ Escolher ' + filtro.charAt(0).toUpperCase() + filtro.slice(1));
			}
		});

		// 5. Fechar ao clicar fora
		$(document).on('click', function(e) {
			if (!$popover.is(e.target) && $popover.has(e.target).length === 0 && !$trigger.is(e.target)) {
			$popover.hide();
			}
		});
	}*/

	function UpdateMRP()
	{
		const date = new Date();
		const update = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
		document.getElementById('update').innerText = `Atualizado às ${update.format(date)}`
	}

	function Loading(modal = 'modalDashboardCS')
	{
		$(`#${modal} .modal-title`).html('Carregando...');
		$(`#${modal} .modal-body`).html('<div class="col-xs-1" align="center"><img id="loading" src="img/load.gif"> Processando...</div>');
	}

	function MRP(row)
	{
		Loading()
		$('#modalDashboardCS .modal-body').load('blinda/detalhes-mrp.php', row);
		$(`#modalDashboardCS .modal-title`).html('Compras MRP');
	}

	</script>
</body>
</html>