<?php
include __DIR__ . '/db/areaSGO/db_areaDespacho.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área de Despacho</title>
    <script src="./js/fun_areaDespacho.js" defer></script>
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
		<a href="./gestaoDespachos.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área de Despacho</h2>
    </div>

    <div class="containerGestao">
		<div class="caixaSaudacao">
			<p>Id: <?php echo $despacho['IdDespacho']; ?></p>
			<p>Tipo de Decisão: <?php echo $despacho['tipoDecisao']; ?></p>
			<p>Descrição: 
				<?php if (!empty($despacho['Descricao']) && strlen($despacho['Descricao']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Descrição</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $despacho['Descricao']; ?></textarea>
				<?php else: ?>
					<?php echo $despacho['Descricao']; ?>
				<?php endif; ?>
			</p>
			<p>Operador: 
				<?php if (!empty($despacho['numOper']) && !empty($despacho['NomeOper'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $despacho["IdOperador"]; ?>'>
					<?php echo $despacho['numOper'] . ") " . $despacho['NomeOper']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Pedido: <a href='areaPedido.php?IdPedido=<?php echo $despacho["IdPedido"]; ?>'>
					<?php echo $despacho['IdPedido']; ?></a></p>
			<p>Ordem de Serviço:
				<?php if (!empty($despacho['IdOrdem']) && !empty($despacho['numOrdem'])): ?>
					<a href='areaOrdemServico.php?IdOrdem=<?php echo $despacho["IdOrdem"]; ?>'>
					<?php echo $despacho['IdOrdem'] . ") " . $despacho['numOrdem']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>

			<button class='btnEditar' data-dados='<?php echo json_encode($despacho); ?>'>Editar</button>
			<button class='btnEliminar'idDespacho="<?php echo $despacho['IdDespacho']; ?>" onclick="funEliminarDespacho(this)">Eliminar</button>
			
		</div>
	</div>
		
	<div id="modalEditar" class="modal">
		<div class="modalConteudo">
			<span class="close" onclick="closeModalEditar()">&times;</span>
			<h2>Editar Despacho</h2>
			<form id="formEditarDespacho" action="db/editarSGO/db_editarDespachos.php" method="POST">
			<input type="hidden" id="redirectUrl" name="redirectUrl" value="areaDespacho.php" readonly>
			<input type="hidden" id="editIdDespacho" name="editIdDespacho" readonly>
				
				<label for="editTipoDecisao">Tipo de Decisão:</label>
				<select id="editTipoDecisao" name="editTipoDecisao">
					<option value="Informação">Informação</option>
					<option value="Despacho">Despacho</option>
					<option value="Outros">Outros</option>
				</select><br>

				<label for="editDescricao">Descrição:</label>
				<textarea id="editDescricao" name="editDescricao" rows="6" placeholder="Escreva aqui..."></textarea><br>

				<label for="editNumOper">Número de Operador:</label>
				<div class="dropdown">
					<input type="text" id="editInputDropdown_numOper" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numOper', 'editListDropdown_numOper', 'editNumOper')" required>
					<input type="hidden" id="editNumOper" name="editNumOper" readonly>
					<div id="editListDropdown_numOper" class="listDropdown"></div>
				</div><br>
				
				<label for="editIdPedido">Pedido:</label>
				<div class="dropdown">
					<input type="text" id="editInputDropdown_IdPedido" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdPedido', 'editListDropdown_IdPedido', 'editIdPedido')" required>
					<input type="hidden" id="editIdPedido" name="editIdPedido" readonly>
					<div id="editListDropdown_IdPedido" class="listDropdown"></div>
				</div><br>
				
				<label for="editIdOrdem">Ordem de Serviço:</label>
				<div class="dropdown">
					<input type="text" id="editInputDropdown_IdOrdem" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdOrdem', 'editListDropdown_IdOrdem', 'editIdOrdem')" required>
					<input type="hidden" id="editIdOrdem" name="editIdOrdem" readonly>
					<div id="editListDropdown_IdOrdem" class="listDropdown"></div>
				</div><br>
				
				<input type="submit" value="Atualizar Despacho">
			</form>
		</div>
	</div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
