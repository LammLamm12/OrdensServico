<?php
include __DIR__ . '/db/areaSGO/db_areaRequerente.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área do Requerente</title>
    <script src="./js/fun_areaRequerente.js" defer></script>
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
		<a href="./gestaoRequerentes.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área do Requerente</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tableOrdem')">Ordens de Serviço Associadas</button>
				<button onclick="showTable('tablePedidos')">Pedidos Associados</button>
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
			<p>Nome: <?php echo $requerente['NomeReq']; ?></p>
			<p>Representante: 
				<?php if (!empty($requerente['IdRepresentante']) && !empty($requerente['NomeRep'])): ?>
					<a href='areaRepresentante.php?IdRepresentante=<?php echo $requerente["IdRepresentante"]; ?>'>
					<?php echo $requerente['IdRepresentante']; ?>) <?php echo $requerente['NomeRep']; ?></a>
				<?php else: ?>
				<?php endif; ?>
			</p>
			<p>Entidade: <?php echo $requerente['Entidade']; ?></p>
			<p>Tipo de Entidade: <?php echo $requerente['tipoEntidade']; ?></p>
			<p>NIF: <?php echo $requerente['NIF']; ?></p>
			<p>CAE: <?php echo $requerente['CAE']; ?></p>
			<p>Local: <?php echo $requerente['Local']; ?></p>
			<p>Morada: <?php echo $requerente['Morada']; ?></p>
			<p>Código Postal: <?php echo $requerente['CPostal']; ?></p>
			<p>Telemóvel: <?php echo $requerente['Telemovel']; ?></p>
			<p>Telefone: <?php echo $requerente['Telefone']; ?></p>
			<p>E-mail: <?php echo $requerente['Email']; ?></p>
			<p>Notas: 
				<?php if (!empty($requerente['Notas']) && strlen($requerente['Notas']) > 30): ?>
					<button onclick="funtoggleNotas(this)">Descrição</button>
					<textarea rows="3" style="display:none;" readonly><?php echo $requerente['Notas']; ?></textarea>
				<?php else: ?>
					<?php echo $requerente['Notas']; ?>
				<?php endif; ?>
			</p>
			
			<button class='btnEditar' data-dados='<?php echo json_encode($requerente); ?>'>Editar</button>
			<button class='btnEliminar'idRequerente="<?php echo $requerente['IdRequerente']; ?>" onclick="funEliminarRequerente(this)">Eliminar</button>
		</div>
		
		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Serviço</h2>
				<form id="formEditar" action="db/editarSGO/db_editarRequerentes.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoRequerentes.php" readonly>
					<input type="hidden" id="editIdRequerente" name="editIdRequerente" readonly>
					<label for="editNomeReq">Nome de Requerente:</label>
					<input type="text" id="editNomeReq" name="editNomeReq" placeholder="Necessário" required><br>
					
					<label for="editIdRepresentante">Representante:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdRepresentante" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdRepresentante', 'editListDropdown_IdRepresentante', 'editIdRepresentante')" required>
						<input type="hidden" id="editIdRepresentante" name="editIdRepresentante">
						<div id="editListDropdown_IdRepresentante" class="listDropdown"></div>
					</div><br>

					<label for="editEntidade">Entidade:</label>
					<input type="text" id="editEntidade" name="editEntidade"><br>
					
					<label for="editTipoEntidade">Tipo de entidade:</label>
					<select id="editTipoEntidade" name="editTipoEntidade" required><br>
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>
					
					<label for="editNIF">NIF:</label>
					<input type="number" id="editNIF" name="editNIF"  placeholder="Necessário" required><br>

					<label for="editCAE">CAE:</label>
					<input type="text" id="editCAE" name="editCAE"><br>

					<label for="editLocal">Local:</label>
					<input type="text" id="editLocal" name="editLocal"><br>
					
					<label for="editMorada">Morada:</label>
					<input type="text" id="editMorada" name="editMorada"><br>

					<label for="editCPostal">Código Postal:</label>
					<input type="number" id="editCPostal" name="editCPostal"><br>
					
					<label for="editTelemovel">Telemóvel:</label>
					<input type="number" id="editTelemovel" name="editTelemovel"><br>

					<label for="editTelefone">Telefone:</label>
					<input type="number" id="editTelefone" name="editTelefone"><br>
					
					<label for="editEmail">E-mail:</label>
					<input type="email" id="editEmail" name="editEmail"><br>
					
					<label for="editNotas">Notas:</label>
					<textarea id="editNotas" name="editNotas" rows="6" rows="6" placeholder="Escreva aqui..."></textarea>

					<input type="submit" value="Atualizar Requerente">
				</form>
			</div>
		</div>
    </div>
	
	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
