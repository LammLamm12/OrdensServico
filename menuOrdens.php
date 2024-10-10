<?php
session_start();

if (!isset($_SESSION['IdOperador'])) {
    header('Location: /OrdensServico/index.php?error=' . urlencode('Não tem permissões para aceder a esta página.'));
    exit();
}

include __DIR__ . '/db/db_connect.php';

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <title>MenuOrdens</title>
	<script src="./js/fun_menuOrdens.js" defer></script>
	<?php
	include __DIR__ . '/css/relSrc.php';
	?>
</head>
<body>
	<?php
	include __DIR__ . '/include/include_header.php';
	?>
	<div class="containerMenu">
	<img src="./img/fudok1.webp" alt="Logótipo SGO">
		<div class="linksMenu">
			<a href="./gestaoDespachos.php">Gestão de Despachos</a>
			<a href="./gestaoOperadores.php">Gestão de Operadores</a>
			<a href="./gestaoOrdemServicos.php">Gestão de Ordem de Serviços</a>
			<a href="./gestaoPedidos.php">Gestão de Pedidos</a>
			<a href="./gestaoRepresentantes.php">Gestão de Representantes</a>
			<a href="./gestaoRequerentes.php">Gestão de Requerentes</a>
			<a href="./gestaoServicos.php">Gestão de Serviços</a>
			<a href="./gestaoViaturas.php">Gestão de Viaturas</a>
			<a href="./submeterOrdem.php">Submeter Ordem</a>
		</div>
	</div>


	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>

