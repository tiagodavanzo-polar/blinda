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
    <title>Intranet Polar | Engenharia Blinda</title>
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

		.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
			color:#FFFFFF !important;
			background-color: #212529 !important;
		}

		#engenharia_wrapper th, #engenharia td
		{
			white-space: nowrap;
			padding-top: 0.1rem !important;
			padding-bottom: 0.1rem !important;
			font-size:12.5px;
		}

		#engenharia_wrapper th:nth-child(1), #engenharia td:nth-child(1),
		#engenharia_wrapper th:nth-child(2), #engenharia td:nth-child(2),
		#engenharia_wrapper th:nth-child(3), #engenharia td:nth-child(3)
		{
			width:50px !important;
		}

		#engenharia_wrapper th:nth-child(5), #engenharia td:nth-child(5),
		#engenharia_wrapper th:nth-child(6), #engenharia td:nth-child(6),
		#engenharia_wrapper th:nth-child(7), #engenharia td:nth-child(7)
		{
			width:60px !important;
		}

		/*#engenharia_wrapper th:nth-child(17), #engenharia td:nth-child(17)
		{
			width:60px !important;
		}*/

		#engenharia_wrapper th:nth-child(4), #engenharia td:nth-child(4)
		{
			width:250px !important;
			text-wrap:inherit;
		}

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
					<h4 class="text-danger text-center">Engenharia Blinda</h4>
					<span id="update" class="pl-4 small font-weight-bold">Carregando...</span><br />
					<div class="pl-4 float-left">
						<input type="hidden" id="hdPainel"value="carteira" />
						<input type="radio" id="painel" name="status" value="carteira" onclick="$('#hdPainel').val('carteira');" checked />
						<label for="painel" class="small">Carteira</label>
						<input type="radio" id="todos" name="status" value="finalizados" onclick="$('#hdPainel').val('finalizados');" />
						<label for="painel" class="small">Finalizados</label>
					</div>
					<button type="button" class="btn btn-sm btn-outline-dark float-right" style="margin-right: 15px" onclick="$('#engenharia').DataTable().ajax.reload()">
						<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
						<path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
						</svg>
						Atualizar
					</button>
					<p>&nbsp;</p>
					<table id="engenharia" class="table table-striped table-bordered table-hover" cellspacing="0">
						<thead>
							<tr>
								<th>LI</th>
								<th>PR</th>
								<th>NÚMERO</th>
								<th>SKU</th>
								<th>ITEM</th>
								<th>LIB APROV</th>
								<th>LIB COMP</th>
								<th>LIB PROD</th>
								<th>ENG</th>
								<th>PRE</th>
								<th>CKL</th>
								<th>DOC</th>
								<th>APR</th>
								<th>SKU</th>
								<th>BOM</th>
								<th>VEN</th>
								<th>CLI</th>
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
        <div class="modal-dialog" role="document" style="max-width:650px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Engenharia</h5>
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

		$('#engenharia').DataTable({
			
			processing: false,
			responsive: true,
			ajax: {
				'url': 'php/blinda.php',
				'type': 'POST',
				'data': function ( d ) {
					d.acao = 'engenharia';
					d.status = $('#hdPainel').val();
				}
			},
			order: [[ 0, "asc" ]],
			scrollX: true,
			scrollY: '500px',
			paging: false,
			autoWidth: false,
			lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Todos']],
			language: { url: 'assets/vendors/DataTables/DataTables-1.10.16/js/Portuguese-Brasil.json', 'decimal': ',', 'thousands': '.'},
			columns: [
				{ data: "LINHA", class: 'text-center' },
				{ data: "PR", class: 'text-center' },
				{ data: "HTMLNUMEROMOV", class: 'text-center' },
				{ data: "SKU" },
				{ data: "NUMITEMPEDIDO", class: 'text-center' },
				{ data: "DATAOTIFAPROV", class: 'text-center' },
				{ data: "DATAOTIFCOMPRAS", class: 'text-center' },
				{ data: "DATAOTIFPRODUCAO", class: 'text-center' },
				{ data: "ENGENHARIA", class: 'text-center' },
				{ data: "PRE_ENG", class: 'text-center' },
				{ data: "CKL_ENG", class: 'text-center' },
				{ data: "DOC_ENG", class: 'text-center' },
				{ data: "APROV_ENG", class: 'text-center' },
				{ data: "SKU_ENG", class: 'text-center' },
				{ data: "BOM_ENG", class: 'text-center' },
				{ data: "VEND_ENG", class: 'text-center' },
				{ data: "CLI_ENG", class: 'text-center' }
			],
			columnDefs: [
				{ orderable: false, targets: [4,8,9,10,11,12,13,14,15,16]}
			],
			dom: 
			"<'row'<'col-sm-12 col-md-9'><\"col-sm-12 col-md-3\">>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3'>>",
			initComplete: function (settings) {

				
			},
			/*drawCallback: function( settings, json ) {

				if($('#hdPainel').val() == 'todos')
					producao.column(18).visible(true);
				else
					producao.column(18).visible(false);
			},*/
			createdRow: function( row, data, dataIndex ) {
				
				statusColors(data.PR, 1, row)

				let index = 7
				
				statusColors(data.ENGENHARIA, index + 1, row)
				statusColors(data.PRE_ENG, index + 2, row)
				statusColors(data.CKL_ENG, index + 3, row)
				statusColors(data.DOC_ENG, index + 4, row)
				statusColors(data.APROV_ENG, index + 5, row)
				statusColors(data.SKU_ENG, index + 6, row)
				statusColors(data.BOM_ENG, index + 7, row)
				statusColors(data.VEND_ENG, index + 8, row)
				statusColors(data.CLI_ENG, index + 9, row)

				UpdateEng()
			}
		});

		function statusColors(status, index, row)
		{
			if (status == 'NI') {
				$(`td:eq(${index})`, row).css('background-color', '#696969');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'I') {
				$(`td:eq(${index})`, row).css('background-color', '#800080');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'P' || status == 'PE' || status == 'PF' || status == 'PM' || status == 'PP' || status == 'PU' || status == 'PD' || status == 'FP') {
				$(`td:eq(${index})`, row).css('background-color', '#ffa500');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'F' || status == 'FT') {
				$(`td:eq(${index})`, row).css('background-color', '#008000');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'ALTA' || status == 'A') {
				$(`td:eq(${index})`, row).css('background-color', '#e74c3c');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'MÉDIA' || status == 'M') {
				$(`td:eq(${index})`, row).css('background-color', '#FE9900');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'NORMAL' || status == 'N') {
				$(`td:eq(${index})`, row).css('background-color', '#FFDE59');
				$(`td:eq(${index})`, row).css('color', '#000000');
			}
			else if (status == 'BAIXA' || status == 'B') {
				$(`td:eq(${index})`, row).css('background-color', '#007bff');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
		}

		function UpdateEng()
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

		function Actions(numeromov, sku, referencia, numitempedido)
		{
			stopInterval()
			Loading()
			$('#modalDashboardCS .modal-body').load('blinda/engenharia.php', { 'numeromov': numeromov, 'sku': sku, 'numitempedido': numitempedido });
			$(`#modalDashboardCS .modal-title`).html(`Engenharia - Linha ${numeromov} | ${sku} | ${referencia} | Item ${numitempedido}`);
		}

		function stopInterval() {
			// 4. Call clearInterval() with the stored ID
			//alert('parou')
			clearInterval(intervalId);
			//console.log('Interval stopped.');
		}

		/*function ProducaoBlinda(acao, numeromov, numitempedido, sku){
	
			if 
			(
				(acao == 'iniciarPlanejamento' && confirm("Tem certeza que deseja iniciar a etapa de plajamento?")) ||
				(acao == 'finalizarPlanejamento' && confirm("Tem certeza que deseja finalizar a etapa de plajamento?")) ||
				(acao == 'reabrirPlanejamento' && confirm("Tem certeza que deseja reabrir a etapa de plajamento?"))
			) {

				$.post(`php/blinda.php`, { 'acao': acao, 'numeromov': numeromov, 'numitempedido': numitempedido, 'sku': sku },
					function(data){
						data = jQuery.parseJSON(data);
						alert(data.resposta)
					}
				);
			}
		}*/

		intervalId = setInterval(`$('#engenharia').DataTable().ajax.reload(null, false);`, 15000)

		$('#modalDashboardCS').on('hidden.bs.modal', function (e) {
			//alert('fechou')
			$('#engenharia').DataTable().ajax.reload()
			intervalId = setInterval(`$('#engenharia').DataTable().ajax.reload(null, false);`, 15000)
		});

	</script>
</body>

</html>