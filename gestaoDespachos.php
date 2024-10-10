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
$sqlTabela = "SELECT DISTINCT d.*, o.IdOperador, o.NomeOper, ors.numOrdem
			  FROM despachos d 
				LEFT JOIN operadores o ON d.numOper = o.numOper
				LEFT JOIN ordem_servicos ors ON d.IdOrdem = ors.IdOrdem
			  WHERE 1=1 ";

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT d.IdDespacho) AS total 
              FROM despachos d 
				LEFT JOIN operadores o ON d.numOper = o.numOper
				LEFT JOIN ordem_servicos ors ON d.IdOrdem = ors.IdOrdem
              WHERE 1=1 ";

// Filtros de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            if ($coluna === 'numOper') {
                $filtro = "AND (d.numOper = '$procurar' OR o.NomeOper = '$procurar') ";
            } elseif ($coluna === 'IdOrdem') {
                $filtro = "AND (d.IdOrdem = '$procurar' OR ors.numOrdem = '$procurar') ";
            } else {
                $filtro = "AND d.$coluna = '$procurar' ";
            }
        } else {
            $filtro = "AND (d.IdDespacho = '$procurar' OR
                            d.tipoDecisao = '$procurar' OR
                            d.Descricao = '$procurar' OR
                            d.numOper = '$procurar' OR
                            o.NomeOper = '$procurar' OR
                            d.IdPedido = '$procurar' OR
                            d.IdOrdem = '$procurar' OR
                            ors.numOrdem = '$procurar')";
        }
    } else {
        if ($coluna) {
            if ($coluna === 'numOper') {
                $filtro = "AND (d.numOper LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
            } elseif ($coluna === 'IdOrdem') {
                $filtro = "AND (d.IdOrdem LIKE '%$procurar%' OR ors.numOrdem LIKE '%$procurar%') ";
            } else {
                $filtro = "AND d.$coluna LIKE '%$procurar%' ";
            }
        } else {
            $filtro = "AND (d.IdDespacho LIKE '%$procurar%' OR
                            d.tipoDecisao LIKE '%$procurar%' OR
                            d.Descricao LIKE '%$procurar%' OR
                            d.numOper LIKE '%$procurar%' OR
                            o.NomeOper LIKE '%$procurar%' OR
                            d.IdPedido LIKE '%$procurar%' OR
                            d.IdOrdem LIKE '%$procurar%' OR
                            ors.numOrdem LIKE '%$procurar%')";
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
    <title>Despachos</title>
    <script src="./js/fun_gestaoDespachos.js" defer></script>
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
        <h2>Lista de Despachos</h2>
	</div>

    <div class="containerGestao">
		<div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="IdDespacho">Id</option>
				<option value="tipoDecisao">Tipo de Decisão</option>
				<option value="Descricao">Descrição</option>
				<option value="numOper">Operador</option>
				<option value="IdPedido">Pedido</option>
				<option value="IdOrdem">Ordem de Serviço</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Novo Despacho</button>
			<table id="table">
				<thead>
					<tr>
						<th>Id</th>
						<th>Tipo de Decisão</th>
						<th>Descrição</th>
						<th>Operador</th>
						<th>Pedido</th>
						<th>Ordem de Serviço</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								echo "<td><a href='areaDespacho.php?IdDespacho=" . $row['IdDespacho'] . "'>" . $row['IdDespacho'] . "</td>";
								if (!empty($row['tipoDecisao']) && strlen($row['tipoDecisao']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Tipo de Decisao</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['tipoDecisao']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['tipoDecisao'] . "</td>";
								}
								if (!empty($row['Descricao']) && strlen($row['Descricao']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Descrição</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Descricao']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Descricao'] . "</td>";
								}
								if (!empty($row['numOper']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numOper'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";		
								echo "<td><a href='areaPedido.php?IdPedido=" . $row['IdPedido'] . "'>" . $row['IdPedido'] . "</td>";
								if (!empty($row['numOper']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOrdemServico.php?IdOrdem=" . $row['IdOrdem'] . "'>" . $row['IdOrdem'] . ") " . $row['numOrdem'] . "</td>";
								}else echo "<td></td>";	
								
								echo "<td>
										<button class='btnEditar' data-dados='" . json_encode($row) . "'>Editar</button>
										<button class='btnEliminar' IdDespacho='" . $row['IdDespacho'] . "' onclick=\"funEliminarDespacho(this)\">Eliminar</button>
									  </td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='7'>Nenhum registo encontrado</td></tr>";
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7">
							<p id="contRegisto"><?php echo "Número total de registos: " . $total_registos; ?></p>
							<div class="containerPaginacao">
								<div id="paginacao">
									<?php
									$primeira_pagina = max(1, $pagina - 15); 
									$ultima_pagina = min($total_paginas, $pagina + 15);
									
									if ($pagina > 1) {
										echo "<button class='btnPaginacao' onclick='funProcurarTable(1)'>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' onclick='funProcurarTable(" . ($pagina - 1) . ")'>&lt; Anterior</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
									}

									for ($i = $primeira_pagina; $i <= $ultima_pagina; $i++) {
										if ($i == $pagina) {
											echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
										} else {
											echo "<button class='btnPaginacao' onclick='funProcurarTable($i)'>$i</button>";
										}
									}

									if ($pagina < $total_paginas) {
										echo "<button class='btnPaginacao' onclick='funProcurarTable(" . ($pagina + 1) . ")'>Próxima &gt;</button>";
										echo "<button class='btnPaginacao' onclick='funProcurarTable($total_paginas)'>Última &raquo;</button>";
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
				<h2>Editar Despacho</h2>
				<form id="formEditarDespacho" action="db/editarSGO/db_editarDespachos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoDespachos.php" readonly>
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
		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Despacho</h2>
				<form action="db/adicionarSGO/db_adicionarDespachos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoDespachos.php" readonly>
					
					<label for="tipoDecisao">Tipo de Decisão:</label>
					<select id="tipoDecisao" name="tipoDecisao">
						<option value="Informação">Informação</option>
						<option value="Despacho">Despacho</option>
						<option value="Outros">Outros</option>
					</select><br>
					
					<label for="Descricao">Descrição:</label>
					<textarea id="Descricao" name="Descricao" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="numOper">Número de Operador:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numOper" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numOper', 'listDropdown_numOper', 'numOper')" required>
						<input type="hidden" id="numOper" name="numOper" readonly>
						<div id="listDropdown_numOper" class="listDropdown"></div>
					</div><br>
					
					<label for="IdPedido">Pedido:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdPedido" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdPedido', 'listDropdown_IdPedido', 'IdPedido')" required>
						<input type="hidden" id="IdPedido" name="IdPedido" readonly>
						<div id="listDropdown_IdPedido" class="listDropdown"></div>
					</div><br>
					
					<label for="IdOrdem">Ordem de Serviço:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdOrdem" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdOrdem', 'listDropdown_IdOrdem', 'IdOrdem')" required>
						<input type="hidden" id="IdOrdem" name="IdOrdem" readonly>
						<div id="listDropdown_IdOrdem" class="listDropdown"></div>
					</div><br>

					<input type="submit" value="Adicionar Despacho">
				</form>
			</div>
		</div>
	</div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
