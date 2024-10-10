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
$sqlTabela = "SELECT DISTINCT req.*, rep.IdRepresentante, rep.NomeRep 
			  FROM requerentes req
				LEFT JOIN representantes rep ON req.IdRepresentante = rep.IdRepresentante
			  WHERE 1=1 ";
				
// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT req.IdRequerente) AS total 
			  FROM requerentes req
				LEFT JOIN representantes rep ON req.IdRepresentante = rep.IdRepresentante
			  WHERE 1=1 ";

// Query de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;


if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
			if ($coluna === 'NomeRep') {
				$filtro .= "AND (rep.IdRepresentante = '$procurar' OR rep.NomeRep = '$procurar') ";
            } else {
				$filtro .= "AND req.$coluna = '$procurar' ";
			}
		} else {
            $filtro .= "AND (req.NomeReq = '$procurar' OR
							 rep.IdRepresentante = '$procurar' OR
							 rep.NomeRep = '$procurar' OR
							 req.Entidade = '$procurar' OR
							 req.NIF = '$procurar' OR
							 req.tipoEntidade = '$procurar' OR
							 req.Morada = '$procurar' OR
							 req.Local = '$procurar' OR
							 req.CPostal = '$procurar' OR
							 req.Telemovel = '$procurar' OR
							 req.Email = '$procurar' OR
							 req.Notas = '$procurar')";
        }
    } else {
        if ($coluna) {
			if ($coluna === 'NomeRep') {
                $filtro .= "AND (rep.IdRepresentante LIKE '%$procurar%' OR rep.NomeRep LIKE '%$procurar%') ";
            } else {
				$filtro .= "AND req.$coluna LIKE '%$procurar%' ";
			}
		} else {
            $filtro .= "AND (req.NomeReq = '$procurar' OR
							 rep.IdRepresentante = '$procurar' OR
							 rep.NomeRep = '$procurar' OR
							 req.Entidade = '$procurar' OR
							 req.NIF = '$procurar' OR
							 req.tipoEntidade = '$procurar' OR
							 req.Morada = '$procurar' OR
							 req.Local = '$procurar' OR
							 req.CPostal = '$procurar' OR
							 req.Telemovel = '$procurar' OR
							 req.Email = '$procurar' OR
							 req.Notas = '$procurar')";
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
    <title>Requerentes</title>
	<script src="./js/fun_gestaoRequerentes.js" defer></script>
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
        <h2>Lista de Requerentes</h2>
	</div>

    <div class="containerGestao">
        <div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="NomeReq">Nome</option>
				<option value="NomeRep">Representante</option>
				<option value="Entidade">Entidade</option>
				<option value="tipoEntidade">Tipo de Entidade</option>
				<option value="NIF">NIF</option>
				<option value="CAE">CAE</option>
				<option value="Local">Local</option>
				<option value="Morada">Morada</option>
				<option value="CPostal">Código Postal</option>
				<option value="Telemovel">Telemóvel</option>
				<option value="Telefone">Telefone</option>
				<option value="Email">E-mail</option>
				<option value="Notas">Notas</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Novo Requerente</button>
            <table id="table">
				<thead>
					<tr>
						<th>Nome</th>
						<th>Representante</th>
						<th>Entidade</th>
						<th>Tipo de Entidade</th>
						<th>NIF</th>
						<th>CAE</th>
						<th>Local</th>
						<th>Morada</th>
						<th>Código Postal</th>
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
							echo "<td><a href='areaRequerente.php?IdRequerente=" . $row['IdRequerente'] . "'>" . $row['NomeReq'] . "</td>";
							if (!empty($row['IdRepresentante']) && !empty($row['NomeRep'])) {
									echo "<td><a href='areaRepresentante.php?IdRepresentante=" . $row['IdRepresentante'] . "'>" . $row['IdRepresentante'] . ") " . $row['NomeRep'] . "</td>";
							}else echo "<td></td>";			
							echo "<td>" . $row['Entidade'] . "</td>";
							echo "<td>" . $row['tipoEntidade'] . "</td>";
							echo "<td>" . $row['NIF'] . "</td>";
							echo "<td>" . $row['CAE'] . "</td>";
							echo "<td>" . $row['Local'] . "</td>";
							echo "<td>" . $row['Morada'] . "</td>";
							echo "<td>" . $row['CPostal'] . "</td>";
							echo "<td>" . $row['Telemovel'] . "</td>";
							echo "<td>" . $row['Telefone'] . "</td>";
							echo "<td>" . $row['Email'] . "</td>";
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
									<button class='btnEliminar' IdRequerente='" . $row['IdRequerente'] . "' onclick=\"funEliminarRequerente(this)\">Eliminar</button>
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
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRequerentes.php?pagina=1'\">&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRequerentes.php?pagina=" . ($pagina - 1) . "'\">&lt; Anterior</button>";
									} else {
										echo "<button class='btnPaginacao' disabled>&laquo; Primeira</button>";
										echo "<button class='btnPaginacao' disabled>&lt; Anterior</button>";
									}

									for ($i = $primeira_pagina; $i <= $ultima_pagina; $i++) {
										if ($i == $pagina) {
											echo "<button class='btnPaginacao' disabled><strong>$i</strong></button>";
										} else {
											echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRequerentes.php?pagina=$i'\">$i</button>";
										}
									}

									if ($pagina < $total_paginas) {
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRequerentes.php?pagina=" . ($pagina + 1) . "'\">Próxima &gt;</button>";
										echo "<button class='btnPaginacao' onclick=\"window.location.href='gestaoRequerentes.php?pagina=$total_paginas'\">Última &raquo;</button>";
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

		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Requerente</h2>
				<form action="db/adicionarSGO/db_adicionarRequerentes.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoRequerentes.php" readonly>
					
					<label for="NomeReq">Nome de Requerente:</label>
					<input type="text" id="NomeReq" name="NomeReq" placeholder="Necessário" required><br>

					<label for="IdRepresentante">Representante:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdRepresentante" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdRepresentante', 'listDropdown_IdRepresentante', 'IdRepresentante')" required>
						<input type="hidden" id="IdRepresentante" name="IdRepresentante">
						<div id="listDropdown_IdRepresentante" class="listDropdown"></div>
					</div><br>
					
					<label for="Entidade">Entidade:</label>
					<input type="text" id="Entidade" name="Entidade"><br>
					
					<label for="tipoPedido">Tipo de Pedido:</label>
					<select id="tipoPedido" name="tipoPedido" name="tipoPedido" placeholder="Necessário" required><br>
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>
					
					<label for="NIF">NIF:</label>
					<input type="number" id="NIF" name="NIF"  placeholder="Necessário" required><br>
					
					<label for="CAE">CAE:</label>
					<input type="text" id="CAE" name="CAE"><br>
		
					<label for="Local">Local:</label>
					<input type="text" id="Local" name="Local"><br>
					
					<label for="Morada">Morada:</label>
					<input type="text" id="Morada" name="Morada"><br>

					<label for="CPostal">Código Postal:</label>
					<input type="number" id="CPostal" name="CPostal"><br>
					
					<label for="Telefone">Telefone:</label>
					<input type="number" id="Telefone" name="Telefone"><br>
					
					<label for="Telemovel">Telemóvel:</label>
					<input type="number" id="Telemovel" name="Telemovel"><br>
					
					<label for="Email">E-mail:</label>
					<input type="email" id="Email" name="Email"><br>
					
					<label for="Notas">Notas:</label>
					<textarea id="Notas" name="Notas" rows="6" rows="6" placeholder="Escreva aqui..."></textarea><br>
					
					<input type="submit" value="Adicionar Requerente">
				</form>
			</div>
		</div>
	</div>
	

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
