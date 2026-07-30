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
    <title>Intranet Polar | Produção Blinda</title>
	<link rel="icon" type="image/x-icon" href="img/favicon.png">
    <!-- GLOBAL MAINLY STYLES-->
    <link href="./assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="./assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- PLUGINS STYLES-->
    <link href="./assets/vendors/DataTables/datatables.min.css" rel="stylesheet" />
    <link href="./css/open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
    <!-- THEME STYLES-->
    <link href="./assets/css/main.min.css" rel="stylesheet" />
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

		#producao_wrapper th, #producao td
		{
			white-space: nowrap;
			padding-top: 0.1rem !important;
			padding-bottom: 0.1rem !important;
			font-size:12.5px;
		}

		#producao_wrapper th:nth-child(1), #producao td:nth-child(1),
		#producao_wrapper th:nth-child(2), #producao td:nth-child(2),
		#producao_wrapper th:nth-child(3), #producao td:nth-child(3),
		#producao_wrapper th:nth-child(4), #producao td:nth-child(4)
		{
			width:50px !important;
		}

		#producao_wrapper th:nth-child(5), #producao td:nth-child(5),
		#producao_wrapper th:nth-child(6), #producao td:nth-child(6),
		#producao_wrapper th:nth-child(7), #producao td:nth-child(7),
		#producao_wrapper th:nth-child(9), #producao td:nth-child(9)
		{
			width:60px !important;
		}

		/*#producao_wrapper th:nth-child(10), #producao td:nth-child(10),
		#producao_wrapper th:nth-child(11), #producao td:nth-child(11),
		#producao_wrapper th:nth-child(12), #producao td:nth-child(12),
		#producao_wrapper th:nth-child(13), #producao td:nth-child(13),
		#producao_wrapper th:nth-child(14), #producao td:nth-child(14),
		#producao_wrapper th:nth-child(15), #producao td:nth-child(15),
		#producao_wrapper th:nth-child(16), #producao td:nth-child(16),
		#producao_wrapper th:nth-child(17), #producao td:nth-child(17),
		#producao_wrapper th:nth-child(18), #producao td:nth-child(18),
		#producao_wrapper th:nth-child(19), #producao td:nth-child(19)
		{
			width:60px !important;
		}*/

		#producao_wrapper th:nth-child(17), #producao td:nth-child(17)
		{
			width:60px !important;
		}

		#producao_wrapper th:nth-child(8), #producao td:nth-child(8)
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
            //require_once 'header.php';

            require_once 'menu.php';
        ?>
        
        <div class="content-wrapper" style="min-height:1100px !important;">
			<div class="row">
				<div class="col-xl-12">
					<h4 class="text-danger text-center">Produção Blinda</h4>
					<span id="atualizacao" class="pl-4 small font-weight-bold">Carregando...</span><br />
					<div class="pl-4 float-left">
						<input type="hidden" id="hdPainel"value="painel" />
						<input type="radio" id="painel" name="status" value="painel" onclick="$('#hdPainel').val('painel');" checked />
						<label for="painel" class="small">Produção</label>
						<input type="radio" id="todos" name="status" value="todos" onclick="$('#hdPainel').val('todos');" />
						<label for="painel" class="small">Todos</label>
					</div>
					<button type="button" class="btn btn-sm btn-outline-dark float-right" style="margin-right: 15px" onclick="$('#producao').DataTable().ajax.reload()">
						<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
						<path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
						</svg>
						Atualizar
					</button>
					<p>&nbsp;</p>
					<table id="producao" class="table table-striped table-bordered table-hover" cellspacing="0">
						<thead>
							<tr>
								<th>LI</th>
								<th>PR</th>
								<th>MOV</th>
								<th>BU</th>
								<th>PRO|OP</th>
								<th>SKU</th>
								<th>IT</th>
								<th>REFERENCIA</th>
								<th>QTDE</th>
								<th>PLAN</th>
								<th>SEP</th>
								<th>MONT</th>
								<th>INSP</th>
								<th>EMB</th>
								<th>OP</th>
								<th>SD</th>
								<th>RTS</th>
								<!--<th>INDUSTR</th>-->
								<th>TRANSP</th>
								<th>AÇÃO PLAN</th>
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
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS-->
    <script src="./assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
	<script src="./assets/vendors/DataTables/datatables.min.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="./assets/js/app.min.js" type="text/javascript"></script>
	<script src="./assets/vendors/DataTables/moment.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/DataTables/datetime-moment.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->

	<script type="text/javascript">

		producao = $('#producao').DataTable({
			
			processing: false,
			responsive: true,
			ajax: {
				'url': 'php/blinda.php',
				'type': 'POST',
				'data': function ( d ) {
					d.acao = 'producao';
					d.status = $('#hdPainel').val(); //document.getElementById('status').value
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
				{ data: "MOV", class: 'text-center' },
				{ data: "CCUSTO", class: 'text-center' },
				{ data: "NUMEROMOV", class: 'text-center' },
				{ data: "SKU", class: 'text-center' },
				{ data: "NUMITEMPEDIDO", class: 'text-center' },
				{ data: "REFERENCIA" },
				{ data: "QTDE", class: 'text-center' },
				{ data: "PLANEJAMENTO", class: 'text-center' },
				{ data: "SEPARACAO", class: 'text-center' },
				{ data: "MONTAGEM", class: 'text-center' },
				{ data: "INSPECAO", class: 'text-center' },
				{ data: "EMBALAGEM", class: 'text-center' },
				{ data: "OP", class: 'text-center' },
				{ data: "SD", class: 'text-center' },
				{ data: "RTS", class: 'text-center' },
				//{ data: "INDUSTRIALIZACAO", class: 'text-center' },
				{ data: "TRANSPORTE", class: 'text-center' },
				{ data: "ACAO", class: 'text-center' }
			],
			columnDefs: [
				//{ className: "text-center", targets: [0,1,7,8] }
				{ orderable: false, targets: [9,10,11,12,13,14,15,16,17]}
				//{ orderable: false, targets: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16]}
			],
			//fixedColumns: true,
			dom: 
			"<'row'<'col-sm-12 col-md-9'><\"col-sm-12 col-md-3\">>" +
			"<'row'<'col-sm-12'tr>>" +
			//"<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3'B>>",
			"<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3'>>",
			initComplete: function (settings) {

				
			},
			drawCallback: function( settings, json ) {

				if($('#hdPainel').val() == 'todos')
					producao.column(18).visible(true);
				else
					producao.column(18).visible(false);
			},
			createdRow: function( row, data, dataIndex ) {
				
				coresProducao(data.PR, 1, row)

				let index = 8
				
				coresProducao(data.PLANEJAMENTO, index + 1, row)
				coresProducao(data.SEPARACAO, index + 2, row)
				coresProducao(data.MONTAGEM, index + 3, row)
				coresProducao(data.INSPECAO, index + 4, row)
				coresProducao(data.EMBALAGEM, index + 5, row)
				coresProducao(data.OP, index + 6, row)
				coresProducao(data.SD, index + 7, row)
				coresProducao(data.RTS, index + 8, row)
				//coresProducao(data.INDUSTRIALIZACAO, index + 9, row)
				coresProducao(data.TRANSPORTE, index + 9, row)

				Atualizacao()
				
				/*
				if (data.PLANEJAMENTO == 'NI') {
					$('td:eq(10)', row).css('background-color', '#696969');
					$('td:eq(10)', row).css('color', '#FFFFFF');
				}
				else if (data.PLANEJAMENTO == 'I') {
					//$(row).addClass( 'problema' );
				}
				else if (data.PLANEJAMENTO == 'P') {
					//$(row).addClass( 'atraso' );
				}
				else if (data.PLANEJAMENTO == 'F') {
					//$(row).addClass( 'desvio' );
				}
					*/
			}
		});

		function coresProducao(status, index, row)
		{
			if (status == 'NI') {
				$(`td:eq(${index})`, row).css('background-color', '#696969');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'I') {
				$(`td:eq(${index})`, row).css('background-color', '#800080');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'P' || status == 'PE' || status == 'PF' || status == 'PM' || status == 'PP' || status == 'PU') {
				$(`td:eq(${index})`, row).css('background-color', '#ffa500');
				$(`td:eq(${index})`, row).css('color', '#FFFFFF');
			}
			else if (status == 'F') {
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

		function Atualizacao()
		{
			const date = new Date();
			const atualizacao = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
			document.getElementById('atualizacao').innerText = `Atualizado às ${atualizacao.format(date)}`
		}

		function ProducaoBlinda(acao, numeromov, numitempedido, sku){
	
			if 
			(
				(acao == 'iniciarPlanejamento' && confirm("Tem certeza que deseja iniciar a etapa de plajamento?")) ||
				(acao == 'finalizarPlanejamento' && confirm("Tem certeza que deseja finalizar a etapa de plajamento?")) ||
				(acao == 'reabrirPlanejamento' && confirm("Tem certeza que deseja reabrir a etapa de plajamento?"))
			) {

				//Loading()
				$.post(`php/blinda.php`, { 'acao': acao, 'numeromov': numeromov, 'numitempedido': numitempedido, 'sku': sku },
					function(data){
						data = jQuery.parseJSON(data);
						alert(data.resposta)
					}
				);
			}
		}

		setInterval(`$('#producao').DataTable().ajax.reload(null, false);`, 30000)

	</script>
</body>

</html>