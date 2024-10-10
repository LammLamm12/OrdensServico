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
$sqlTabela = "SELECT DISTINCT v.*, o.IdOperador, o.NomeOper 
			  FROM viaturas v 
				LEFT JOIN operadores o ON v.numResp = o.numOper		
			  WHERE 1=1 ";

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT v.IdViatura) AS total 
			  FROM viaturas v 
				LEFT JOIN operadores o ON v.numResp = o.numOper		
			  WHERE 1=1 ";

// Query de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            if ($coluna === 'IdViatura') {
                $filtro .= "AND (v.IdViatura = '$procurar' OR v.Matricula = '$procurar') ";
            } elseif ($coluna === 'numResp') {
                $filtro .= "AND (v.numResp = '$procurar' OR o.NomeOper = '$procurar') ";
            } else {
                $filtro .= "AND v.$coluna = '$procurar' ";
            }
        } else {
            $filtro .= "AND (v.IdViatura = '$procurar' OR
							 v.Matricula = '$procurar' OR
							 v.Marca = '$procurar' OR
							 v.Modelo = '$procurar' OR
							 v.CC = '$procurar' OR
							 v.PB = '$procurar' OR
							 v.dataMatricula = '$procurar' OR
							 v.Lugares = '$procurar' OR
							 v.Categoria = '$procurar' OR
							 v.Afetacao = '$procurar' OR
							 v.numResp = '$procurar' OR
							 o.NomeOper = '$procurar' OR
							 v.Livrete = '$procurar' OR
							 v.Seguro = '$procurar' OR
							 v.dataInspecao = '$procurar' OR
							 v.Notas = '$procurar')";
        }
    } else {
		if ($coluna) {
			if ($coluna === 'IdViatura') {
				$filtro .= "AND (v.IdViatura LIKE '%$procurar%' OR v.Matricula LIKE '%$procurar%') ";
			} elseif ($coluna === 'numResp') {
				$filtro .= "AND (v.numResp LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
			} else {
				$filtro .= "AND v.$coluna LIKE '%$procurar%' ";
			}
		} else {
			$filtro .= "AND (v.IdViatura LIKE '%$procurar%' OR
							 v.Matricula LIKE '%$procurar%' OR
							 v.Marca LIKE '%$procurar%' OR
							 v.Modelo LIKE '%$procurar%' OR
							 v.CC LIKE '%$procurar%' OR
							 v.PB LIKE '%$procurar%' OR
							 v.dataMatricula LIKE '%$procurar%' OR
							 v.Lugares LIKE '%$procurar%' OR
							 v.Categoria LIKE '%$procurar%' OR
							 v.Afetacao LIKE '%$procurar%' OR
							 v.numResp LIKE '%$procurar%' OR
							 o.NomeOper LIKE '%$procurar%' OR
							 v.Livrete LIKE '%$procurar%' OR
							 v.Seguro LIKE '%$procurar%' OR
							 v.dataInspecao LIKE '%$procurar%' OR
							 v.Notas LIKE '%$procurar%')";
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
    <title>Viaturas</title>
    <script src="./js/fun_gestaoViaturas.js" defer></script>
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
        <h2>Lista de Viaturas</h2>
	</div>

    <div class="containerGestao">
		<div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="Matricula">Matrícula</option>
				<option value="Marca">Marca</option>
				<option value="Modelo">Modelo</option>
				<option value="CC">Cilindrada</option>
				<option value="PB">Peso Bruto</option>
				<option value="dataMatricula">Data de Matrícula</option>
				<option value="Lugares">Lugares</option>
				<option value="Categoria">Categoriao</option>
				<option value="Afetacao">Afectação</option>
				<option value="numResp">Responsável</option>
				<option value="Livrete">Livrete</option>
				<option value="Seguro">Seguro</option>
				<option value="dataInspecao">Data de Inspeção</option>
				<option value="Notas">Notas</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Nova Viatura</button>
			<table id="table">
				<thead>
					<tr>
						<th>Matrícula</th>
						<th>Marca</th>
						<th>Modelo</th>
						<th>Cilindrada</th>
						<th>Peso Bruto</th>
						<th>Data de Matrícula</th>
						<th>Lugares</th>
						<th>Categoria</th>
						<th>Afectação</th>
						<th>Responsável</th>
						<th>Livrete</th>
						<th>Seguro</th>
						<th>Data de Inspeção</th>
						<th>Notas</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								if (!empty($row['IdViatura']) && !empty($row['Matricula'])) {
									echo "<td><a href='areaViatura.php?IdViatura=" . $row['IdViatura'] . "'>" . $row['IdViatura'] . ") " . $row['Matricula'] . "</a></td>";
								} else {
									echo "<td></td>";        
								}	
								echo "<td>" . $row['Marca'] . "</td>";
								echo "<td>" . $row['Modelo'] . "</td>";
								echo "<td>" . $row['CC'] . "</td>";
								echo "<td>" . $row['PB'] . "</td>";
								echo "<td>" . $row['dataMatricula'] . "</td>";
								echo "<td>" . $row['Lugares'] . "</td>";
								echo "<td>" . $row['Categoria'] . "</td>";
								echo "<td>" . $row['Afetacao'] . "</td>";
								if (!empty($row['numResp']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numResp'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";					
								echo "<td>" . $row['Livrete'] . "</td>";
								echo "<td>" . $row['Seguro'] . "</td>";
								echo "<td>" . $row['dataInspecao'] . "</td>";
								// Mostrar botão se não é nulo e tem mais de 30 caracteres
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
										<button class='btnEliminar' IdViatura='" . $row['IdViatura'] . "' onclick=\"funEliminarViatura(this)\">Eliminar</button>
									  </td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='15'>Nenhum registo encontrado</td></tr>";
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="15">
							<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos; ?></p>
							<div class="containerPaginacao">
								<div id="paginacao">
									<?php
									$primeira_pagina = max(1, $pagina - 15); 
									$ultima_pagina = min($total_paginas, $pagina + 15);

									
									if ($pagina > 1) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoViaturas.php?pagina=1'\">&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoViaturas.php?pagina=" . ($pagina - 1) . "'\">&lt; Anterior</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
									}

									
									for ($i = $primeira_pagina; $i <= $ultima_pagina; $i++) {
										if ($i == $pagina) {
											echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
										} else {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoViaturas.php?pagina=$i'\">$i</button>";
										}
									}

									
									if ($pagina < $total_paginas) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoViaturas.php?pagina=" . ($pagina + 1) . "'\">Próxima &gt;</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoViaturas.php?pagina=$total_paginas'\">Última &raquo;</button>";
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
				<h2>Editar Viatura</h2>
				<form id="formEditar" action="db/editarSGO/db_editarViaturas.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoViaturas.php" readonly>
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
					<textarea id="editNotas" name="editNotas" rows="6" placeholder="Escreva aqui..."></textarea>

					<input type="submit" value="Atualizar Viatura">
				</form>
			</div>
		</div>
		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Viatura</h2>
				<form action="db/adicionarSGO/db_adicionarViaturas.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoViaturas.php" readonly>

					<label for="Matricula">Matrícula:</label>
					<input type="text" id="Matricula" name="Matricula" placeholder="Necessário" required><br>

					<label for="Marca">Marca:</label>
					<input type="text" id="Marca" name="Marca"><br>

					<label for="Modelo">Modelo:</label>
					<input type="text" id="Modelo" name="Modelo"><br>

					<label for="CC">Cilindrada:</label>
					<input type="number" id="CC" name="CC" placeholder="Cc"><br>

					<label for="PB">Peso Bruto:</label>
					<input type="number" id="PB" name="PB" placeholder="kg"><br>

					<label for="dataMatricula">Data de Matrícula:</label>
					<input type="date" id="dataMatricula" name="dataMatricula"><br>

					<label for="Lugares">Lugares:</label>
					<input type="number" id="Lugares" name="Lugares"><br>

					<label for="Categoria">Categoria:</label>
					<input type="text" id="Categoria" name="Categoria"><br>

					<label for="Afetacao">Afetação:</label>
					<input type="text" id="Afetacao" name="Afetacao"><br>

					<label for="numResp">Responsável:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numResp', 'listDropdown_numResp', 'numResp')">
						<input type="hidden" id="numResp" name="numResp" readonly>
						<div id="listDropdown_numResp" class="listDropdown"></div>
					</div><br>

					<label for="Livrete">Livrete:</label>
					<input type="text" id="Livrete" name="Livrete"><br>

					<label for="Seguro">Seguro:</label>
					<input type="text" id="Seguro" name="Seguro"><br>

					<label for="dataInspecao">Data de Inspeção:</label>
					<input type="date" id="dataInspecao" name="dataInspecao"><br>

					<label for="Notas">Notas:</label>
					<textarea id="Notas" name="Notas" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<input type="submit" value="Adicionar Viatura">
				</form>
			</div>
		</div>
		
	</div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
