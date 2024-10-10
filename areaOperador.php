<?php
include __DIR__ . '/db/areaSGO/db_areaOperador.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área do Operador</title>
    <script src="./js/fun_areaOperador.js" defer></script>
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
		<a href="./gestaoOperadores.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área do Operador</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tableDespachos')">Despachos Associados</button>
				<button onclick="showTable('tableOrdem')">Ordens de Serviço Associados</button>
				<button onclick="showTable('tablePedidos')">Pedidos Associados</button>
				<button onclick="showTable('tableServicos')">Serviços Associados</button>
				<button onclick="showTable('tableViaturas')">Viaturas Associadas</button>
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
										<?php endif; ?>
									</td>
									<td>
										<?php if (!empty($row['IdOrdem']) && !empty($row['numOrdem'])): ?>
											<a href='areaOrdemServico.php?IdOrdem=<?= $row['IdOrdem'] ?>'>
												<?php echo $row['IdOrdem'] . ") " . $row['numOrdem']; ?></a>
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
									<td><a href='areaPedido.php?IdPedido=<?= $row['IdPedido'] ?>'><?= $row['IdPedido'] ?></a></td>
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
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$idOperador&pagina_pedidos=1&tableId=tablePedidos'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$idOperador&pagina_pedidos=" . ($pagina_pedidos - 1) . "&tableId=tablePedidos'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_pedidos; $i <= $ultima_pagina_pedidos; $i++) {
											if ($i == $pagina_pedidos) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$idOperador&pagina_pedidos=$i&tableId=tablePedidos'\">$i</button>";
											}
										}

										if ($pagina_pedidos < $total_paginas_pedidos) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$idOperador&pagina_pedidos=" . ($pagina_pedidos + 1) . "&tableId=tablePedidos'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$idOperador&pagina_pedidos=$total_paginas_pedidos&tableId=tablePedidos'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaPedidosInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_pedidos; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaPedidosInput', <?php echo $total_paginas_pedidos; ?>, 'areaOperador.php', <?= $idOperador ?>, 'pagina_pedidos', 'tablePedidos'); }">

									<script>
										var totalPaginas = <?php echo $total_paginas_pedidos; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>

            <div id="tableServicos" class="containerTable">
                <a href='gestaoServicos.php'><h2>Serviços Associados</h2></a>
                <table>
                    <thead>
                        <tr>
                            <th>Nome do Serviço</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php if ($result_servicos->num_rows > 0): ?>
							<?php while ($row = $result_servicos->fetch_assoc()): ?>
								<tr>
									<td><a href='areaServico.php?IdServico=<?= $row['IdServico'] ?>'><?= $row['NomeServ'] ?></a></td>
									<td><?= $row['Categoria'] ?></td>
								</tr>
							<?php endwhile; ?>
						<?php else: ?>
							<tr>
								<td colspan="2" style="text-align: center;">Nenhum registo encontrado.</td>
							</tr>
						<?php endif; ?>	
                    </tbody>
						<tfoot>
							<tr>
								<td colspan="2">
									<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_servicos; ?></p>
									<div class="containerPaginacao">
										<div id="paginacao">
											<?php
											$primeira_pagina_servicos = max(1, $pagina_servicos - 15); 
											$ultima_pagina_servicos = min($total_paginas_servicos, $pagina_servicos + 15);

											if ($pagina_servicos > 1) {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_servicos=1&tableId=tableServicos'\">&laquo; Primeira</button>";
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_servicos=" . ($pagina_servicos - 1) . "&tableId=tableServicos'\">&lt; Anterior</button>";
											} else {
												echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
												echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
											}

											for ($i = $primeira_pagina_servicos; $i <= $ultima_pagina_servicos; $i++) {
												if ($i == $pagina_servicos) {
													echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
												} else {
													echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_servicos=$i&tableId=tableServicos'\">$i</button>";
												}
											}

											if ($pagina_servicos < $total_paginas_servicos) {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_servicos=" . ($pagina_servicos + 1) . "&tableId=tableServicos'\">Próxima &gt;</button>";
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_servicos=$total_paginas_servicos&tableId=tableServicos'\">Última &raquo;</button>";
											} else {
												echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
												echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
											}
											?>
										</div>

										<input type="number" id="paginaServicosInput" class="inputPaginacao" placeholder="Ir para página..." 
											min="1" max="<?php echo $total_paginas_servicos; ?>" 
											onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaServicosInput', <?php echo $total_paginas_servicos; ?>, 'areaOperador.php', <?= $IdOperador ?>, 'pagina_servicos', 'tableServicos'); }">
										
										<script>
											var totalPaginas = <?php echo $total_paginas_servicos; ?>;
										</script>
									</div>
								</td>
							</tr>
						</tfoot>
                </table>
            </div>

            <div id="tableViaturas" class="containerTable">
                <a href='gestaoViaturas.php'><h2>Viaturas Associadas</h2></a>
                <table>
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Categoria</th>
                            <th>Afectação</th>
                            <th>Data de Inspeção</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php if ($result_viaturas->num_rows > 0): ?>
							<?php while ($row = $result_viaturas->fetch_assoc()): ?>
								<tr>
									<td><a href='areaViatura.php?IdViatura=<?= $row['IdViatura'] ?>'><?= $row['Matricula'] ?></a></td>
									<td><?= $row['Categoria'] ?></td>
									<td><?= $row['Afetacao'] ?></td>
									<td><?= $row['dataInspecao'] ?></td>
									<td><?= $row['Notas'] ?></td>
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
								<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_viaturas; ?></p>
								<div class="containerPaginacao">
									<div id="paginacao">
										<?php
										$primeira_pagina_viaturas = max(1, $pagina_viaturas - 15); 
										$ultima_pagina_viaturas = min($total_paginas_viaturas, $pagina_viaturas + 15);

										if ($pagina_viaturas > 1) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_viaturas=1&tableId=tableViaturas'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_viaturas=" . ($pagina_viaturas - 1) . "&tableId=tableViaturas'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_viaturas; $i <= $ultima_pagina_viaturas; $i++) {
											if ($i == $pagina_viaturas) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_viaturas=$i&tableId=tableViaturas'\">$i</button>";
											}
										}

										if ($pagina_viaturas < $total_paginas_viaturas) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_viaturas=" . ($pagina_viaturas + 1) . "&tableId=tableViaturas'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaOperador.php?IdOperador=$IdOperador&pagina_viaturas=$total_paginas_viaturas&tableId=tableViaturas'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaViaturasInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_viaturas; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaViaturasInput', <?php echo $total_paginas_viaturas; ?>, 'areaOperador.php', <?= $IdOperador ?>, 'pagina_viaturas', 'tableViaturas'); }">
									
									<script>
										var totalPaginas = <?php echo $total_paginas_viaturas; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
		
		<div class="caixaSaudacao">
			<p>Número: <?php echo $operador['numOper']; ?></p>
			<p>Nome: <?php echo $operador['NomeOper']; ?></p>
			<p>Tipo de Operário: <?php echo $operador['tipoOper']; ?></p>

			<?php
			$funcao = '';
			$categoria = '';
			$hierarquia = '';
			$email = '';
			$servico = '';

			// Dividindo os tipos de operador
			$tipoOper_array = explode(', ', $operador['tipoOper']);

			foreach ($tipoOper_array as $tipoOperItem) {
				switch ($tipoOperItem) {
					case 'Utilizador':
						$categoria = $operador['UtilizadorCategoria'] . ' ';
						$hierarquia = $operador['UtilizadorHierarquia'] . ' ';
						break;

					case 'Funcionário':
						$funcao = $operador['FuncionarioFuncao'] . ' ';
						$categoria = $operador['FuncionarioCategoria'] . ' '; 
						$email = $operador['FuncionarioEmail'] . ' ';
						$servico = $operador['NomeServ'] . ' ';
						break;

					case 'Responsável':
						$funcao = $operador['ResponsavelFuncao'] . ' '; 
						$hierarquia = $operador['ResponsavelHierarquia'] . ' '; 
						break;

					case 'Ordenante':
						$funcao = $operador['OrdenanteFuncao'] . ' '; 
						$hierarquia = $operador['OrdenanteHierarquia'] . ' ';
						break;

					default:
						echo "Tipo de operador não reconhecido.";
				}
			}

			echo "<p>Função: " . $funcao . "</p>"; 
			echo "<p>Categoria: " . $categoria . "</p>";
			echo "<p>Hierarquia: " . $hierarquia . "</p>";
			echo "<p>E-mail: " . $email . "</p>";
			echo "<p>Serviço: " . $servico . "</p>";
			?>

			<button class='btnEditar' data-dados='<?php echo json_encode($operador); ?>'>Editar</button>
			<button class='btnEliminar' IdOperador="<?php echo $operador['IdOperador']; ?>" onclick="funEliminarOperador(this)">Eliminar</button>
		</div>
		
		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Operador</h2>
				<form id="formEditar" action="db/editarSGO/db_editarOperadores.php" method="POST">
					<input type="hidden" name="redirectUrl" value="areaOperador.php" readonly>
					<input type="hidden" id="editIdOperador" name="editIdOperador" readonly>

					<label for="editNumOper">Número de Operador:</label>
					<input type="number" id="editNumOper" name="editNumOper"><br>

					<label for="editNomeOper">Nome de Operador:</label>
					<input type="text" id="editNomeOper" name="editNomeOper"><br>

					<label>Tipo de Operador:</label><br>
					<div>
						<label for="editUtilizador">Utilizador</label>
						<input type="checkbox" id="editUtilizador" name="tipoOper[]" value="Utilizador" onchange="toggleEditTipoOper()">
					</div>
					<div>
						<label for="editFuncionario">Funcionário</label>
						<input type="checkbox" id="editFuncionario" name="tipoOper[]" value="Funcionário" onchange="toggleEditTipoOper()">
					</div>
					<div>
						<label for="editResponsavel">Responsável</label>
						<input type="checkbox" id="editResponsavel" name="tipoOper[]" value="Responsável" onchange="toggleEditTipoOper()">
					</div>
					<div>
						<label for="editOrdenante">Ordenante</label>
						<input type="checkbox" id="editOrdenante" name="tipoOper[]" value="Ordenante" onchange="toggleEditTipoOper()">
					</div>
					<input type="hidden" id="editTipoOper" name="editTipoOper"><br>

					<!-- Campos exclusivos para cada tipoOper -->
					<div id="fieldEditPassword" style="display:none;">
						<input type="checkbox" id="checkEditPassword" name="checkEditPassword" onclick="togglePasswordFields(this)">
						<label for="checkEditPassword">Desejo editar a senha</label><br>

						<label for="editPassword">Password:</label>
						<input type="password" id="editPassword" name="editPassword" disabled><br>

						<label for="confirmEditPassword">Confirmar Password:</label>
						<input type="password" id="confirmEditPassword" name="confirmEditPassword" disabled><br>
					</div>

					<div id="fieldEditFuncao" style="display:none;">
						<label for="editFuncao">Função:</label>
						<input type="text" id="editFuncao" name="editFuncao"><br>
					</div>

					<div id="fieldEditCategoria" style="display:none;">
						<label for="editCategoria">Categoria:</label>
						<input type="text" id="editCategoria" name="editCategoria"><br>
					</div>

					<div id="fieldEditHierarquia" style="display:none;">
						<label for="editHierarquia">Hierarquia:</label>
						<input type="text" id="editHierarquia" name="editHierarquia"><br>
					</div>
					
					<div id="fieldEditEmail" style="display:none;">
						<label for="editEmail">E-mail:</label>
						<input type="email" id="editEmail" name="editEmail"><br>
					</div>

					<div id="fieldEditServico" style="display:none;">
						<label for="editIdServico">Serviço:</label>
						<div class="dropdown">
							<input type="text" id="editInputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdServico', 'editListDropdown_IdServico', 'editIdServico')">
							<input type="hidden" id="editIdServico" name="editIdServico">
							<div id="editListDropdown_IdServico" class="listDropdown"></div>
						</div><br>
					</div>

					<input type="submit" value="Atualizar Operador">
				</form>
			</div>
		</div>		
    </div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
