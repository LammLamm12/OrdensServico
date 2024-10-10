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
$sqlTabela = "SELECT DISTINCT o.*, 
				u.Password AS UtilizadorPassword, u.Categoria AS UtilizadorCategoria, u.Hierarquia AS UtilizadorHierarquia,
				f.Funcao AS FuncionarioFuncao, f.Categoria AS FuncionarioCategoria, f.email AS FuncionarioEmail,
				f.IdServico, s.NomeServ,
				r.Password AS ResponsavelPassword, r.Hierarquia AS ResponsavelHierarquia, r.Funcao AS ResponsavelFuncao,
				ord.Password AS OrdenantePassword, ord.Funcao AS OrdenanteFuncao, ord.Hierarquia AS OrdenanteHierarquia
              FROM operadores o
				LEFT JOIN utilizadores u ON o.numOper = u.numUtil
				LEFT JOIN funcionarios f ON o.numOper = f.numFunc
				LEFT JOIN responsaveis r ON o.numOper = r.numResp
				LEFT JOIN ordenantes ord ON o.numOper = ord.numOrd
				LEFT JOIN servicos s ON f.IdServico = s.IdServico
              WHERE 1=1";

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT o.IdOperador) AS total 
              FROM operadores o
				LEFT JOIN utilizadores u ON o.numOper = u.numUtil
				LEFT JOIN funcionarios f ON o.numOper = f.numFunc
				LEFT JOIN responsaveis r ON o.numOper = r.numResp
				LEFT JOIN ordenantes ord ON o.numOper = ord.numOrd
				LEFT JOIN servicos s ON f.IdServico = s.IdServico
              WHERE 1=1";

// Filtros de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            if ($coluna == "categoria") {
                $filtro .= " AND (u.Categoria = '$procurar' OR f.Categoria = '$procurar') ";
            } elseif ($coluna === 'hierarquia') {
                $filtro .= " AND (u.Hierarquia = '$procurar' OR r.Hierarquia = '$procurar' OR ord.Hierarquia = '$procurar') ";
            } elseif ($coluna === 'funcao') {
                $filtro .= " AND (f.Funcao = '$procurar' OR r.Funcao = '$procurar' OR ord.Funcao = '$procurar') ";
            } elseif ($coluna === 'servico') {
                $filtro .= " AND (f.IdServico = '$procurar' OR s.NomeServ = '$procurar') ";
            } else {
                $filtro .= " AND $coluna = '$procurar' ";
            }
        } else {
            $filtro .= " AND (o.numOper = '$procurar' OR
                              o.NomeOper = '$procurar' OR
                              o.tipoOper = '$procurar' OR
                              u.Categoria = '$procurar' OR
                              f.Categoria = '$procurar' OR
                              u.Hierarquia = '$procurar' OR
                              r.Hierarquia = '$procurar' OR
                              ord.Hierarquia = '$procurar' OR
                              f.Funcao = '$procurar' OR
                              r.Funcao = '$procurar' OR
                              ord.Funcao = '$procurar' OR
                              f.email = '$procurar' OR
                              f.IdServico = '$procurar' OR
                              s.NomeServ = '$procurar')";
        }
    } else {
        if ($coluna) {
            if ($coluna == "categoria") {
                $filtro .= " AND (u.Categoria LIKE '%$procurar%' OR f.Categoria LIKE '%$procurar%') ";
            } elseif ($coluna === 'hierarquia') {
                $filtro .= " AND (u.Hierarquia LIKE '%$procurar%' OR r.Hierarquia LIKE '%$procurar%' OR ord.Hierarquia LIKE '%$procurar%') ";
            } elseif ($coluna === 'funcao') {
                $filtro .= " AND (f.Funcao LIKE '%$procurar%' OR r.Funcao LIKE '%$procurar%' OR ord.Funcao LIKE '%$procurar%') ";
            } elseif ($coluna === 'servico') {
                $filtro .= " AND (f.IdServico LIKE '%$procurar%' OR s.NomeServ LIKE '%$procurar%') ";
            } else {
                $filtro .= " AND $coluna LIKE '%$procurar%' ";
            }
        } else {
            $filtro .= " AND (o.numOper LIKE '%$procurar%' OR
                              o.NomeOper LIKE '%$procurar%' OR
                              o.tipoOper LIKE '%$procurar%' OR
                              u.Categoria LIKE '%$procurar%' OR
                              f.Categoria LIKE '%$procurar%' OR
                              u.Hierarquia LIKE '%$procurar%' OR
                              r.Hierarquia LIKE '%$procurar%' OR
                              ord.Hierarquia LIKE '%$procurar%' OR
                              f.Funcao LIKE '%$procurar%' OR
                              r.Funcao LIKE '%$procurar%' OR
                              ord.Funcao LIKE '%$procurar%' OR
                              f.email LIKE '%$procurar%' OR
                              f.IdServico LIKE '%$procurar%' OR
                              s.NomeServ LIKE '%$procurar%')";
        }
    }
    $sqlTabela .= $filtro;
    $sql_total .= $filtro;
}

$sqlTabela .= " LIMIT $registos_pagina OFFSET $offset";

$result = $conn->query($sqlTabela);

$result_total = $conn->query($sql_total);
$total_registos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registos / $registos_pagina);

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
    <title>Operadores</title>
	<script src="./js/fun_gestaoOperadores.js" defer></script>
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
        <h2>Lista de Operadores</h2>
	</div>

    <div class="containerGestao">
        <div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="numOper">Número</option>
				<option value="NomeOper">Nome</option>
				<option value="tipoOper">Tipo de Operador</option>
				<option value="funcao">Função</option>
				<option value="categoria">Categoria</option>
				<option value="hierarquia">Hierarquia</option>
				<option value="email">E-mail</option>
				<option value="servico">Serviço</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Novo Operador</button>
            <table id="table">
				<thead>
					<tr>
						<th>Número</th>
						<th>Nome</th>
						<th>Tipo de Operador</th>  
						<th>Função</th>
						<th>Categoria</th>
						<th>Hierarquia</th>
						<th>E-mail</th>
						<th>Serviço</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numOper'] . "</a></td>";        
								echo "<td>" . $row['NomeOper'] . "</td>";
								echo "<td>" . $row['tipoOper'] . "</td>";
								
								$funcao = '';
								$categoria = '';
								$hierarquia = '';
								$email = '';
								$servico = '';

								if (strpos($row['tipoOper'], 'Utilizador') !== false) {
									$categoria = $row['UtilizadorCategoria'];
									$hierarquia = $row['UtilizadorHierarquia'];
								}
								if (strpos($row['tipoOper'], 'Funcionário') !== false) {
									$funcao = $row['FuncionarioFuncao'];
									$email = $row['FuncionarioEmail'];
									$servico = $row['NomeServ'];
								}
								if (strpos($row['tipoOper'], 'Responsável') !== false) {
									$funcao = $row['ResponsavelFuncao'];
									$hierarquia = $row['ResponsavelHierarquia'];
								}
								if (strpos($row['tipoOper'], 'Ordenante') !== false) {
									$funcao = $row['OrdenanteFuncao'];
									$hierarquia = $row['OrdenanteHierarquia'];
								}
								
								echo "<td>" . $funcao . "</td>";
								echo "<td>" . $categoria . "</td>";
								echo "<td>" . $hierarquia . "</td>";
								echo "<td>" . $email . "</td>";
								echo "<td><a href='areaServico.php?IdServico=" . $row['IdServico'] . "'>" . $servico . "</a></td>";

								echo "<td>
										<button class='btnEditar' data-dados='" . json_encode($row) . "'>Editar</button>
										<button class='btnEliminar' IdOperador='" . $row['IdOperador'] . "' onclick=\"funEliminarOperador(this)\">Eliminar</button>
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
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoOperadores.php?pagina=1'\">&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoOperadores.php?pagina=" . ($pagina - 1) . "'\">&lt; Anterior</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
									}
									
									for ($i = $primeira_pagina; $i <= $ultima_pagina; $i++) {
										if ($i == $pagina) {
											echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
										} else {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoOperadores.php?pagina=$i'\">$i</button>";
										}
									}

									if ($pagina < $total_paginas) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoOperadores.php?pagina=" . ($pagina + 1) . "'\">Próxima &gt;</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoOperadores.php?pagina=$total_paginas'\">Última &raquo;</button>";
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
				<h2>Editar Operador</h2>
				<form id="formEditar" action="db/editarSGO/db_editarOperadores.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoOperadores.php" readonly>
					<input type="hidden" id="editIdOperador" name="editIdOperador" readonly>

					<label for="editNumOper">Número de Operador:</label>
					<input type="number" id="editNumOper" name="editNumOper"><br>

					<label for="editNomeOper">Nome de Operador:</label>
					<input type="text" id="editNomeOper" name="editNomeOper"><br>

					<label>Tipo de Operador:</label><br>
					<div class="containerCheckbox">
						<div>
							<input type="checkbox" id="editUtilizador" name="tipoOper[]" value="Utilizador" onchange="toggleEditTipoOper()">
							<label for="editUtilizador">Utilizador</label>
						</div>
						<div>
							<input type="checkbox" id="editFuncionario" name="tipoOper[]" value="Funcionário" onchange="toggleEditTipoOper()">
							<label for="editFuncionario">Funcionário</label>
						</div>
						<div>
							<input type="checkbox" id="editResponsavel" name="tipoOper[]" value="Responsável" onchange="toggleEditTipoOper()">
							<label for="editResponsavel">Responsável</label>
						</div>
						<div>
							<input type="checkbox" id="editOrdenante" name="tipoOper[]" value="Ordenante" onchange="toggleEditTipoOper()">
							<label for="editOrdenante">Ordenante</label>
						</div>
						<input type="hidden" id="editTipoOper" name="editTipoOper"><br>
					</div>

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
						<label for="editEmail">Email:</label>
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
		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Operador</h2>
				<form action="db/adicionarSGO/db_adicionarOperadores.php" method="POST" onsubmit="return funValidarPassword()">
					<input type="hidden" name="redirectUrl" value="gestaoOperadores.php" readonly>

					<label for="numOper">Número de Operador:</label>
					<input type="number" id="numOper" name="numOper" required><br>

					<label for="NomeOper">Nome de Operador:</label>
					<input type="text" id="NomeOper" name="NomeOper" required><br>

					<label>Tipo de Operador:</label><br>
					<div class="containerCheckbox">
						<div>
							<label for="utilizador">Utilizador</label>
							<input type="checkbox" id="utilizador" name="tipoOper[]" value="Utilizador" onchange="toggleTipoOper()">
						</div>
						<div>
							<label for="funcionario">Funcionário</label>
							<input type="checkbox" id="funcionario" name="tipoOper[]" value="Funcionário" onchange="toggleTipoOper()">
						</div>
						<div>
							<label for="responsavel">Responsável</label>
							<input type="checkbox" id="responsavel" name="tipoOper[]" value="Responsável" onchange="toggleTipoOper()">
						</div>
						<div>
							<label for="ordenante">Ordenante</label>
							<input type="checkbox" id="ordenante" name="tipoOper[]" value="Ordenante" onchange="toggleTipoOper()">
						</div>
						<input type="hidden" id="tipoOper" name="tipoOper"><br>
					</div>

					<!-- Campos exclusivos para cada tipoOper -->
					<div id="fieldPassword" style="display:none;">
						<label for="Password">Password:</label>
						<input type="password" id="Password" name="Password" placeholder="Necessário" required><br>
						
						<label for="confirmPassword">Confirmar Password:</label>
						<input type="password" id="confirmPassword" name="confirmPassword" required><br>
					</div>

					<div id="fieldFuncao" style="display:none;">
						<label for="Funcao">Função:</label>
						<input type="text" id="Funcao" name="Funcao"><br>
					</div>
					
					<div id="fieldCategoria" style="display:none;">
						<label for="Categoria">Categoria:</label>
						<input type="text" id="Categoria" name="Categoria"><br>
					</div>

					<div id="fieldHierarquia" style="display:none;">
						<label for="Hierarquia">Hierarquia:</label>
						<input type="text" id="Hierarquia" name="Hierarquia"><br>
					</div>

					<div id="fieldEmail" style="display:none;">
						<label for="Email">Email:</label>
						<input type="email" id="Email" name="Email"><br>
					</div>
					
					<div id="fieldServico" style="display:none;">
						<label for="IdServico">Serviço:</label>
						<div class="dropdown">
							<input type="text" id="inputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdServico', 'listDropdown_IdServico', 'IdServico')">
							<input type="hidden" id="IdServico" name="IdServico">
							<div id="listDropdown_IdServico" class="listDropdown"></div>
						</div><br>
					</div>

					<input type="submit" value="Adicionar Operador">
				</form>
			</div>
		</div>
	</div>
	

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
