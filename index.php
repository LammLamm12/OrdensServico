<?php
session_start();

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Menu de Ordens</title>
    <script src="./js/fun_index.js" defer></script>
	<?php
	include __DIR__ . '/css/relSrc.php';
	?>
</head>
<body>	
	<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
		<a href="#" class="sidebar-toggler flex-shrink-0"> <i class="fa fa-bars"></i></a>
		<div class="navbar-nav align-items-center ms-auto">
			<?php
			if (isset($_SESSION['IdOperador'])) {
				echo '<span class="nav-item d-none d-lg-inline-flex">Bem-vindo, ' . $_SESSION['NomeOper'] . '!</span>';			
				echo '<a href="./areaOperador.php?IdOperador=' . $_SESSION['IdOperador'] . '" class="btn btn-light mx-2">Área do Operador</a>';
				echo '<a href="./db/db_logout.php" class="btn btn-danger mx-2">Logout</a>';
			} else {
				echo '<a id="btnLogin" class="btn btn-primary mx-2">Login</a>';
			}
			?>
		</div>
	</nav>
	
	<div class="sidebar pe-4 pb-3">
		<nav class="navbar bg-light navbar-light">
			<a href="index.html" class="navbar-brand mx-4 mb-3"></a>
			<div class="navbar-nav w-100">
				<?php
				if (isset($_SESSION['IdOperador'])) {
                    echo '<a href="index.php" class="nav-item nav-link"><i class="fa-solid fa-globe"></i>Dashboard</a>';
					echo '<a href="menuOrdens.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Ordens</a>';	
				 // Opções de permissões
                echo '<div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Permissões</a>
                        <div class="dropdown-menu bg-transparent border-0">';
                
                // Checkboxes para alterar permissões
                echo '<form class="formSidebar" id="formPermissoes" method="POST" style="padding: 15px;">';
				echo '<p>Permissões Atuais: <span id="permissao">' . $_SESSION['permissao'] . '</span></p>';
                $tiposOper = explode(', ', $_SESSION['tipoOper']);
                $permissoesAtuais = explode(', ', $_SESSION['permissao']);
                $opcoesPermissao = ['Utilizador', 'Funcionário', 'Responsável', 'Ordenante'];
				
				echo '<div class="containerCheckbox">';
				foreach ($opcoesPermissao as $opcao) {
					$checked = in_array($opcao, $permissoesAtuais) ? 'checked' : ''; 
					echo "<div>";
						echo "<input type='checkbox' id='$opcao' name='tipoOper[]' value='$opcao' $checked>";
						echo "<label for='$opcao'>$opcao</label>";
					echo "</div>";
				}
				echo '</div>';

                // Administrador
                $permiteAdmin = in_array('Utilizador', $tiposOper) &&
                                in_array('Funcionário', $tiposOper) &&
                                in_array('Responsável', $tiposOper) &&
                                in_array('Ordenante', $tiposOper);

                if ($permiteAdmin) {
                    $checkedAdmin = in_array('Administrador', $permissoesAtuais) ? 'checked' : ''; 
                    echo "<div>
                            <input type='checkbox' id='Administrador' name='tipoOper[]' value='Administrador' $checkedAdmin>
                            <label for='Administrador'>Administrador</label>
                          </div>";
                }

                echo '<input type="submit" value="Atualizar" class="btn btn-primary" style="margin-top: 10px;">';
                echo '</form></div></div>';
				}
				
				
				?>
			</div>
		</nav>
	</div>
	
    <div class="container-xxl position-relative bg-white d-flex p-0">
		<div class="containerIndex">
			<a href="menuOrdens.php" class="boxIndex">
				<img src="./img/fudok1.webp" alt="Logótipo SGO">
				<p>Ordens de Serviço</p>
			</a>
			<a href="menuOrdens.php" class="boxIndex">
				<img src="./img/fudok1.webp" alt="Logótipo SGO">
				<p>Menu 2</p>
			</a>
			<a href="menuOrdens.php" class="boxIndex">
				<img src="./img/fudok1.webp" alt="Logótipo SGO">
				<p>Menu 3</p>
			</a>
			<a href="menuOrdens.php" class="boxIndex">
				<img src="./img/fudok1.webp" alt="Logótipo SGO">
				<p>Menu 4</p>
			</a>
			<a href="menuOrdens.php" class="boxIndex">
				<img src="./img/fudok1.webp" alt="Logótipo SGO">
				<p>Menu 5</p>
			</a>
			<a href="menuOrdens.php" class="boxIndex">
				<img src="./img/fudok1.webp" alt="Logótipo SGO">
				<p>Menu 6</p>
			</a>
			
		</div>
    </div>
	
	<button class="btnFooter"></button>
	<div class="footer d-flex justify-content-between align-items-center">
		<div id="calendar"></div>
		<div class="mapouter">
			<div class="gmap_canvas">
				<iframe src="https://maps.google.com/maps?q=penamacor&amp;t=k&amp;z=14&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" style="width: 300px; height: 300px;"></iframe>
			</div>
		</div>
	</div>

	
	<div id="modalLogin" class="modal">
		<div class="modalConteudo">
			<span class="close" onclick="closeModalLogin()">&times;</span>
            <h2>Iniciar Sessão</h2>
			<form id="loginForm" action="db/db_login.php" method="POST">
				<label for="numOper">Número:</label>
				<input type="text" id="numOper" name="numOper" required>
				<br>
				<label for="password">Senha:</label>
				<input type="password" id="password" name="password">
				<br>
				<input type="submit" value="Login">
			</form>
		</div>
	</div>
</body>
</html>
