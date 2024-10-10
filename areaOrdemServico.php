<?php
include __DIR__ . '/db/areaSGO/db_areaOrdemServico.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área da Ordem</title>
    <script src="./js/fun_areaOrdemServico.js" defer></script>
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
		<a href="./gestaoOrdemServicos.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área da Ordem de Serviço</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tableDespachos')">Despachos Associados</button>
			</div>

            <div id="tableDespachos" class="containerTable">
                <a href='gestaoDespachos.php'><h2>Despachos Associados</h2></a>
                <table>
                    <thead>
                        <tr>
							<th>Id</th>
							<th>Tipo de Decisão</th>
							<th>Descrição</th>
							<th>Operador</th>
							<th>Pedido</th>
                        </tr>
                    </thead>
					<tbody>
						<?php if ($result_despachos->num_rows > 0): ?>
							<?php while ($row = $result_despachos->fetch_assoc()): ?>
								<tr>
									<td><a href='areaDespacho.php?IdDespacho=<?= $row['IdDespacho'] ?>'><?= $row['IdDespacho'] ?></a></td>
									<td><?= $row['tipoDecisao'] ?></td>				
									<td>
										<?php if (!empty($row['Descricao']) && strlen($row['Descricao']) > 30): ?>
											<button onclick="funtoggleNotas(this)">Descrição</button>
											<textarea rows="3" style="display:none;" readonly><?= $row['Descricao'] ?></textarea>
										<?php else: ?>
											<?= $row['Descricao'] ?>
										<?php endif; ?>
									</td>
									<td>
										<?php if (!empty($row['numOper']) && !empty($row['NomeOper'])): ?>
											<a href='areaOperador.php?IdOperador=<?= $row['IdOperador'] ?>'>
												<?= $row['numOper'] ?>) <?= $row['NomeOper'] ?></a>
										<?php else: ?>
										<?php endif; ?>
									</td>

								</tr>
							<?php endwhile; ?>
						<?php else: ?>
							<tr>
								<td colspan="5" style="text-align: center;">Nenhum registo encontrado.</td>
							</tr>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_despachos; ?></p>
								<div class="containerPaginacao">
									<div id="paginacao">
										<?php
										$primeira_pagina_despachos = max(1, $pagina_despachos - 5); 
										$ultima_pagina_despachos = min($total_paginas_despachos, $pagina_despachos + 5);

										if ($pagina_despachos > 1) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_despachos=1&tableId=tableDespachos'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_despachos=" . ($pagina_despachos - 1) . "&tableId=tableDespachos'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_despachos; $i <= $ultima_pagina_despachos; $i++) {
											if ($i == $pagina_despachos) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_despachos=$i&tableId=tableDespachos'\">$i</button>";
											}
										}

										if ($pagina_despachos < $total_paginas_despachos) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_despachos=" . ($pagina_despachos + 1) . "&tableId=tableDespachos'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_despachos=$total_paginas_despachos&tableId=tableDespachos'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaDespachosInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_despachos; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaDespachosInput', <?php echo $total_paginas_despachos; ?>, 'areaOperador.php', <?= $IdOperador ?>, 'pagina_despachos', 'tableDespachos'); }">
									
									<script>
										var totalPaginas = <?php echo $total_paginas_despachos; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
			
		<div class="caixaSaudacao">
			<p>Id da Ordem: <?php echo $ordem_servicos['IdOrdem']; ?></p>
			<p>Número da Ordem: <?php echo $ordem_servicos['numOrdem']; ?></p>
			<p>Id do Pedido: <a href='areaPedido.php?IdPedido=<?php echo $ordem_servicos["IdPedido"]; ?>'>
				<?php echo $ordem_servicos['IdPedido']; ?></a>
			</p>
			<p>Tipo de Pedido: <?php echo $ordem_servicos['tipoPedido']; ?></p>
			<p>Requerente: <a href='areaRequerente.php?IdRequerente=<?php echo $ordem_servicos["IdRequerente"]; ?>'>
				<?php echo $ordem_servicos['NomeReq']; ?></a>
			</p>
			<p>Responsável: 
				<?php if (!empty($ordem_servicos['numResp']) && !empty($ordem_servicos['NomeOper'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $ordem_servicos["IdOperador"]; ?>'>
					<?php echo $ordem_servicos['numResp'] . ") " . $ordem_servicos['NomeOper']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Data de Registo: <?php echo $ordem_servicos['dataRegisto']; ?></p>
			<p>Descritivo:
				<?php if (!empty($ordem_servicos['Descritivo']) && strlen($ordem_servicos['Descritivo']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Descrição</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $ordem_servicos['Descritivo']; ?></textarea>
				<?php else: ?>
					<?php echo $ordem_servicos['Descritivo']; ?>
				<?php endif; ?>
			</p>
			<p>Estado do Pedido: <?php echo $ordem_servicos['estadoPedido']; ?></p>
			<p>Local de Destino: <?php echo $ordem_servicos['LocalDestino']; ?></p>
			<p>Serviço de Destino: <a href='areaServico.php?IdServico=<?php echo $ordem_servicos["IdServico"]; ?>'>
				<?php echo $ordem_servicos['NomeServ']; ?></a>
			</p>
			<p>Despacho:
				<?php if (!empty($ordem_servicos['Despacho']) && strlen($ordem_servicos['Despacho']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Despacho</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $ordem_servicos['Despacho']; ?></textarea>
				<?php else: ?>
					<?php echo $ordem_servicos['Despacho']; ?>
				<?php endif; ?>
			</p>
			<p>Data do Despacho: <?php echo $ordem_servicos['dataDespacho']; ?></p>
			<p>Data de Início: <?php echo $ordem_servicos['dataIni']; ?></p>
			<p>Data de Fim: <?php echo $ordem_servicos['dataFim']; ?></p>
			<p>Kms de Ida: <?php echo $ordem_servicos['kmsIda']; ?></p>
			<p>Kms de Volta: <?php echo $ordem_servicos['kmsVolta']; ?></p>
			<p>Kms Totais: <?php echo $ordem_servicos['kmsTotal']; ?></p>

			<button class='btnEditar' data-dados='<?php echo json_encode($ordem_servicos); ?>'>Editar</button>
			<button class='btnEliminar' idDespacho="<?php echo $ordem_servicos['IdOrdem']; ?>" onclick="funEliminarOrdem(this)">Eliminar</button>
		</div>

		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Ordem de Serviço</h2>
				<form id="formEditar" action="db/editarSGO/db_editarOrdemServicos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoOrdemServicos.php" readonly>
					<input type="hidden" id="editIdOrdem" name="editIdOrdem" readonly>
					
					<label for="editNumOrdem ">Número da Ordem:</label>
					<input type="text" id="editNumOrdem" name="editNumOrdem" readonly><br>
					
					<label for="editIdPedido">Id do Pedido:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdPedido" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdPedido', 'editListDropdown_IdPedido', 'editIdPedido')" required>
						<input type="hidden" id="editIdPedido" name="editIdPedido">
						<div id="editListDropdown_IdPedido" class="listDropdown"></div>
					</div><br>

					<label for="editTipoPedido">Tipo de Pedido:</label>
					<select id="editTipoPedido" name="editTipoPedido" required><br>
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>
					
					<label for="editIdRequerente">Requerente:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdRequerente" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdRequerente', 'editListDropdown_IdRequerente', 'editIdRequerente')" required>
						<input type="hidden" id="editIdRequerente" name="editIdRequerente">
						<div id="editListDropdown_IdRequerente" class="listDropdown"></div>
					</div><br>
					
					<label for="editNumResp">Responsável:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numResp', 'editListDropdown_numResp', 'editNumResp')" required>
						<input type="hidden" id="editNumResp" name="editNumResp">
						<div id="editListDropdown_numResp" class="listDropdown"></div>
					</div><br>

					<label for="editDataRegisto">Data de Registo:</label>
					<input type="date" id="editDataRegisto" name="editDataRegisto"><br>
					
					<label for="editDescritivo">Descritivo:</label>
					<textarea type="number" id="editDescritivo" name="editDescritivo" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editEstadoPedido">Estado do Pedido:</label>
					<select id="editEstadoPedido" name="editEstadoPedido" placeholder="Necessário" required><br>
						<option value="Pendente">Pendente</option>
						<option value="Cancelado">Cancelado</option>
						<option value="Não Autorizado">Não Autorizado</option>
						<option value="Autorizado">Autorizado</option>
						<option value="Rejeitado">Rejeitado</option>
						<option value="Concluído">Concluído</option>
					</select><br>
					
					<label for="editLocalDestino">Local de Destino:</label>
					<input type="text" id="editLocalDestino" name="editLocalDestino"><br>
					
					<label for="editIdServico">Serviço de Destino:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdServico', 'editListDropdown_IdServico', 'editIdServico')" required>
						<input type="hidden" id="editIdServico" name="editIdServico">
						<div id="editListDropdown_IdServico" class="listDropdown"></div>
					</div><br>

					<label for="editDespacho">Despacho:</label>
					<textarea type="number" id="editDespacho" name="editDespacho" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editDataDespacho">Data de Despacho:</label>
					<input type="date" id="editDataDespacho" name="editDataDespacho"><br>

					<label for="editDataIni">Data de Início:</label>
					<input type="date" id="editDataIni" name="editDataIni"><br>

					<label for="editDataFim">Data de Fim:</label>
					<input type="date" id="editDataFim" name="editDataFim"><br>

					<label for="editKmsIda">Kms de Ida:</label>
					<input type="number" id="editKmsIda" name="editKmsIda"  oninput="calcularEditKms()" placeholder="kms/h"><br>

					<label for="editKmsVolta">Kms de Volta:</label>
					<input type="number" id="editKmsVolta" name="editKmsVolta"  oninput="calcularEditKms()" placeholder="kms/h"><br>
					
					<label for="editKmsTotal">Kms Total</label>
					<input type="number" id="editKmsTotal" name ="editKmsTotal" placeholder="Ida + Volta" readonly><br>
					
					<input type="submit" value="Atualizar Ordem">
				</form>
			</div>
		</div>
    </div>
	
	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
