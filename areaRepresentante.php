<?php
include __DIR__ . '/db/areaSGO/db_areaRepresentante.php';


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Área de Representante</title>
    <script src="./js/fun_areaRepresentante.js" defer></script>
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
		<a href="./gestaoRepresentantes.php"><img src="./img/seta_esquerda.webp" alt="Gestão"></a>
        <h2>Área de Representante</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<div class="containerRight">
				<button onclick="showTable('tableRequerentes')">Requerentes Associados</button>
			</div>

			
            <div id="tableRequerentes" class="containerTable">
                <a href='gestaoRequerentes.php'><h2>Requerentes Associados</h2></a>
                <table>
                    <thead>
                        <tr>
                            <th>Nome do Requerente</th>
                            <th>Entidade</th>
                            <th>Tipo de Entidade</th>
							<th>NIF</th>
                            <th>CAE</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php if ($result_requerentes->num_rows > 0): ?>
							<?php while ($row = $result_requerentes->fetch_assoc()): ?>
								<tr>
									<td><a href='areaRequerente.php?IdRequerente=<?= $row['IdRequerente'] ?>'><?= $row['NomeReq'] ?></a></td>
									<td><?= $row['Entidade'] ?></td>
									<td><?= $row['tipoEntidade'] ?></td>
									<td><?= $row['NIF'] ?></td>
									<td><?= $row['CAE'] ?></td>
									<td><?= $row['Notas'] ?></td>
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
								<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos_requerentes; ?></p>
								<div class="containerPaginacao">
									<div id="paginacao">
										<?php
										$primeira_pagina_requerentes = max(1, $pagina_requerentes - 15); 
										$ultima_pagina_requerentes = min($total_paginas_requerentes, $pagina_requerentes + 15);

										if ($pagina_requerentes > 1) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaRepresentante.php?IdRepresentante=$idRepresentante&pagina_requerentes=1&tableId=tableRequerentes'\">&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaRepresentante.php?IdRepresentante=$idRepresentante&pagina_requerentes=" . ($pagina_requerentes - 1) . "&tableId=tableRequerentes'\">&lt; Anterior</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
											echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
										}

										for ($i = $primeira_pagina_requerentes; $i <= $ultima_pagina_requerentes; $i++) {
											if ($i == $pagina_requerentes) {
												echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
											} else {
												echo "<button class='btnPaginacao' onclick=\"window.location.href='areaRepresentante.php?IdRepresentante=$idRepresentante&pagina_requerentes=$i&tableId=tableRequerentes'\">$i</button>";
											}
										}

										if ($pagina_requerentes < $total_paginas_requerentes) {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaRepresentante.php?IdRepresentante=$idRepresentante&pagina_requerentes=" . ($pagina_requerentes + 1) . "&tableId=tableRequerentes'\">Próxima &gt;</button>";
											echo "<button class='btnPaginacao' onclick=\"window.location.href='areaRepresentante.php?IdRepresentante=$idRepresentante&pagina_requerentes=$total_paginas_requerentes&tableId=tableRequerentes'\">Última &raquo;</button>";
										} else {
											echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
											echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
										}
										?>
									</div>

									<input type="number" id="paginaRequerentesInput" class="inputPaginacao" placeholder="Ir para página..." 
										min="1" max="<?php echo $total_paginas_requerentes; ?>" 
										onkeydown="if(event.key === 'Enter'){ funInputPagina('paginaRequerentesInput', <?php echo $total_paginas_requerentes; ?>, 'areaRepresentante.php', <?= $idRepresentante ?>, 'pagina_requerentes', 'tableRequerentes'); }">

									<script>
										var totalPaginas = <?php echo $total_paginas_requerentes; ?>;
									</script>
								</div>
							</td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
	
		<div class="caixaSaudacao">
			<p>Id do Representante: <?php echo $representante['IdRepresentante']; ?></p>
			<p>Número: <?php echo $representante['numRep']; ?></p>
			<p>Nome: <?php echo $representante['NomeRep']; ?></p>
			<p>Cargo: <?php echo $representante['Cargo']; ?></p>
			<p>Telemóvel: <?php echo $representante['Telemovel']; ?></p>
			<p>Telefone: <?php echo $representante['Telefone']; ?></p>
			<p>E-mail: <?php echo $representante['Email']; ?></p>
			<p>Notas: <?php echo $representante['Notas']; ?></p>

			<button class='btnEditar' data-dados='<?php echo json_encode($representante); ?>'>Editar</button>
			<button class='btnEliminar'idRepresentante="<?php echo $representante['IdRepresentante']; ?>" onclick="funEliminarRepresentante(this)">Eliminar</button>
		</div>
		
		<div id="modalEditar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalEditar()">&times;</span>
				<h2>Editar Representante</h2>
				<form id="formEditar" action="db/editarSGO/db_editarRepresentantes.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoRepresentantes.php" readonly>
					<input type="hidden" id="editIdRepresentante" name="editIdRepresentante" readonly>
					
					<label for="editNumRep">Número:</label>
					<input type="number" id="editNumRep" name="editNumRep"><br>

					<label for="editNomeRep">Nome de Representante:</label>
					<input type="text" id="editNomeRep" name="editNomeRep"><br>

					<label for="editCargo">Cargo:</label>
					<input type="text" id="editCargo" name="editCargo"><br>

					<label for="editTelemovel">Telemovel:</label>
					<input type="number" id="editTelemovel" name="editTelemovel"><br>

					<label for="editTelefone">Telefone:</label>
					<input type="number" id="editTelefone" name="editTelefone"><br>
					
					<label for="editEmail">E-mail:</label>
					<input type="email" id="editEmail" name="editEmail"><br>

					<label for="editNotas">Notas:</label>
					<textarea id="editNotas" name="editNotas" rows="6" rows="6" placeholder="Escreva aqui..."></textarea>

					<input type="submit" value="Atualizar Representante">
				</form>
			</div>
		</div>
    </div>
	
	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
