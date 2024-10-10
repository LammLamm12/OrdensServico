<?php
session_start();

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

	<div class="container-fluid pt-4 px-4">
		<div class="row vh-100 bg-light rounded align-items-center justify-content-center mx-0">
			<div class="col-md-6 text-center p-4">
				<i class="bi bi-exclamation-triangle display-1 text-primary"></i>
				<h1 class="display-1 fw-bold">404</h1>
				<h1 class="mb-4">Page Not Found</h1>
				<p class="mb-4">Desculpa, a página que encontraste-te não existe. Queres voltar ao dasboard?</p>
				<a class="btn btn-primary rounded-pill py-3 px-5" href="index.php">Regressar</a>
			</div>
		</div>
	</div>

</body>
</html>

