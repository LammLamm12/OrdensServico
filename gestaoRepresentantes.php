<?php
session_start();

if (!isset($_SESSION['IdOperador'])) {
    header('Location: /OrdensServico/index.php?error=' . urlencode('Não tem permissões para aceder a esta página.'));
    exit();
}

include __DIR__ . '/db/db_connect.php';

// Paginação
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registos_pagina = 30;
$offset = ($pagina - 1) * $registos_pagina;

// Query de busca
$sqlTabela = "SELECT DISTINCT rep.*
			  FROM representantes rep
			  WHERE 1=1 ";

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT rep.IdRepresentante) AS total 
			  FROM representantes rep
			  WHERE 1=1 ";
// Query de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            $filtro .= "AND rep.$coluna = '$procurar' ";
        } else {
            $filtro .= "AND (rep.IdRepresentate = '$procurar' OR
							 rep.numRep = '$procurar' OR
							 rep.NomeRep = '$procurar' OR
							 rep.Cargo = '$procurar' OR
							 rep.Telemovel = '$procurar' OR
							 rep.Telefone = '$procurar' OR
							 rep.Email = '$procurar' OR
							 rep.Notas = '$procurar')";
        }
    } else {
        if ($coluna) {
            $filtro .= "AND rep.$coluna LIKE '%$procurar%' ";
        } else {
            $filtro .= "AND (rep.IdRepresentate LIKE '%$procurar%' OR
							 rep.numRep LIKE '%$procurar%' OR
							 rep.NomeRep LIKE '%$procurar%' OR
							 rep.Cargo LIKE '%$procurar%' OR
							 rep.Telemovel LIKE '%$procurar%' OR
							 rep.Telefone LIKE '%$procurar%' OR
							 rep.Email LIKE '%$procurar%' OR
							 rep.Notas LIKE '%$procurar%')";
        }
    }
    $sqlTabela .= $filtro;
    $sql_total .= $filtro;
}

$sqlTabela .= " LIMIT $registos_pagina OFFSET $offset";

$result_total = $conn->query($sql_total);
$total_registos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registos / $registos_pagina);

$result = $conn->query($sqlTabela);

if (isset($_GET['procurar'])) {
    $data = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'data' => $data,
        'totalPaginas' => $total_paginas,
        'paginaAtual' => $pagina,
        'totalRegistos' => $total_registos
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Representantes</title>
    <script src="./js/fun_gestaoRepresentantes.js" defer></script>
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
        <h2>Lista de Representantes</h2>
	</div>

    <div class="containerGestao">
        <div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="IdRepresentante">Id do Representante</option>
				<option value="numRep">Número</option>
				<option value="NomeRep">Nome</option>
				<option value="Cargo">Cargo</option>
				<option value="Telemovel">Telemóvel</option>
				<option value="Telefone">Telefone</option>
				<option value="Email">E-mail</option>
				<option value="Notas">Notas</option>
			</select>
			<input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Novo Representante</button>
			<table id="table">
				<thead>
					<tr>
						<th>Id do Representante</th>
						<th>Número</th>
						<th>Nome</th>
						<th>Cargo</th>
						<th>Telemóvel</th>
						<th>Telefone</th>
						<th>E-mail</th>
						<th>Notas</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								echo "<td><a href='areaRepresentante.php?IdRepresentante=" . $row['IdRepresentante'] . "'>" . $row['IdRepresentante'] . "</td>";
								echo "<td>" . $row['numRep'] . "</td>";
								echo "<td>" . $row['NomeRep'] . "</td>";
								echo "<td>" . $row['Cargo'] . "</td>";
								echo "<td>" . $row['Telemovel'] . "</td>";
								echo "<td>" . $row['Telefone'] . "</td>";
								echo "<td>" . $row['Email'] . "</td>";
								if (!empty($row['Notas']) && strlen($row['Notas']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Notas</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Notas']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Notas'] . "</td>";
								}
								echo "<td>
										<button class='btnEditar' data-dados='" . json_encode($row) . "'>Editar</button>
										<button class='btnEliminar' IdRepresentante='" . $row['IdRepresentante'] . "' onclick=\"funEliminarRepresentante(this)\">Eliminar</button>
									  </td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='10'>Nenhum registo encontrado</td></tr>";
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10">
							<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos; ?></p>
							<div class="containerPaginacao">
								<div id="paginacao">
									<?php
									$primeira_pagina = max(1, $pagina - 15); 
									$ultima_pagina = min($total_paginas, $pagina + 15);

									if ($pagina > 1) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRepresentantes.php?pagina=1'\">&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRepresentantes.php?pagina=" . ($pagina - 1) . "'\">&lt; Anterior</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
									}

									for ($i = $primeira_pagina; $i <= $ultima_pagina; $i++) {
										if ($i == $pagina) {
											echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
										} else {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRepresentantes.php?pagina=$i'\">$i</button>";
										}
									}

									if ($pagina < $total_paginas) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRepresentantes.php?pagina=" . ($pagina + 1) . "'\">Próxima &gt;</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRepresentantes.php?pagina=$total_paginas'\">Última &raquo;</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>Próxima &gt;</button>";
										echo "<button class='btnPaginacao' disabled>Última &raquo;</button>";
									}
									?>
								</div>

								<input type="number" id="paginaInput" class="inputPaginacao" placeholder="Ir para página..." min="1" max="<?php echo $total_paginas; ?>" onkeydown="if(event.key === 'Enter'){ funInputPagina(); }">
								<!-- Não funciona se não for definido aqui -->
								<script>
									var totalPaginas = <?php echo $total_paginas; ?>;
								</script>
							</div>
						</td>
					</tr>
				</tfoot>
            </table>
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
		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Representante</h2>
				<form action="db/adicionarSGO/db_adicionarRepresentantes.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoRepresentantes.php" readonly>
					
					<label for="numRep">Número de Representante:</label>
					<input type="number" id="numRep" name="numRep" placeholder="Necessário" required><br>

					<label for="NomeRep">Nome:</label>
					<input type="text" id="NomeRep" name="NomeRep" placeholder="Necessário" required><br>
					
					<label for="Cargo">Cargo:</label>
					<input type="text" id="Cargo" name="Cargo"><br>
					
					<label for="Telefone">Telefone:</label>
					<input type="number" id="Telefone" name="Telefone"><br>
					
					<label for="Telemovel">Telemóvel:</label>
					<input type="number" id="Telemovel" name="Telemovel"><br>
					
					<label for="Email">E-mail:</label>
					<input type="email" id="Email" name="Email"><br>
					
					<label for="Notas">Notas:</label>
					<textarea id="Notas" name="Notas" rows="6" rows="6" placeholder="Escreva aqui..."></textarea><br>
					
					<input type="submit" value="Adicionar Representante">
				</form>
			</div>
		</div>
	</div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
