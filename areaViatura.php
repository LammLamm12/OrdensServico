<?php
include __DIR__ . '/db/areaSGO/db_areaViatura.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área da Viatura</title>
    <script src="./js/fun_areaViatura.js" defer></script>
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
		<a href="./gestaoViaturas.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área da Viatura</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tablePedidos')">Pedidos Associados</button>
			</div>

			
            <div id="tablePedidos" class="containerTable">
                <a href='gestaoPedidos.php'><h2>Pedidos Associados</h2></a>
                <table>
                    <thead>
                        <tr>
                            <th>Id do Pedido</th>
                            <th>Tipo de Pedido</th>
                            <th>Estado do Pedido</th>
                            <th>Descrição</th>
                            <th>Data de Registo</th>
                            <th>Despacho</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php if ($result_pedidos->num_rows > 0): ?>
							<?php while ($row = $result_pedidos->fetch_assoc()): ?>
								<tr>
									<td><?= $row['IdPedido'] ?></td>
									<td><?= $row['tipoPedido'] ?></td>
									<td><?= $row['estadoPedido'] ?></td>
									<td><?= $row['Descricao'] ?></td>
									<td><?= $row['dataRegisto'] ?></td>
									<td><?= $row['Despacho'] ?></td>
								</tr>
							<?php endwhile; ?>
						<?php else: ?>
							<tr>
								<td colspan="6" style="text-align: center;">Nenhum registo encontrado.</td>
							</tr>
						<?php endif; ?>
                    </tbody>
					<tfoot>
						<tr>
							<td colspan="6">
								<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_pedidos; ?></p>
								<div class="containerPaginacao">
									<div id="paginacao">
										<?php
										$primeira_pagina_pedidos = max(1, $pagina_pedidos - 15); 
										$ultima_pagina_pedidos = min($total_paginas_pedidos, $pagina_pedidos + 15);

										if ($pagina_pedidos > 1) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_pedidos=1&tableId=tablePedidos'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_pedidos=" . ($pagina_pedidos - 1) . "&tableId=tablePedidos'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_pedidos; $i <= $ultima_pagina_pedidos; $i++) {
											if ($i == $pagina_pedidos) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_pedidos=$i&tableId=tablePedidos'\">$i</button>";
											}
										}

										if ($pagina_pedidos < $total_paginas_pedidos) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_pedidos=" . ($pagina_pedidos + 1) . "&tableId=tablePedidos'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaViatura.php?IdViatura=$idViatura&pagina_pedidos=$total_paginas_pedidos&tableId=tablePedidos'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaPedidosInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_pedidos; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaPedidosInput', <?php echo $total_paginas_pedidos; ?>, 'areaViatura.php', <?= $idViatura ?>, 'pagina_pedidos', 'tablePedidos'); }">

									<script>
										var totalPaginas = <?php echo $total_paginas_pedidos; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
	
		<div class="caixaSaudacao">
			<p>Matrícula: <?php echo $viatura['Matricula']; ?></p>
			<p>Marca: <?php echo $viatura['Marca']; ?></p>
			<p>Cilindrada: <?php echo $viatura['Modelo']; ?></p>
			<p>Modelo: <?php echo $viatura['CC']; ?></p>
			<p>Peso Bruto: <?php echo $viatura['PB']; ?></p>
			<p>Data de Matrícula: <?php echo $viatura['dataMatricula']; ?></p>
			<p>Lugares: <?php echo $viatura['Lugares']; ?></p>
			<p>Categoria: <?php echo $viatura['Categoria']; ?></p>
			<p>Afectação: <?php echo $viatura['Afetacao']; ?></p>
			<p>Responsável: 
				<?php if (!empty($viatura['numResp']) && !empty($viatura['NomeOper'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $viatura["IdOperador"]; ?>'>
					<?php echo $viatura['numResp']; ?>) <?php echo $viatura['NomeOper']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Livrete: <?php echo $viatura['Livrete']; ?></p>
			<p>Seguro: <?php echo $viatura['Seguro']; ?></p>
			<p>Data de Inspeção: <?php echo $viatura['dataInspecao']; ?></p>
			<p>Notas: <?php echo $viatura['Notas']; ?></p>

			<button class='btnEditar' data-dados='<?php echo json_encode($viatura); ?>'>Editar</button>
			<button class='btnEliminar' idViatura="<?php echo $viatura['IdViatura']; ?>" onclick="funEliminarViatura(this)">Eliminar</button>
		</div>
		
		<!-- Modal para edição-->
		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Viatura</h2>
				<form id="formEditar" action="db/editarSGO/db_editarViaturas.php" method="POST">
					<input type="hidden" name="redirectUrl" readonly>
					<input type="hidden" id="editIdViatura" name="editIdViatura" readonly>
					
					<label for="editMatricula">Matrícula:</label>
					<input type="text" id="editMatricula" name="editMatricula"><br>

					<label for="editMarca">Marca:</label>
					<input type="text" id="editMarca" name="editMarca"><br>

					<label for="editModelo">Modelo:</label>
					<input type="text" id="editModelo" name="editModelo"><br>

					<label for="editCC">Cilindrada:</label>
					<input type="number" id="editCC" name="editCC" placeholder="Cc"><br>

					<label for="editPB">Peso Bruto:</label>
					<input type="number" id="editPB" name="editPB" placeholder="Kg"><br>

					<label for="editDataMatricula">Data de Matrícula:</label>
					<input type="date" id="editDataMatricula" name="editDataMatricula"><br>

					<label for="editLugares">Lugares:</label>
					<input type="number" id="editLugares" name="editLugares"><br>

					<label for="editCategoria">Categoria:</label>
					<input type="text" id="editCategoria" name="editCategoria"><br>

					<label for="editAfetacao">Afetação:</label>
					<input type="text" id="editAfetacao" name="editAfetacao"><br>

					<label for="editNumResp">Responsável:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numResp', 'editListDropdown_numResp', 'editNumResp')" required>
						<input type="hidden" id="editNumResp" name="editNumResp" readonly>
						<div id="editListDropdown_numResp" class="listDropdown"></div>
					</div><br>

					<label for="editLivrete">Livrete:</label>
					<input type="text" id="editLivrete" name="editLivrete"><br>

					<label for="editSeguro">Seguro:</label>
					<input type="text" id="editSeguro" name="editSeguro"><br>

					<label for="editDataInspecao">Data de Inspeção:</label>
					<input type="date" id="editDataInspecao" name="editDataInspecao"><br>

					<label for="editNotas">Notas:</label>
					<textarea id="editNotas" name="editNotas" rows="6" rows="6" placeholder="Escreva aqui..."></textarea>

					<input type="submit" value="Atualizar Viatura">
				</form>
			</div>
		</div>
    </div>
	
	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
