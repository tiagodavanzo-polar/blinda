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
    <title>Intranet Polar | Compras Blinda</title>
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

		#compras_wrapper th, #compras td, #lblSKU, #txtSKU
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

		/*.table#compras_wrapper th:nth-child(2), #compras td:nth-child(2),
		#compras_wrapper th:nth-child(4), #compras td:nth-child(4)
		{
			width:200px !important;
			text-wrap:inherit;
		}*/

		 #compras td:nth-child(3)
		{
			width:auto !important;
		}

		#compras td:nth-child(4)
		{
			width:800px !important;
			text-wrap:inherit;
		}

		/*#compras_wrapper th:nth-child(1), #compras td:nth-child(1),
		#compras_wrapper th:nth-child(2), #compras td:nth-child(2),
		#compras_wrapper th:nth-child(3), #compras td:nth-child(3),
		#compras_wrapper th:nth-child(4), #compras td:nth-child(4)
		{
			width:50px !important;
		}

		#compras_wrapper th:nth-child(5), #compras td:nth-child(5),
		#compras_wrapper th:nth-child(6), #compras td:nth-child(6)
		{
			width:60px !important;
		}

		#compras_wrapper th:nth-child(7), #compras td:nth-child(7)
		{
			width:250px !important;
			text-wrap:inherit;
		}*/

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
				<div class="col-xl-12">
					<h4 class="text-danger text-center">Compras Blinda</h4>
					<span id="update" class="pl-4 small font-weight-bold">Carregando...</span><br />
					<div class="pl-4 float-left">
						<input type="hidden" id="hdPainel"value="1" />
						<input type="radio" id="painel" name="status" value="blinda" onclick="$('#hdPainel').val(1); $('#compras').DataTable().ajax.reload()" checked />
						<label for="painel" class="small">Blinda</label>
						<input type="radio" id="todos" name="status" value="todos" onclick="$('#hdPainel').val(0); $('#compras').DataTable().ajax.reload()" />
						<label for="todos" class="small">Todos</label>
					</div>
					<button type="button" class="btn btn-sm btn-outline-dark float-right" style="margin-right: 15px" onclick="$('#compras').DataTable().ajax.reload()">
						<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
						<path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
						</svg>
						Atualizar
					</button>
					<p>&nbsp;</p>
					<table id="compras" class="table table-striped table-bordered table-hover" cellspacing="0">
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
								<th>COMPRAS</th>
								<th>DISPONÍVEL</th>
							</tr>
						</thead>
					</table>
					<!--<table id="compras" class="table table-striped table-bordered table-hover" cellspacing="0">
						<thead>
							<tr>
								<th>LINHA</th>
								<th>CODTMV</th>
								<th>BU</th>
								<th>OP</th>
								<th>POCLIENTE</th>
								<th>PROCESSO</th>
								<th>CLIENTE</th>
								<th>ITEM PO</th>
								<th>SKU</th>
								<th>PRODUTO</th>
								<th>FABRICANTE</th>
								<th>GRPROD</th>
								<th>UN</th>
								<th>QTDE</th>
								<th>DTPEDIDO</th>
								<th>DTENTREGA</th>
								<th>DTREPROG</th>
								<th>DTENTREGAITEM</th>
								<th>DTREPROGITEM</th>
								<th>PRAZO</th>
							</tr>
						</thead>
					</table>-->
				</div>
			</div>
            <footer class="page-footer">
                <div class="to-top"><i class="fa fa-angle-double-up"></i></div>
            </footer>
        </div>
    </div>

	<div id="modalDashboardCS" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:1200px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compras</h5>
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

	<script type="text/javascript">

		let intervalId;

		var compras = $('#compras').DataTable({
			
			processing: true,
			responsive: true,
			ajax: {
				'url': 'php/blinda.php',
				'type': 'POST',
				'data': function ( d ) {
					d.acao = 'compras';
					d.status = $('#hdPainel').val();
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
				//{ data: "SKU", class: 'text-center' },
				{ 
					data: 'SKU', class: 'text-center',
					render: function(data, type, row, meta) {
						// 'data' é o conteúdo da coluna atual
						// 'row' permite acessar dados de outras colunas se precisar
						//return '<a href="' + data + '" target="_blank">' + data + '</a>';
						 var rowString = JSON.stringify(row).replace(/"/g, '&quot;');

						return `<a href="javascript: void(0)" data-toggle="modal" data-target="#modalDashboardCS" onclick="MRP(${rowString})">${row.SKU}</a>`
					}
				},
				{ data: "REFERENCIA" },
				{ data: "DESCITM" },
				{ data: "CODFAB", visible: false },
				{ data: "FABRICANTE" },
				{ data: "ESTOQUE", type: 'num', class: 'text-center' },
				{ data: "DEMANDA", type: 'num', class: 'text-center' },
				{ data: "SALDO", type: 'num', class: 'text-center' },
				{ data: "COMPRAS", type: 'num', class: 'text-center' },
				{ data: "DISPONIVEL", type: 'num', class: 'text-center' },
				{ data: "ESTOQUE_SP_01_01", visible: false },
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
				{ data: "ORIGEM_DEMANDA", visible: false },
				//{ data: "GRUPOPRODUTO", class: 'text-center' },
				//{ data: "UN", class: 'text-center' },
				//{ data: "DEMANDA", class: 'text-center' }
				/*
				{ data: "LINHA", class: 'text-center' },
				{ data: "CODTMV", class: 'text-center' },
				{ data: "CCUSTO", class: 'text-center' },
				{ data: "OPERACAO", class: 'text-center' },
				{ data: "POCLIENTE" },
				{ data: "PROCESSO", class: 'text-center' },
				{ data: "CLIENTE" },
				{ data: "NUMITEMPEDIDO", class: 'text-center' },
				{ data: "SKU", class: 'text-center' },
				{ data: "PRODUTO" },
				{ data: "FABRICANTE", class: 'text-center' },
				{ data: "GRUPOPRODUTO", class: 'text-center' },
				{ data: "UN", class: 'text-center' },
				{ data: "QTDE", class: 'text-center' },
				{ data: "DTPEDIDO", class: 'text-center' },
				{ data: "DTENTREGA", class: 'text-center' },
				{ data: "DTREPROG", class: 'text-center' },
				{ data: "DTENTREGAITEM", class: 'text-center' },
				{ data: "DTREPROGITEM", class: 'text-center' },
				{ data: "PRAZO", class: 'text-center' }
				*/
			],
			columnDefs: [
				//{ orderable: false, targets: [1,2,3,4,6,8,9,10,12,13,14,15,16,17,18]}
			],
			dom: 
			"<'row'<'col-sm-12 col-md-9'><\"col-sm-12 col-md-3\">>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3'>>",
			createdRow: function( row, data, dataIndex ) {
				
				UpdateCompras()
			}
		});

		compras.on('click', 'tbody tr', (e) => {
			let classList = e.currentTarget.classList;
		
			if (classList.contains('selected')) {
				classList.remove('selected');
			}
			else {
				compras.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
				classList.add('selected');
			}
		});

		function UpdateCompras()
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
			$('#modalDashboardCS .modal-body').load('blinda/mrp.php', row);
			$(`#modalDashboardCS .modal-title`).html('Compras');
		}

	</script>
</body>

</html>