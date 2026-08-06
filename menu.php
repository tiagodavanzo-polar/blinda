<?php
	
	$pagina = basename($_SERVER['PHP_SELF']);

	$liAtivo = [1 => '', 2 => '', 3 => '', 4 => ''];
	$menu = [
		'index.php' => '',
		'producao.php' => '',
		'engenharia.php' => '',
		'mrp.php' => '',
		'mps.php' => '',
	];

	$string = explode('.', $_SESSION[Config::$uniqid]['USUARIO']);
    $nome = ucfirst($string[0]);

    $menu[$pagina] = 'class="active"';
 ?>
<!-- START SIDEBAR-->
<nav class="page-sidebar" id="sidebar">
	<div id="sidebar-collapse">
		<div class="admin-block d-flex">
			<div>
				<img src="./assets/img/admin-avatar.png" width="45px" />
			</div>
			<div class="admin-info">
				<div class="font-strong">Olá, <?php echo $nome; ?></div><!--<small>Administrator</small>--></div>
		</div>
		<ul class="side-menu metismenu">
			<li <?= $menu['index.php']; ?>>
				<a href="index.php" <?= $menu['index.php']; ?>><i class="sidebar-item-icon fa fa-home"></i>
					<span class="nav-label">Home</span>
				</a>
			</li>
			<li <?= $menu['producao.php'].$menu['engenharia.php'].$menu['mrp.php']; ?>>
				<a href="javascript:;"><i class="sidebar-item-icon fa fa-th-large"></i>
					<span class="nav-label">Blinda</span><i class="fa fa-angle-left arrow"></i>
				</a>
				<ul class="nav-2-level collapse">
					<li><a href="producao.php" <?= $menu['producao.php']; ?>>Produção</a></li>
					<li><a href="engenharia.php" <?= $menu['engenharia.php']; ?>>Engenharia</a></li>
					<li><a href="mrp.php" <?= $menu['mrp.php']; ?>>MRP</a></li>
					<li><a href="mps.php" <?= $menu['mps.php']; ?>>MPS</a></li>
				</ul>
			</li>			
			<li>
				<a href="logout.php"><i class="sidebar-item-icon fa fa-sign-out"></i>
					<span class="nav-label">Logout</span>
				</a>
			</li>
		</ul>
	</div>
</nav>
