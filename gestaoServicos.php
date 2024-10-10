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
$sqlTabela = "SELECT DISTINCT s.*, o.IdOperador, o.numOper, o.NomeOper
			  FROM servicos s
				LEFT JOIN operadores o ON s.numResp = o.numOper
			  WHERE 1=1 ";

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT s.IdServico) AS total 
			  FROM servicos s
				LEFT JOIN operadores o ON s.numResp = o.numOper
			  WHERE 1=1 ";

// Query de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            if ($coluna === 'numResp') {
                $filtro .= "AND (s.numResp = '$procurar' OR o.NomeOper = '$procurar') ";
            } else {
                $filtro .= "AND s.$coluna = '$procurar' ";
            } 
		}else {
			$filtro .= "AND (s.ServSigla = '$procurar' OR
							 s.NomeServ = '$procurar' OR
							 s.numResp = '$procurar' OR
							 s.titDecisor = '$procurar' OR
							 s.NomeDecisor = '$procurar' OR
							 s.Categoria = '$procurar')";
		}
	} else {
		if ($coluna) {
			if ($coluna === 'numResp') {
					$filtro .= "AND (s.numResp LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
				} else {
					$filtro .= "AND s.$coluna LIKE '%$procurar%' ";
				}
			} else {
				$filtro .= "AND (s.ServSigla LIKE '%$procurar%' OR
								 s.NomeServ LIKE '%$procurar%' OR
								 s.numResp LIKE '%$procurar%' OR
								 s.titDecisor LIKE '%$procurar%' OR
								 s.NomeDecisor LIKE '%$procurar%' OR
								 s.Categoria LIKE '%$procurar%')";
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
    <title>Serviços</title>
	<script src="./js/fun_gestaoServicos.js" defer></script>	
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
        <h2>Lista de Serviços</h2>
	</div>

    <div class="containerGestao">
        <div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="ServSigla">Sigla</option>
				<option value="NomeServ">Nome</option>
				<option value="numResp">Responsável</option>
				<option value="titDecisor">Título do Decisor</option>
				<option value="NomeDecisor">Decisor</option>
				<option value="Categoria">Categoria</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Novo Serviço</button>
            <table id="table">
				<thead>
					<tr>
						<th>Sigla</th>
						<th>Nome</th>
						<th>Responsável</th>
						<th>Título do Decisor</th>
						<th>Decisor</th>
						<th>Categoria</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								echo "<td><a href='areaServico.php?IdServico=" . $row['IdServico'] . "'>" . $row['ServSigla'] . "</td>";
								echo "<td>" . $row['NomeServ'] . "</td>";
								if (!empty($row['numResp']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numResp'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";			
								echo "<td>" . $row['titDecisor'] . "</td>";
								echo "<td>" . $row['NomeDecisor'] . "</td>";
								echo "<td>" . $row['Categoria'] . "</td>";
								
								echo "<td>
										<button class='btnEditar' data-dados='" . json_encode($row) . "'>Editar</button>
										<button class='btnEliminar' IdServico='" . $row['IdServico'] . "' onclick=\"funEliminarServico(this)\">Eliminar</button>
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
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoServicos.php?pagina=1'\">&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoServicos.php?pagina=" . ($pagina - 1) . "'\">&lt; Anterior</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
									}
									
									for ($i = $primeira_pagina; $i <= $ultima_pagina; $i++) {
										if ($i == $pagina) {
											echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
										} else {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoServicos.php?pagina=$i'\">$i</button>";
										}
									}

									if ($pagina < $total_paginas) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoServicos.php?pagina=" . ($pagina + 1) . "'\">Próxima &gt;</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoServicos.php?pagina=$total_paginas'\">Última &raquo;</button>";
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

		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Serviço</h2>
				<form action="db/adicionarSGO/db_adicionarServicos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoServicos.php" readonly>
					
					<label for="ServSigla">Sigla:</label>
					<input type="text" id="ServSigla" name="ServSigla"><br>
					
					<label for="NomeServ">Nome:</label>
					<input type="text" id="NomeServ" placeholder="Necessário" name="NomeServ" required><br>
					
					<label for="numResp">Responsável:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numResp', 'listDropdown_numResp', 'numResp')"  required>
						<input type="hidden" id="numResp" name="numResp" readonly> <!-- Campo oculto, envia numResp para db -->
						<div id="listDropdown_numResp" class="listDropdown"></div>
					</div><br>
					
					<label for="titDecisor">Título do Decisor:</label>
					<input type="text" id="titDecisor" name="titDecisor"><br>
					
					<label for="NomeDecisor">Decisor:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_NomeDecisor" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_NomeDecisor', 'listDropdown_NomeDecisor', 'NomeDecisor')"  required>
						<input type="hidden" id="NomeDecisor" name="NomeDecisor" readonly> <!-- Campo oculto, envia NomeDecisor para db -->
						<div id="listDropdown_NomeDecisor" class="listDropdown"></div>
					</div><br>
					
					<label for="Categoria">Categoria:</label>
					<input type="text" id="Categoria" name="Categoria"><br>
					
					<input type="submit" value="Adicionar Serviço">
				</form>
			</div>
		</div>
	</div>
	

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
