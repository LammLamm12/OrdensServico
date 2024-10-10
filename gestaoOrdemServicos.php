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
$sqlTabela = "SELECT DISTINCT ors.*, req.NomeReq, o.NomeOper, s.NomeServ, rep.IdRepresentante
			  FROM ordem_servicos ors
				LEFT JOIN servicos s ON ors.IdServico = s.IdServico
				LEFT JOIN requerentes req ON ors.IdRequerente = req.IdRequerente
				LEFT JOIN pedidos p ON ors.IdPedido = p.IdPedido
				LEFT JOIN operadores o ON ors.numResp = o.numOper
				LEFT JOIN representantes rep ON ors.numResp = rep.IdRepresentante
			  WHERE 1=1 ";
				
				

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT ors.IdOrdem) AS total 
			  FROM ordem_servicos ors
				LEFT JOIN servicos s ON ors.IdServico = s.IdServico
				LEFT JOIN requerentes req ON ors.IdRequerente = req.IdRequerente
				LEFT JOIN pedidos p ON ors.IdPedido = p.IdPedido
				LEFT JOIN operadores o ON ors.numResp = o.numOper
				LEFT JOIN representantes rep ON ors.numResp = rep.IdRepresentante
              WHERE 1=1 ";


// Query de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            $filtro .= "AND ors.$coluna = '$procurar' ";
        } else {
            $filtro .= "AND (ors.IdOrdem = '$procurar' OR
                             ors.numOrdem = '$procurar' OR
                             ors.IdPedido = '$procurar' OR
                             ors.tipoPedido = '$procurar' OR
                             ors.IdRequerente = '$procurar' OR
                             ors.numResp = '$procurar' OR
                             ors.dataRegisto = '$procurar' OR
                             ors.Descritivo = '$procurar' OR
                             ors.estadoPedido = '$procurar' OR
                             ors.LocalDestino = '$procurar' OR
                             ors.IdServico = '$procurar' OR
                             ors.Despacho = '$procurar' OR
                             ors.dataDespacho = '$procurar' OR
                             ors.dataIni = '$procurar' OR
                             ors.dataFim = '$procurar' OR
                             ors.kmsIda = '$procurar' OR
                             ors.kmsVolta = '$procurar' OR
                             ors.kmsTotal = '$procurar')";
        }
    } else {
        if ($coluna) {
            $filtro .= "AND ors.$coluna LIKE '%$procurar%' ";
        } else {
            $filtro .= "AND (ors.IdOrdem LIKE '%$procurar%' OR
							 ors.numOrdem LIKE '%$procurar%' OR
							 ors.IdPedido LIKE '%$procurar%' OR
							 ors.tipoPedido LIKE '%$procurar%' OR
							 ors.IdRequerente LIKE '%$procurar%' OR
							 ors.numResp LIKE '%$procurar%' OR
							 ors.dataRegisto LIKE '%$procurar%' OR
							 ors.Descritivo LIKE '%$procurar%' OR
							 ors.estadoPedido LIKE '%$procurar%' OR
							 ors.LocalDestino LIKE '%$procurar%' OR
							 ors.IdServico LIKE '%$procurar%' OR
							 ors.Despacho LIKE '%$procurar%' OR
							 ors.dataDespacho LIKE '%$procurar%' OR
							 ors.dataIni LIKE '%$procurar%' OR
							 ors.dataFim LIKE '%$procurar%' OR
							 ors.kmsIda LIKE '%$procurar%' OR
							 ors.kmsVolta LIKE '%$procurar%' OR
                             ors.kmsTotal LIKE '%$procurar%')";
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
    <title>Ordens de Serviço</title>
    <script src="./js/fun_gestaoOrdemServicos.js" defer></script>
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
        <h2>Lista de Ordens</h2>
    </div>

    <div class="containerGestao">
        <div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="IdOrdem">Id</option>
				<option value="numOrdem">Número</option>
				<option value="IdPedido">Id do Pedido</option>
				<option value="tipoPedido">Tipo do Pedido</option>
				<option value="IdRequerente">Requerente</option>
				<option value="numResp">Responsável</option>
				<option value="dataRegisto">Data de Registo</option>
				<option value="Descritivo">Descritivo</option>
				<option value="estadoPedido">Estado do Pedido</option>
				<option value="LocalDestino">Local de Destino</option>
				<option value="IdServico">Serviço de Destino</option>
				<option value="Despacho">Despacho</option>
				<option value="dataDespacho">Data de Despacho</option>
				<option value="dataIni">Data de Início</option>
				<option value="dataFim">Data de Fim</option>
				<option value="kmsIda">Kms de Ida</option>
				<option value="kmsVolta">Kms de Volta</option>
				<option value="kmsTotal">Total de Kms</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Nova Ordem de Serviço</button>
            <table id="table">
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
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
					<?php
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								echo "<td><a href='areaOrdemServico.php?IdOrdem=" . $row['IdOrdem'] . "'>" . $row['IdOrdem'] . "</td>";
								echo "<td>" . $row['numOrdem'] . "</td>";
								echo "<td><a href='areaPedido.php?IdPedido=" . $row['IdPedido'] . "'>" . $row['IdPedido'] . "</td>";
								echo "<td>" . $row['tipoPedido'] . "</td>";
								if (!empty($row['IdRequerente']) && !empty($row['NomeReq'])) {
									echo "<td><a href='areaRequerente.php?IdRequerente=" . $row['IdRequerente'] . "'>" . $row['IdRequerente'] . ") " . $row['NomeReq'] . "</td>";
								}else echo "<td></td>";			
								if (!empty($row['numResp']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaRepresentante.php?IdRepresentante=" . $row['IdRepresentante'] . "'>" . $row['numResp'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";			
								echo "<td>" . $row['dataRegisto'] . "</td>";
								if (!empty($row['Descritivo']) && strlen($row['Descritivo']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Descritivo</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Descritivo']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Descritivo'] . "</td>";
								}
								echo "<td>" . $row['estadoPedido'] . "</td>";
								echo "<td>" . $row['LocalDestino'] . "</td>";
								echo "<td><a href='areaOrdemServico.php?IdServico=" . $row['IdServico'] . "'>" . $row['NomeServ'] . "</td>";
								if (!empty($row['Despacho']) && strlen($row['Despacho']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Despacho</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Despacho']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Despacho'] . "</td>";
								}
								echo "<td>" . $row['dataDespacho'] . "</td>";
								echo "<td>" . $row['dataIni'] . "</td>";
								echo "<td>" . $row['dataFim'] . "</td>";
								echo "<td>" . $row['kmsIda'] . "</td>";
								echo "<td>" . $row['kmsVolta'] . "</td>";
								echo "<td>" . $row['kmsTotal'] . "</td>";

								echo "<td>
										<button class='btnEditar' data-dados='" . json_encode($row) . "'>Editar</button>
										<button class='btnEliminar' IdOrdem='" . $row['IdOrdem'] . "' onclick=\"funEliminarOrdem(this)\">Eliminar</button>
									  </td>";
							echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='20'>Nenhum registo encontrado</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
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
				<h2>Editar Ordem de Serviço</h2>
				<form id="formEditar" action="db/editarSGO/db_editarOrdemServicos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoOrdemServicos.php" readonly>
					<input type="hidden" id="editIdOrdem" name="editIdOrdem" readonly>
					
					<label for="editNumOrdem ">Número:</label>
					<input type="text" id="editNumOrdem" name="editNumOrdem" readonly><br>
					
					<label for="editIdPedido">Id do Pedido:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdPedido" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdPedido', 'editListDropdown_IdPedido', 'editIdPedido')" required>
						<input type="hidden" id="editIdPedido" name="editIdPedido">
						<div id="editListDropdown_IdPedido" class="listDropdown"></div>
					</div><br>

					<label for="editTipoPedido">Tipo do Pedido:</label>
					<select id="editTipoPedido" name="editTipoPedido" required><br>
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
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

					<label for="editDataRegisto">Data de Registo:</label>
					<input type="date" id="editDataRegisto" name="editDataRegisto"><br>
					
					<label for="editDescritivo">Descritivo:</label>
					<textarea type="number" id="editDescritivo" name="editDescritivo" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editEstadoPedido">Estado do Pedido:</label>
					<select id="editEstadoPedido" name="editEstadoPedido" placeholder="Necessário" required><br>
						<option value="Pendente">Pendente</option>
						<option value="Cancelado">Cancelado</option>
						<option value="Não Autorizado">Não Autorizado</option>
						<option value="Autorizado">Autorizado</option>
						<option value="Rejeitado">Rejeitado</option>
						<option value="Concluído">Concluído</option>
					</select><br>
					
					<label for="editLocalDestino">Local de Destino:</label>
					<input type="text" id="editLocalDestino" name="editLocalDestino"><br>
					
					<label for="editIdServico">Serviço de Destino:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdServico', 'editListDropdown_IdServico', 'editIdServico')" required>
						<input type="hidden" id="editIdServico" name="editIdServico">
						<div id="editListDropdown_IdServico" class="listDropdown"></div>
					</div><br>

					<label for="editDespacho">Despacho:</label>
					<textarea type="number" id="editDespacho" name="editDespacho" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editDataDespacho">Data de Despacho:</label>
					<input type="date" id="editDataDespacho" name="editDataDespacho"><br>

					<label for="editDataIni">Data de Início:</label>
					<input type="date" id="editDataIni" name="editDataIni"><br>

					<label for="editDataFim">Data de Fim:</label>
					<input type="date" id="editDataFim" name="editDataFim"><br>

					<label for="editKmsIda">Kms de Ida:</label>
					<input type="number" id="editKmsIda" name="editKmsIda"  oninput="calcularEditKms()" placeholder="kms/h"><br>

					<label for="editKmsVolta">Kms de Volta:</label>
					<input type="number" id="editKmsVolta" name="editKmsVolta"  oninput="calcularEditKms()" placeholder="kms/h"><br>
					
					<label for="editKmsTotal">Total de Kms</label>
					<input type="number" id="editKmsTotal" name ="editKmsTotal" placeholder="Ida + Volta" readonly><br>
					
					<input type="submit" value="Atualizar Ordem">
				</form>
			</div>
		</div>

		
		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Ordem de Serviço</h2>
				<form action="db/adicionarSGO/db_adicionarOrdemServicos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoOrdemServicos.php" readonly>

					<label for="numOrdem">Número:</label>
					<input type="text" id="numOrdem" name="numOrdem" placeholder="Necessário" required><br>

					<label for="IdPedido">Id do Pedido:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdPedido" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdPedido', 'listDropdown_IdPedido', 'IdPedido')" required>
						<input type="hidden" id="IdPedido" name="IdPedido">
						<div id="listDropdown_IdPedido" class="listDropdown"></div>
					</div><br>

					<label for="tipoPedido">Tipo do Pedido:</label>
					<select id="tipoPedido" name="tipoPedido" required>
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>

					<label for="IdRequerente">Requerente:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdRequerente" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdRequerente', 'listDropdown_IdRequerente', 'IdRequerente')" required>
						<input type="hidden" id="IdRequerente" name="IdRequerente">
						<div id="listDropdown_IdRequerente" class="listDropdown"></div>
					</div><br>

					<label for="numResp">Responsável:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numResp', 'listDropdown_numResp', 'numResp')" required>
						<input type="hidden" id="numResp" name="numResp">
						<div id="listDropdown_numResp" class="listDropdown"></div>
					</div><br>

					<label for="dataRegisto">Data de Registo:</label>
					<input type="date" id="dataRegisto" name="dataRegisto"><br>

					<label for="Descritivo">Descritivo:</label>
					<textarea id="Descritivo" name="Descritivo" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="estadoPedido">Estado do Pedido:</label>
					<select id="estadoPedido" name="estadoPedido" required>
						<option value="Pendente">Pendente</option>
						<option value="Cancelado">Cancelado</option>
						<option value="Não Autorizado">Não Autorizado</option>
						<option value="Autorizado">Autorizado</option>
						<option value="Rejeitado">Rejeitado</option>
						<option value="Concluído">Concluído</option>
					</select><br>

					<label for="LocalDestino">Local de Destino:</label>
					<input type="text" id="LocalDestino" name="LocalDestino"><br>

					<label for="IdServico">Serviço de Destino:</label>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdServico', 'listDropdown_IdServico', 'IdServico')" required>
						<input type="hidden" id="IdServico" name="IdServico">
						<div id="listDropdown_IdServico" class="listDropdown"></div>
					</div><br>

					<label for="Despacho">Despacho:</label>
					<textarea id="Despacho" name="Despacho" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="dataDespacho">Data de Despacho:</label>
					<input type="date" id="dataDespacho" name="dataDespacho"><br>

					<label for="dataIni">Data de Início:</label>
					<input type="date" id="dataIni" name="dataIni"><br>

					<label for="dataFim">Data de Fim:</label>
					<input type="date" id="dataFim" name="dataFim"><br>

					<label for="kmsIda">Kms de Ida:</label>
					<input type="number" id="kmsIda" name="kmsIda" oninput="calcularKms()" placeholder="kms/h"><br>

					<label for="kmsVolta">Kms de Volta:</label>
					<input type="number" id="kmsVolta" name="kmsVolta" oninput="calcularKms()" placeholder="kms/h"><br>

					<label for="kmsTotal">Total de Kms:</label>
					<input type="number" id="kmsTotal" name="kmsTotal" placeholder="Ida + Volta" readonly><br>

					<input type="submit" value="Adicionar Ordem de Serviço">
				</form>
			</div>
		</div>
	</div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
