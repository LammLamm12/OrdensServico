<?php
include __DIR__ . '/db/areaSGO/db_areaServico.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área de Serviço</title>
    <script src="./js/fun_areaServico.js" defer></script>
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
		<a href="./gestaoServicos.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área de Serviço</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tableOperadores')">Operadores Associados</button>
			</div>

			
            <div id="tableOperadores" class="containerTable">
                <a href='gestaoOperadores.php'><h2>Operadores Associados</h2></a>
                <table>
                    <thead>
                        <tr>
							<th>Número</th>
							<th>Nome</th>
							<th>Tipo de Operador</th>  
							<th>Função</th>
							<th>Categoria</th>
							<th>E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php if ($result_operadores->num_rows > 0): ?>
							<?php while ($row = $result_operadores->fetch_assoc()): ?>
								<tr dadosPagina="<?= $pagina ?>">
									<td><a href='areaOperador.php?IdOperador=<?= $row['IdOperador'] ?>'><?= $row['numOper'] ?></a></td>
									<td><?= $row['NomeOper'] ?></td>
									<td><?= $row['tipoOper'] ?></td>
									<td><?= $row['Funcao'] ?></td>
									<td><?= $row['Categoria'] ?></td>
									<td><?= $row['Email'] ?></td>
								</tr>
							<?php endwhile; ?>
						<?php else: ?>
							<tr>
								<td colspan='10' style="text-align: center;">Nenhum registo encontrado.</td>
							</tr>
						<?php endif; ?>
                    </tbody>
					<tfoot>
						<tr>
							<td colspan="6">
								<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_operadores; ?></p>
								<div class="containerPaginacao">
									<div id="paginacao">
										<?php
										$primeira_pagina_operadores = max(1, $pagina_operadores - 15); 
										$ultima_pagina_operadores = min($total_paginas_operadores, $pagina_operadores + 15);

										if ($pagina_operadores > 1) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_operadores=1&tableId=tablePedidos'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_operadores=" . ($pagina_operadores - 1) . "&tableId=tablePedidos'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_operadores; $i <= $ultima_pagina_operadores; $i++) {
											if ($i == $pagina_operadores) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_operadores=$i&tableId=tablePedidos'\">$i</button>";
											}
										}

										if ($pagina_operadores < $total_paginas_operadores) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_operadores=" . ($pagina_operadores + 1) . "&tableId=tablePedidos'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_operadores=$total_paginas_operadores&tableId=tablePedidos'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaPedidosInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_operadores; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaPedidosInput', <?php echo $total_paginas_operadores; ?>, 'areaViatura.php', <?= $idViatura ?>, 'pagina_operadores', 'tablePedidos'); }">

									<script>
										var totalPaginas = <?php echo $total_paginas_operadores; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
	
		<div class="caixaSaudacao">
			<p>Sigla do Serviço: <?php echo $servico['ServSigla']; ?></p>
			<p>Nome do Serviço: <?php echo $servico['NomeServ']; ?></p>
			<p>Responsável: 
				<?php if (!empty($servico['numOper']) && !empty($servico['NomeOper'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $servico["IdOperador"]; ?>'>
					<?php echo $servico['numOper']; ?>) <?php echo $servico['NomeOper']; ?></a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Decisor: <?php echo $servico['titDecisor']; ?></p>
			<p>Nome do Decisor: <?php echo $servico['NomeDecisor']; ?></p>
			<p>Categoria: <?php echo $servico['Categoria']; ?></p>
			
			
			<button class='btnEditar' data-dados='<?php echo json_encode($pedido); ?>'>Editar</button>
			<button class='btnEliminar'idpedido="<?php echo $pedido['IdPedido']; ?>" onclick="funEliminarPedido(this)">Eliminar</button>s
		</div>
		

		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Serviço</h2>
				<form id="formEditar" action="db/editarSGO/db_editarServicos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoServicos.php" readonly>
					<input type="hidden" id="editIdServico" name="editIdServico" readonly>
					
					<label for="editServSigla">Sigla:</label>
					<input type="text" id="editServSigla" name="editServSigla"><br>

					<label for="editNomeServ">Marca:</label>
					<input type="text" id="editNomeServ" name="editNomeServ"><br>

					<label for="editNumResp">Responsável:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numResp', 'editListDropdown_numResp', 'editNumResp')" required>
						<input type="hidden" id="editNumResp" name="editNumResp">
						<div id="editListDropdown_numResp" class="listDropdown"></div>
					</div><br>

					<label for="editNomeDecisor">Decisor:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_NomeDecisor" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_NomeDecisor', 'editListDropdown_NomeDecisor', 'editNomeDecisor')" required>
						<input type="hidden" id="editNomeDecisor" name="editNomeDecisor">
						<div id="editListDropdown_NomeDecisor" class="listDropdown"></div>
					</div><br>
					
					<label for="editTitDecisor">Título do Decisor:</label>
					<input type="text" id="editTitDecisor" name="editTitDecisor"><br>
					
					<label for="editCategoria">Categoria:</label>
					<input type="text" id="editCategoria" name="editCategoria"><br>

					<input type="submit" value="Atualizar Serviço">
				</form>
			</div>
		</div>
    </div>
	
	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
