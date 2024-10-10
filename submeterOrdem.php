<?php
session_start();

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Index</title>
	<script src="./js/fun_submeterOrdem.js" defer></script>
	<?php
	include __DIR__ . '/css/relSrc.php';
	?>
</head>
<body>
	<?php
	include __DIR__ . '/include/include_header.php';
	?>
	<div class="header">
        <a href="./index.php"><img src="./img/01LOG.webp" alt="Index"></a>
		<a href="./menuOrdens.php"><img src="./img/seta_esquerda.webp" alt="Menu"></a>
        <h2>Submeter Ordem</h2>
	</div>

    <div class="containeSubmeter">
		<form action="db/adicionarSGO/db_submeterOrdem.php" method="POST">
			<input type="hidden" name="redirectUrl" value="submeterOrdem.php" readonly>

			<h3>Ordem de Serviço</h3>

			<label for="numOrdem">Número da Ordem:</label>
			<input type="text" id="numOrdem" name="numOrdem" placeholder="Necessário" required><br>

			<label for="tipoPedido">Tipo de Pedido:</label>
			<select id="tipoPedido" name="tipoPedido" required>
				<option value="Interno">Interno</option>
				<option value="Externo">Externo</option>
				<option value="Outros">Outros</option>
			</select><br>

			<label for="IdRequerente">Requerente:</label>
			<div class="dropdown">
				<input type="text" id="inputDropdown_IdRequerente" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdRequerente', 'listDropdown_IdRequerente', 'IdRequerente')" required>
				<input type="hidden" id="IdRequerente" name="IdRequerente">
				<div id="listDropdown_IdRequerente" class="listDropdown"></div>
			</div><br>

			<label for="numResp">Responsável:</label>
			<div class="dropdown">
				<input type="text" id="inputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numResp', 'listDropdown_numResp', 'numResp')" required>
				<input type="hidden" id="numResp" name="numResp">
				<div id="listDropdown_numResp" class="listDropdown"></div>
			</div><br>

			<label for="dataRegisto">Data de Registo:</label>
			<input type="date" id="dataRegisto" name="dataRegisto"><br>

			<label for="Descritivo">Descritivo:</label>
			<textarea id="Descritivo" name="Descritivo" rows="6" placeholder="Escreva aqui..."></textarea><br>

			<label for="LocalDestino">Local de Destino:</label>
			<input type="text" id="LocalDestino" name="LocalDestino"><br>
			
			<h3>Pedido</h3>

			<label for="numOrdem">Ordenante:</label>
			<input type="text" id="numOrdem" name="numOrdem"><br>

			<label for="IdServico">Serviço:</label>
			<div class="dropdown">
				<input type="text" id="inputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdServico', 'listDropdown_IdServico', 'IdServico')" required>
				<input type="hidden" id="IdServico" name="IdServico">
				<div id="listDropdown_IdServico" class="listDropdown"></div>
			</div><br>

			<label for="Descricao">Descrição do Pedido:</label>
			<textarea id="Descricao" name="Descricao" rows="6" placeholder="Escreva aqui..."></textarea><br>

			<label for="Observacoes">Observações:</label>
			<textarea id="Observacoes" name="Observacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

			<input type="submit" value="Adicionar Ordem e Pedido">
		</form>
    </div>
	
	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>

