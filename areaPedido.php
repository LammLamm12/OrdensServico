<?php
include __DIR__ . '/db/areaSGO/db_areaPedido.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área de Pedido</title>
    <script src="./js/fun_areaPedido.js" defer></script>
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
		<a href="./gestaoPedidos.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área de Pedido</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tableDespachos')">Despachos Associados</button>
				<button onclick="showTable('tableOrdem')">Ordens de Serviço Associadas</button>
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
							<th>Ordem de Serviço</th>
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
											<td></td>
										<?php endif; ?>
									</td>
									<td>
										<?php if (!empty($row['IdOrdem']) && !empty($row['numOrdem'])): ?>
											<a href='areaOrdemServico.php?IdOrdem=<?= $row['IdOrdem'] ?>'>
												<?php echo $row['IdOrdem'] . ") " . $row['numOrdem']; ?></a>
										<?php else: ?>
											<td></td>
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
			
            <div id="tableOrdem" class="containerTable">
                <a href='gestaoOrdemServicos.php'><h2>Ordens de Serviço Associados</h2></a>
                <table>
                    <thead>
                        <tr>
							<th>Id</th>
							<th>Número</th>
							<th>Id do Pedido</th>
							<th>Tipo do Pedido</th>
							<th>Requerente</th>
							<th>Responsável</th>
							<th>Data de Registo</th>
							<th>Descritivo</th>
							<th>Estado do Pedido</th>
							<th>Local de Destino</th>
							<th>Serviço de Destino</th>
							<th>Despacho</th>
							<th>Data de Despacho</th>
							<th>Data de Início</th>
							<th>Data de Fim</th>
							<th>Kms de Ida</th>
							<th>Kms de Volta</th>
							<th>Total de Kms</th>
                        </tr>
                    </thead>
					<tbody>
						<?php if ($result_ordem_servicos->num_rows > 0): ?>
							<?php while ($row = $result_ordem_servicos->fetch_assoc()): ?>
								<tr>
									<td><a href='areaOrdemServico.php?IdOrdem=<?= $row['IdOrdem'] ?>'><?= $row['IdOrdem'] ?></a></td>
									<td><?= $row['numOrdem'] ?></td>
									<td><a href='areaPedido.php?IdPedido=<?= $row['IdPedido'] ?>'><?= $row['IdPedido'] ?></a></td>
									<td><?= $row['tipoPedido'] ?></td>
									<?php if (!empty($row['IdRequerente']) && !empty($row['NomeReq'])): ?>
										<td><a href='areaRequerente.php?IdRequerente=<?= $row['IdRequerente'] ?>'><?= $row['IdRequerente'] . ") " . $row['NomeReq'] ?></a></td>
									<?php else: ?>
										<td></td>
									<?php endif; ?>
									<?php if (!empty($row['numResp']) && !empty($row['NomeOper'])): ?>
										<td><a href='areaOperador.php?IdOperador=<?= $row['IdResponsavel'] ?>'><?= $row['numResp'] . ") " . $row['NomeOper'] ?></a></td>
									<?php else: ?>
										<td></td>
									<?php endif; ?>
									<td><?= $row['dataRegisto'] ?></td>
									<?php if (!empty($row['Descritivo']) && strlen($row['Descritivo']) > 30): ?>
										<td>
											<button onclick="funtoggleNotas(this)">Descritivo</button>
											<textarea rows="3" style="display:none;" readonly><?= $row['Descritivo'] ?></textarea>
										</td>
									<?php else: ?>
										<td><?= $row['Descritivo'] ?></td>
									<?php endif; ?>
									<td><?= $row['estadoPedido'] ?></td>
									<td><?= $row['LocalDestino'] ?></td>
									<td><a href='areaServico.php?IdServico=<?= $row['IdServico'] ?>'><?= $row['NomeServ'] ?></a></td>
									<?php if (!empty($row['Despacho']) && strlen($row['Despacho']) > 30): ?>
										<td>
											<button onclick="funtoggleNotas(this)">Despacho</button>
											<textarea rows="3" style="display:none;" readonly><?= $row['Despacho'] ?></textarea>
										</td>
									<?php else: ?>
										<td><?= $row['Despacho'] ?></td>
									<?php endif; ?>
									<td><?= $row['dataDespacho'] ?></td>
									<td><?= $row['dataIni'] ?></td>
									<td><?= $row['dataFim'] ?></td>
									<td><?= $row['kmsIda'] ?></td>
									<td><?= $row['kmsVolta'] ?></td>
									<td><?= $row['kmsTotal'] ?></td>
								</tr>
							<?php endwhile; ?>
						<?php else: ?>
							<tr>
								<td colspan="20" style="text-align: center;">Nenhum registo encontrado.</td>
							</tr>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="20">
								<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_ordem_servicos; ?></p>
								<div class="containerPaginacao">
									<div id="paginacao">
										<?php
										$primeira_pagina_ordem_servicos = max(1, $pagina_ordem_servicos - 15); 
										$ultima_pagina_ordem_servicos = min($total_paginas_ordem_servicos, $pagina_ordem_servicos + 15);

										if ($pagina_ordem_servicos > 1) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_ordem_servicos=1&tableId=ordemServicosTable'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_ordem_servicos=" . ($pagina_ordem_servicos - 1) . "&tableId=ordemServicosTable'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_ordem_servicos; $i <= $ultima_pagina_ordem_servicos; $i++) {
											if ($i == $pagina_ordem_servicos) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_ordem_servicos=$i&tableId=ordemServicosTable'\">$i</button>";
											}
										}

										if ($pagina_ordem_servicos < $total_paginas_ordem_servicos) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_ordem_servicos=" . ($pagina_ordem_servicos + 1) . "&tableId=ordemServicosTable'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_ordem_servicos=$total_paginas_ordem_servicos&tableId=ordemServicosTable'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaOrdemServicosInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_ordem_servicos; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaOrdemServicosInput', <?php echo $total_paginas_ordem_servicos; ?>, 'areaOperador.php', <?= $IdOperador ?>, 'pagina_ordem_servicos', 'ordemServicosTable'); }">
									
									<script>
										var totalPaginas = <?php echo $total_paginas_ordem_servicos; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
		
		<div class="caixaSaudacao">
			<p>Id do Pedido: <?php echo $pedido['IdPedido']; ?></p>
			<p>Tipo de pedido: <?php echo $pedido['tipoPedido']; ?></p>
			<p>Estado do Pedido: <?php echo $pedido['estadoPedido']; ?></p>
			<p>Requerente: 
				<?php if (!empty($pedido['IdRequerente']) && !empty($pedido['NomeReq'])): ?>
					<a href='areaRequerente.php?IdRequerente=<?php echo $pedido["IdRequerente"]; ?>'>
						<?php echo $pedido['IdRequerente'] . ") " . $pedido['NomeReq']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Responsável: 
				<?php if (!empty($pedido['numResp']) && !empty($pedido['NomeResp'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $pedido["IdResponsavel"]; ?>'>
						<?php echo $pedido['numResp'] . ") " . $pedido['NomeResp']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Ordenante: 
				<?php if (!empty($pedido['numOrd']) && !empty($pedido['NomeOrd'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $pedido["IdOrdenante"]; ?>'>
						<?php echo $pedido['numOrd'] . ") " . $pedido['NomeOrd']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Serviço: 
				<?php if (!empty($pedido['IdServico']) && !empty($pedido['NomeServ'])): ?>
					<a href='areaServico.php?IdServico=<?php echo $pedido["IdServico"]; ?>'>
						<?php echo $pedido['NomeServ']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Descrição:
				<?php if (!empty($pedido['Descricao']) && strlen($pedido['Descricao']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Descrição</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $pedido['Descricao']; ?></textarea>
				<?php else: ?>
					<?php echo $pedido['Descricao']; ?>
				<?php endif; ?>
			</p>
			<p>Funcionário 1: 
				<?php if (!empty($pedido['IdFuncionario1']) && !empty($pedido['NomeFunc1'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $pedido["IdFuncionario1"]; ?>'>
						<?php echo $pedido['IdFuncionario1'] . ") " . $pedido['NomeFunc1']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Funcionário 2: 
				<?php if (!empty($pedido['IdFuncionario2']) && !empty($pedido['NomeFunc2'])): ?>
					<a href='areaOperador.php?IdOperador=<?php echo $pedido["IdFuncionario2"]; ?>'>
						<?php echo $pedido['IdFuncionario2'] . ") " . $pedido['NomeFunc2']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Data de Registo: <?php echo $pedido['dataRegisto']; ?></p>
			<p>Observações: <?php echo $pedido['Observacoes']; ?></p>
			<p>Despacho:
				<?php if (!empty($pedido['Despacho']) && strlen($pedido['Despacho']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Descrição</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $pedido['Despacho']; ?></textarea>
				<?php else: ?>
					<?php echo $pedido['Despacho']; ?>
				<?php endif; ?>
			</p>
			<p>Data de Despacho: <?php echo $pedido['dataDespacho']; ?></p>
			<p>Notas:
				<?php if (!empty($pedido['Notas']) && strlen($pedido['Notas']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Notas</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $pedido['Notas']; ?></textarea>
				<?php else: ?>
					<?php echo $pedido['Notas']; ?>
				<?php endif; ?>
			</p>
			<p>Hora de Início: <?php echo $pedido['HoraIni']; ?></p>
			<p>Data de Início: <?php echo $pedido['DataIni']; ?></p>
			<p>Hora de Fim: <?php echo $pedido['HoraFim']; ?></p>
			<p>Data de Fim: <?php echo $pedido['DataFim']; ?></p>
			<p>Viatura: 
				<?php if (!empty($pedido['IdViatura']) && !empty($pedido['Matricula'])): ?>
					<a href='areaViatura.php?IdViatura=<?php echo $pedido["IdViatura"]; ?>'>
						<?php echo $pedido['IdViatura'] . ") " . $pedido['Matricula']; ?>
					</a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Kms Previstos: <?php echo $pedido['kmsPrevistos']; ?></p>
			<p>Local de Partida: <?php echo $pedido['LocalPartida']; ?></p>
			<p>Local de Destino <?php echo $pedido['LocalDestino']; ?></p>
			<p>Kms de Ida: <?php echo $pedido['kmsIda']; ?></p>
			<p>Kms de Volta: <?php echo $pedido['kmsVolta']; ?></p>
			<p>Kms Total: <?php echo $pedido['kmsTotal']; ?></p>
			<p>Anotações:
				<?php if (!empty($pedido['Anotacoes']) && strlen($pedido['Anotacoes']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Anotações</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $pedido['Anotacoes']; ?></textarea>
				<?php else: ?>
					<?php echo $pedido['Anotacoes']; ?>
				<?php endif; ?>
			</p>

			<button class='btnEditar' data-dados='<?php echo json_encode($pedido); ?>'>Editar</button>
			<button class='btnEliminar'idpedido="<?php echo $pedido['IdPedido']; ?>" onclick="funEliminarPedido(this)">Eliminar</button>
		</div>
		
		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Pedido</h2>
				<form id="formEditar" action="db/editarSGO/db_editarPedidos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoPedidos.php" readonly>
					<input type="hidden" id="editIdPedido" name="editIdPedido" readonly>
					
					<label for="editTipoPedido">Tipo de Pedido:</label>
					<select id="editTipoPedido" name="editTipoPedido">
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>
					
					<label for="editEstadoPedido">Estado do Pedido:</label>
					<select id="editEstadoPedido" name="editEstadoPedido">
						<option value="Pendente">Pendente</option>
						<option value="Cancelado">Cancelado</option>
						<option value="Não Autorizado">Não Autorizado</option>
						<option value="Autorizado">Autorizado</option>
						<option value="Rejeitado">Rejeitado</option>
						<option value="Concluído">Concluído</option>
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
					
					<label for="editNumOrdem">Ordenante:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numOrdem" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numOrdem', 'editListDropdown_numOrdem', 'editNumOrdem')" required>
						<input type="hidden" id="editNumOrdem" name="editNumOrdem">
						<div id="editListDropdown_numOrdem" class="listDropdown"></div>
					</div><br>
					
					<label for="editIdServico">Serviço:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdServico', 'editListDropdown_IdServico', 'editIdServico')" required>
						<input type="hidden" id="editIdServico" name="editIdServico">
						<div id="editListDropdown_IdServico" class="listDropdown"></div>
					</div><br>

					<label for="editDescricao">Descrição:</label>
					<textarea id="editDescricao" rows="6" placeholder="Escreva aqui..."></textarea><br>
					
					<label for="editNumFunc1">Funcionário 1:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numFunc1" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numFunc1', 'editListDropdown_numFunc1', 'editNumFunc1')" required>
						<input type="hidden" id="editNumFunc1" name="editNumFunc1">
						<div id="editListDropdown_numFunc1" class="listDropdown"></div>
					</div><br>

					<label for="editNumFunc2">Funcionário 2:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numFunc2" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numFunc2', 'editListDropdown_numFunc2', 'editNumFunc2')">
						<input type="hidden" id="editNumFunc2" name="editNumFunc2">
						<div id="editListDropdown_numFunc2" class="listDropdown"></div>
					</div><br>

					<label for="editDataRegisto">Data de Registo:</label>
					<input type="date" id="editDataRegisto" name="editDataRegisto" readonly><br>

					<label for="editObservacoes">Observações:</label>
					<textarea id="editObservacoes" name="editObservacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editDespacho">Despacho:</label>
					<textarea id="editDespacho" name="editDespacho" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editDataDespacho">Data de Despacho:</label>
					<input type="date" id="editDataDespacho" name="editDataDespacho"><br>

					<label for="editNotas">Notas:</label>
					<textarea id="editNotas" name="editNotas" rows="6" placeholder="Escreva aqui..."></textarea><br>
					
					<label for="editHoraIni">Hora de Início:</label>
					<input type="date" id="editHoraIni" name="editHoraIni"><br>

					<label for="editDataIni">Data de Início:</label>
					<input type="date" id="editDataIni" name="editDataIni"><br>

					<label for="editHoraFim">Hora de Fim:</label>
					<input type="date" id="editHoraFim" name="editHoraFim"><br>

					<label for="editDataFim">Data de Fim:</label>
					<input type="date" id="editDataFim" name="editDataFim"><br>
					
					<label for="editIdViatura">Viatura:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdViatura" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdViatura', 'editListDropdown_IdViatura', 'editIdViatura')" required>
						<input type="hidden" id="editIdViatura" name="editIdViatura">
						<div id="editListDropdown_IdViatura" class="listDropdown"></div>
					</div><br>

					<label for="editKmsPrevistos">Kms Previstos:</label>
					<input type="number" id="editKmsPrevistos" name="editKmsPrevistos"><br>

					<label for="editLocalPartida">Local de Partida:</label>
					<input type="text" id="editLocalPartida" name="editLocalPartida"><br>

					<label for="editLocalDestino">Local de Destino:</label>
					<input type="text" id="editLocalDestino" name="editLocalDestino"><br>
					
					<label for="editKmsIda">Kms de Ida:</label>
					<input type="number" id="editKmsIda" name="editKmsIda"  oninput="calcularEditKms()" placeholder="kms/h"><br>

					<label for="editKmsVolta">Kms de Volta:</label>
					<input type="number" id="editKmsVolta" name="editKmsVolta"  oninput="calcularEditKms()" placeholder="kms/h"><br>
					
					<label for="editKmsTotal">Kms Total</label>
					<input type="number" id="editKmsTotal" name ="editKmsTotal" placeholder="Ida + Volta" readonly><br>

					<label for="editAnotacoes">Anotações:</label>
					<textarea id="editAnotacoes" name="editAnotacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<input type="submit" value="Atualizar Pedido">
				</form>
			</div>
		</div>	
    </div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
