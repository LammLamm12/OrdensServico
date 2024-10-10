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
$sqlTabela = "SELECT DISTINCT p.*, o.IdOperador, o.NomeOper, ser.NomeServ, v.Matricula
			  FROM pedidos p
				LEFT JOIN operadores o ON p.numResp = o.numOper
				LEFT JOIN requerentes req ON p.IdRequerente = req.IdRequerente
				LEFT JOIN ordenantes ord ON p.numResp = ord.numOrd
				LEFT JOIN servicos ser ON p.IdServico = ser.IdServico
				LEFT JOIN funcionarios f ON p.numFunc1 = f.numFunc
				LEFT JOIN viaturas v ON p.IdViatura = v.IdViatura
			  WHERE 1=1 ";
				
				

// Query de contagem
$sql_total = "SELECT COUNT(DISTINCT p.IdPedido) AS total 
			  FROM pedidos p
				LEFT JOIN operadores o ON p.numResp = o.numOper
				LEFT JOIN requerentes req ON p.IdRequerente = req.IdRequerente
				LEFT JOIN ordenantes ord ON p.numResp = ord.numOrd
				LEFT JOIN servicos ser ON p.IdServico = ser.IdServico
				LEFT JOIN funcionarios f ON p.numFunc1 = f.numFunc
				LEFT JOIN viaturas v ON p.IdViatura = v.IdViatura
			  WHERE 1=1 ";

// Query de pesquisa
$procurar = isset($_GET['procurar']) ? $_GET['procurar'] : '';
$coluna = isset($_GET['coluna']) ? $_GET['coluna'] : '';
$palavra_inteira = isset($_GET['palavraInteira']) ? (int)$_GET['palavraInteira'] : 0;

if ($procurar) {
    $filtro = '';
    if ($palavra_inteira) {
        if ($coluna) {
            if ($coluna === 'IdRequerente') {
                $filtro .= "AND (p.IdRequerente = '$procurar' OR o.NomeOper = '$procurar') ";
            } elseif ($coluna === 'numResp') {
                $filtro .= "AND (p.numResp = '$procurar' OR o.NomeOper = '$procurar') ";
            } elseif ($coluna === 'numOrd') {
                $filtro .= "AND (p.numOrd = '$procurar' OR o.NomeOper = '$procurar') ";
            } elseif ($coluna === 'IdServico') {
                $filtro .= "AND (p.IdServico = '$procurar' OR ser.NomeServ = '$procurar') ";
            } elseif ($coluna === 'numFunc1') {
                $filtro .= "AND (p.numFunc1 = '$procurar' OR o.NomeOper = '$procurar') ";
            } elseif ($coluna === 'numFunc2') {
                $filtro .= "AND (p.numFunc2 = '$procurar' OR o.NomeOper = '$procurar') ";
            } elseif ($coluna === 'IdViatura') {
                $filtro .= "AND (p.IdViatura = '$procurar' OR v.Matricula = '$procurar') ";
            } else {
                $filtro .= "AND p.$coluna = '$procurar' ";
            }
        } else {
            $filtro .= "AND (p.IdPedido LIKE '%$procurar%' OR
                             p.tipoPedido LIKE '%$procurar%' OR
                             p.estadoPedido LIKE '%$procurar%' OR
                             p.IdRequerente LIKE '%$procurar%' OR
							 p.numResp LIKE '%$procurar%' OR
							 p.numOrd LIKE '%$procurar%' OR
							 p.IdServico LIKE '%$procurar%' OR
							 ser.NomeServ LIKE '%$procurar%' OR
							 p.Descricao LIKE '%$procurar%' OR
							 p.numFunc1 LIKE '%$procurar%' OR
							 p.numFunc2 LIKE '%$procurar%' OR
							 p.dataRegisto LIKE '%$procurar%' OR
							 p.Observacoes LIKE '%$procurar%' OR
							 p.Despacho LIKE '%$procurar%' OR
							 p.dataDespacho LIKE '%$procurar%' OR
							 p.Notas LIKE '%$procurar%' OR
							 p.HoraIni LIKE '%$procurar%' OR
							 p.DataIni LIKE '%$procurar%' OR
							 p.HoraFim LIKE '%$procurar%' OR
							 p.DataFim LIKE '%$procurar%' OR
							 p.IdViatura LIKE '%$procurar%' OR
							 p.kmsPrevistos LIKE '%$procurar%' OR
							 p.LocalPartida LIKE '%$procurar%' OR
							 p.LocalDestino LIKE '%$procurar%' OR
							 p.kmsIda LIKE '%$procurar%' OR
							 p.kmsVolta LIKE '%$procurar%' OR
							 p.kmsTotal LIKE '%$procurar%' OR
							 p.Anotacoes LIKE '%$procurar%')";
        }
    } else {
        if ($coluna) {
            if ($coluna === 'IdRequerente') {
                $filtro .= "AND (p.IdRequerente LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
            } elseif ($coluna === 'numResp') {
                $filtro .= "AND (p.numResp LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
            } elseif ($coluna === 'numOrd') {
                $filtro .= "AND (p.numOrd LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
            } elseif ($coluna === 'IdServico') {
                $filtro .= "AND (p.IdServico LIKE '%$procurar%' OR ser.NomeServ LIKE '%$procurar%') ";
            } elseif ($coluna === 'numFunc1') {
                $filtro .= "AND (p.numFunc1 LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
            } elseif ($coluna === 'numFunc2') {
                $filtro .= "AND (p.numFunc2 LIKE '%$procurar%' OR o.NomeOper LIKE '%$procurar%') ";
            } elseif ($coluna === 'IdViatura') {
                $filtro .= "AND (p.IdViatura LIKE '%$procurar%' OR v.Matricula LIKE '%$procurar%') ";
            } else {
                $filtro .= "AND p.$coluna LIKE '%$procurar%' ";
            }
        } else {
            $filtro .= "AND (p.IdPedido LIKE '%$procurar%' OR
							 p.tipoPedido LIKE '%$procurar%' OR
							 p.estadoPedido LIKE '%$procurar%' OR
							 p.IdRequerente LIKE '%$procurar%' OR
							 p.numResp LIKE '%$procurar%' OR
							 p.numOrd LIKE '%$procurar%' OR
							 p.IdServico LIKE '%$procurar%' OR
							 ser.NomeServ LIKE '%$procurar%' OR
							 p.Descricao LIKE '%$procurar%' OR
							 p.numFunc1 LIKE '%$procurar%' OR
							 p.numFunc2 LIKE '%$procurar%' OR
							 p.dataRegisto LIKE '%$procurar%' OR
							 p.Observacoes LIKE '%$procurar%' OR
							 p.Despacho LIKE '%$procurar%' OR
							 p.dataDespacho LIKE '%$procurar%' OR
							 p.Notas LIKE '%$procurar%' OR
							 p.HoraIni LIKE '%$procurar%' OR
							 p.DataIni LIKE '%$procurar%' OR
							 p.HoraFim LIKE '%$procurar%' OR
							 p.DataFim LIKE '%$procurar%' OR
							 p.IdViatura LIKE '%$procurar%' OR
							 p.kmsPrevistos LIKE '%$procurar%' OR
							 p.LocalPartida LIKE '%$procurar%' OR
							 p.LocalDestino LIKE '%$procurar%' OR
							 p.kmsIda LIKE '%$procurar%' OR
							 p.kmsVolta LIKE '%$procurar%' OR
							 p.kmsTotal LIKE '%$procurar%' OR
							 p.Anotacoes LIKE '%$procurar%')";
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
    <title>Pedidos</title>
    <script src="./js/fun_gestaoPedidos.js" defer></script>
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
        <h2>Lista de Pedidos</h2>
	</div>

    <div class="containerGestao">
		<div class="left">
			<select id="colunaProcurar">
				<option value="">Pesquisa Geral</option>
				<option value="IdPedido">Id do Pedido</option>
				<option value="tipoPedido">Tipo de Pedido</option>
				<option value="estadoPedido">Estado do Pedido</option>
				<option value="IdRequerente">Requerente</option>
				<option value="numResp">Responsável</option>
				<option value="numOrd">Ordenante</option>
				<option value="IdServico">Servico</option>
				<option value="Descricao">Descrição</option>
				<option value="numFunc1">Funcionário 1</option>
				<option value="numFunc2">Funcionário 2</option>
				<option value="dataRegisto">Data de Registo</option>
				<option value="Observacoes">Observações</option>
				<option value="Despacho">Despacho</option>
				<option value="dataDespacho">Data de Despacho</option>
				<option value="Notas">Notas</option>
				<option value="HoraIni">Hora de Início</option>
				<option value="DataIni">Data de Início</option>
				<option value="HoraFim">Hora de Fim</option>
				<option value="DataFim">Data de Fim</option>
				<option value="IdViatura">Viatura</option>
				<option value="kmsPrevistos">Kms Previstos</option>
				<option value="LocalPartida">Local de Partida</option>
				<option value="LocalDestino">Local de Destino</option>
				<option value="kmsIda">Kms de Ida</option>
				<option value="kmsVolta">Kms de Volta</option>
				<option value="kmsTotal">Kms Total</option>
				<option value="Anotacoes">Anotações</option>
			</select>
            <input id="inputProcurar" type="text" onkeydown="if(event.key === 'Enter'){ funProcurarTable(); }" placeholder="Pesquisar..." />
			<input id="procurarPalavraInteira" type="checkbox" />Procurar na Integridade
			<button id="openModalAdicionar" class="btnAdicionar">Adicionar Novo Pedido</button>
			<table id="table">
				<thead>
					<tr>
						<th>Id do Pedido</th>
						<th>Tipo de pedido</th>
						<th>Estado do Pedido</th>
						<th>Requerente</th>
						<th>Responsável</th>
						<th>Ordenante</th>
						<th>Servico</th>
						<th>Descrição</th>
						<th>Funcionário 1</th>
						<th>Funcionário 2</th>
						<th>Data de Registo</th>
						<th>Observações</th>
						<th>Despacho</th>
						<th>Data de Despacho</th>
						<th>Notas</th>
						<th>Hora de Início</th>
						<th>Data de Início</th>
						<th>Hora de Fim</th>
						<th>Data de Fim</th>
						<th>Viatura</th>
						<th>Kms Previstos</th>
						<th>Local de Partida</th>
						<th>Local de Destino</th>
						<th>Kms de Ida</th>
						<th>Kms de Volta</th>
						<th>Kms Total</th>
						<th>Anotações</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							echo "<tr dadosPagina='{$pagina}'>";
								echo "<td><a href='areaPedido.php?IdPedido=" . $row['IdPedido'] . "'>" . $row['IdPedido'] . "</td>";
								echo "<td>" . $row['tipoPedido'] . "</td>";
								echo "<td>" . $row['estadoPedido'] . "</td>";
								if (!empty($row['IdRequerente']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['IdRequerente'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";		
								if (!empty($row['numResp']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numResp'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";			
								if (!empty($row['numOrd']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numOrd'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";	
								if (!empty($row['IdServico']) && !empty($row['NomeServ'])) {
									echo "<td><a href='areaServico.php?IdServico=" . $row['IdServico'] . "'>" . $row['IdServico'] . ") " . $row['NomeServ'] . "</td>";
								}else echo "<td></td>";						
								if (!empty($row['Descricao']) && strlen($row['Descricao']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Descrição</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Descricao']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Descricao'] . "</td>";
								}
								if (!empty($row['numFunc1']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numFunc1'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";		
								if (!empty($row['numFunc2']) && !empty($row['NomeOper'])) {
									echo "<td><a href='areaOperador.php?IdOperador=" . $row['IdOperador'] . "'>" . $row['numFunc2'] . ") " . $row['NomeOper'] . "</td>";
								}else echo "<td></td>";		
								echo "<td>" . $row['dataRegisto'] . "</td>";
								if (!empty($row['Observacoes']) && strlen($row['Observacoes']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Observações</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Observacoes']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Observacoes'] . "</td>";
								}
								if (!empty($row['Despacho']) && strlen($row['Despacho']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Despacho</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Despacho']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Despacho'] . "</td>";
								}
								echo "<td>" . $row['dataDespacho'] . "</td>";
								if (!empty($row['Notas']) && strlen($row['Notas']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Notas</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Notas']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Notas'] . "</td>";
								}
								echo "<td>" . $row['HoraIni'] . "</td>";
								echo "<td>" . $row['DataIni'] . "</td>";
								echo "<td>" . $row['HoraFim'] . "</td>";
								echo "<td>" . $row['DataFim'] . "</td>";
								if (!empty($row['IdViatura']) && !empty($row['Matricula'])) {
									echo "<td><a href='areaViatura.php?IdViatura=" . $row['IdViatura'] . "'>" . $row['IdViatura'] . ") " . $row['Matricula'] . "</td>";
								}else echo "<td></td>";	
								echo "<td>" . $row['kmsPrevistos'] . "</td>";
								echo "<td>" . $row['LocalPartida'] . "</td>";
								echo "<td>" . $row['LocalDestino'] . "</td>";
								echo "<td>" . $row['kmsIda'] . "</td>";
								echo "<td>" . $row['kmsVolta'] . "</td>";
								echo "<td>" . $row['kmsTotal'] . "</td>";
								if (!empty($row['Anotacoes']) && strlen($row['Anotacoes']) > 30) {
									echo "<td>";
									echo "<button onclick=\"funtoggleNotas(this)\">Anotações</button>";
									echo "<textarea rows='3' style='display:none;' readonly>{$row['Anotacoes']}</textarea>";
									echo "</td>";
								} else {
									echo "<td>" . $row['Anotacoes'] . "</td>";
								}

								echo "<td>
										<button class='btnEditar' data-dados='" . json_encode($row) . "'>Editar</button>
										<button class='btnEliminar' IdPedido='" . $row['IdPedido'] . "' onclick=\"funEliminarPedidos(this)\">Eliminar</button>
									  </td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='29'>Nenhum registo encontrado</td></tr>";
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="29">
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
				<h2>Editar Pedido</h2>
				<form id="formEditar" action="db/editarSGO/db_editarPedidos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoPedidos.php" readonly>
					<input type="hidden" id="editIdPedido" name="editIdPedido" readonly>
					
					<label for="editTipoPedido">Tipo de Pedido:</label>
					<select id="editTipoPedido" name="editTipoPedido">
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>
					
					<label for="editEstadoPedido">Estado do Pedido:</label>
					<select id="editEstadoPedido" name="editEstadoPedido">
						<option value="Pendente">Pendente</option>
						<option value="Cancelado">Cancelado</option>
						<option value="Não Autorizado">Não Autorizado</option>
						<option value="Autorizado">Autorizado</option>
						<option value="Rejeitado">Rejeitado</option>
						<option value="Concluído">Concluído</option>
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
					
					<label for="editNumOrdem">Ordenante:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numOrdem" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numOrdem', 'editListDropdown_numOrdem', 'editNumOrdem')" required>
						<input type="hidden" id="editNumOrdem" name="editNumOrdem">
						<div id="editListDropdown_numOrdem" class="listDropdown"></div>
					</div><br>
					
					<label for="editIdServico">Serviço:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdServico', 'editListDropdown_IdServico', 'editIdServico')" required>
						<input type="hidden" id="editIdServico" name="editIdServico">
						<div id="editListDropdown_IdServico" class="listDropdown"></div>
					</div><br>

					<label for="editDescricao">Descrição:</label>
					<textarea id="editDescricao" rows="6" placeholder="Escreva aqui..."></textarea><br>
					
					<label for="editNumFunc1">Funcionário 1:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numFunc1" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numFunc1', 'editListDropdown_numFunc1', 'editNumFunc1')" required>
						<input type="hidden" id="editNumFunc1" name="editNumFunc1">
						<div id="editListDropdown_numFunc1" class="listDropdown"></div>
					</div><br>

					<label for="editNumFunc2">Funcionário 2:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_numFunc2" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_numFunc2', 'editListDropdown_numFunc2', 'editNumFunc2')">
						<input type="hidden" id="editNumFunc2" name="editNumFunc2">
						<div id="editListDropdown_numFunc2" class="listDropdown"></div>
					</div><br>

					<label for="editDataRegisto">Data de Registo:</label>
					<input type="date" id="editDataRegisto" name="editDataRegisto" readonly><br>

					<label for="editObservacoes">Observações:</label>
					<textarea id="editObservacoes" name="editObservacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editDespacho">Despacho:</label>
					<textarea id="editDespacho" name="editDespacho" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="editDataDespacho">Data de Despacho:</label>
					<input type="date" id="editDataDespacho" name="editDataDespacho"><br>

					<label for="editNotas">Notas:</label>
					<textarea id="editNotas" name="editNotas" rows="6" placeholder="Escreva aqui..."></textarea><br>
					
					<label for="editHoraIni">Hora de Início:</label>
					<input type="date" id="editHoraIni" name="editHoraIni"><br>

					<label for="editDataIni">Data de Início:</label>
					<input type="date" id="editDataIni" name="editDataIni"><br>

					<label for="editHoraFim">Hora de Fim:</label>
					<input type="date" id="editHoraFim" name="editHoraFim"><br>

					<label for="editDataFim">Data de Fim:</label>
					<input type="date" id="editDataFim" name="editDataFim"><br>
					
					<label for="editIdViatura">Viatura:</label><br>
					<div class="dropdown">
						<input type="text" id="editInputDropdown_IdViatura" placeholder="Selecione na lista" oninput="funProcurarDropdown('editInputDropdown_IdViatura', 'editListDropdown_IdViatura', 'editIdViatura')" required>
						<input type="hidden" id="editIdViatura" name="editIdViatura">
						<div id="editListDropdown_IdViatura" class="listDropdown"></div>
					</div><br>

					<label for="editKmsPrevistos">Kms Previstos:</label>
					<input type="number" id="editKmsPrevistos" name="editKmsPrevistos"><br>

					<label for="editLocalPartida">Local de Partida:</label>
					<input type="text" id="editLocalPartida" name="editLocalPartida"><br>

					<label for="editLocalDestino">Local de Destino:</label>
					<input type="text" id="editLocalDestino" name="editLocalDestino"><br>
					
					<label for="editKmsIda">Kms de Ida:</label>
					<input type="number" id="editKmsIda" name="editKmsIda"  oninput="calcularEditKms()" placeholder="kms/h"><br>

					<label for="editKmsVolta">Kms de Volta:</label>
					<input type="number" id="editKmsVolta" name="editKmsVolta"  oninput="calcularEditKms()" placeholder="kms/h"><br>
					
					<label for="editKmsTotal">Kms Total</label>
					<input type="number" id="editKmsTotal" name ="editKmsTotal" placeholder="Ida + Volta" readonly><br>

					<label for="editAnotacoes">Anotações:</label>
					<textarea id="editAnotacoes" name="editAnotacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<input type="submit" value="Atualizar Pedido">
				</form>
			</div>
		</div>

		
		<div id="modalAdicionar" class="modal">
			<div class="modalConteudo">
				<span class="close" onclick="closeModalAdicionar()">&times;</span>
				<h2>Adicionar Pedido</h2>
				<form action="db/adicionarSGO/db_adicionarPedidos.php" method="POST">
					<input type="hidden" name="redirectUrl" value="gestaoPedidos.php" readonly>

					<label for="tipoPedido">Tipo de Pedido:</label>
					<select id="tipoPedido" name="tipoPedido">
						<option value="Interno">Interno</option>
						<option value="Externo">Externo</option>
						<option value="Outros">Outros</option>
					</select><br>

					<label for="estadoPedido">Estado do Pedido:</label>
					<select id="estadoPedido" name="estadoPedido">
						<option value="Pendente">Pendente</option>
						<option value="Cancelado">Cancelado</option>
						<option value="Não Autorizado">Não Autorizado</option>
						<option value="Autorizado">Autorizado</option>
						<option value="Rejeitado">Rejeitado</option>
						<option value="Concluído">Concluído</option>
					</select><br>

					<label for="IdRequerente">Requerente:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdRequerente" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdRequerente', 'listDropdown_IdRequerente', 'IdRequerente')" required>
						<input type="hidden" id="IdRequerente" name="IdRequerente">
						<div id="listDropdown_IdRequerente" class="listDropdown"></div>
					</div><br>

					<label for="numResp">Responsável:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numResp" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numResp', 'listDropdown_numResp', 'numResp')" required>
						<input type="hidden" id="numResp" name="numResp">
						<div id="listDropdown_numResp" class="listDropdown"></div>
					</div><br>

					<label for="numOrd">Ordenante:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numOrdem" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numOrdem', 'listDropdown_numOrdem', 'numOrd')" required>
						<input type="hidden" id="numOrd" name="numOrd">
						<div id="listDropdown_numOrdem" class="listDropdown"></div>
					</div><br>

					<label for="IdServico">Serviço:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdServico" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdServico', 'listDropdown_IdServico', 'IdServico')" required>
						<input type="hidden" id="IdServico" name="IdServico">
						<div id="listDropdown_IdServico" class="listDropdown"></div>
					</div><br>

					<label for="Descricao">Descrição:</label>
					<textarea id="Descricao" name="Descricao" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="numFunc1">Funcionário 1:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numFunc1" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numFunc1', 'listDropdown_numFunc1', 'numFunc1')" required>
						<input type="hidden" id="numFunc1" name="numFunc1">
						<div id="listDropdown_numFunc1" class="listDropdown"></div>
					</div><br>

					<label for="numFunc2">Funcionário 2:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_numFunc2" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_numFunc2', 'listDropdown_numFunc2', 'numFunc2')">
						<input type="hidden" id="numFunc2" name="numFunc2">
						<div id="listDropdown_numFunc2" class="listDropdown"></div>
					</div><br>

					<label for="dataRegisto">Data de Registo:</label>
					<input type="date" id="dataRegisto" name="dataRegisto" readonly><br>

					<label for="Observacoes">Observações:</label>
					<textarea id="Observacoes" name="Observacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="Despacho">Despacho:</label>
					<textarea id="Despacho" name="Despacho" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="dataDespacho">Data de Despacho:</label>
					<input type="date" id="dataDespacho" name="dataDespacho"><br>

					<label for="Notas">Notas:</label>
					<textarea id="Notas" name="Notas" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<label for="HoraIni">Hora de Início:</label>
					<input type="time" id="HoraIni" name="HoraIni"><br>

					<label for="DataIni">Data de Início:</label>
					<input type="date" id="DataIni" name="DataIni"><br>

					<label for="HoraFim">Hora de Fim:</label>
					<input type="time" id="HoraFim" name="HoraFim"><br>

					<label for="DataFim">Data de Fim:</label>
					<input type="date" id="DataFim" name="DataFim"><br>

					<label for="IdViatura">Viatura:</label><br>
					<div class="dropdown">
						<input type="text" id="inputDropdown_IdViatura" placeholder="Selecione na lista" oninput="funProcurarDropdown('inputDropdown_IdViatura', 'listDropdown_IdViatura', 'IdViatura')" required>
						<input type="hidden" id="IdViatura" name="IdViatura">
						<div id="listDropdown_IdViatura" class="listDropdown"></div>
					</div><br>

					<label for="kmsPrevistos">Kms Previstos:</label>
					<input type="number" id="kmsPrevistos" name="kmsPrevistos" placeholder="kms/h"><br>

					<label for="LocalPartida">Local de Partida:</label>
					<input type="text" id="LocalPartida" name="LocalPartida"><br>

					<label for="LocalDestino">Local de Destino:</label>
					<input type="text" id="LocalDestino" name="LocalDestino"><br>

					<label for="kmsIda">Kms de Ida:</label>
					<input type="number" id="kmsIda" name="kmsIda" oninput="calcularKms()" placeholder="kms/h"><br>

					<label for="kmsVolta">Kms de Volta:</label>
					<input type="number" id="kmsVolta" name="kmsVolta" oninput="calcularKms()" placeholder="kms/h"><br>
					
					<label for="kmsTotal">Kms Total</label>
					<input type="number" id="kmsTotal" name="kmsTotal" placeholder="Ida + Volta" readonly><br>
					
					<label for="Anotacoes">Anotações:</label>
					<textarea id="Anotacoes" name="Anotacoes" rows="6" placeholder="Escreva aqui..."></textarea><br>

					<input type="submit" value="Adicionar Pedido">
				</form>
			</div>
		</div>
	</div>

	<?php
	include __DIR__ . '/include/include_footer.php';
	?>
</body>
</html>
