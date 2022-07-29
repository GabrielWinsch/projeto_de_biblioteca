<?php

include('../config/db_connect.php');
require('../validacao.php');

$erro_form = "";
$tit_obra = $ano_obra = $pag_obra = $cat_obra = $cod_barra_obra = $basico_obra = $nome_imagem = $editora_obra = $edicao_obra = $isbn_obra = $ling_obra = $valor_obra = $nome_autor = $sobrenome_autor = '';
$errors = array('titulo'=>'','ano'=>'', 'pagina'=>'', 'categoria'=>'','cod_barra'=>'','basico'=>'','imagem'=>'','editora'=>'','edicao'=>'','isbn'=>'','linguagem'=>'','valor'=>'', 'autor'=>'', 'sobrenome'=>'');
if(isset($_POST['submit'])){

		$tit_obra = $_POST['titulo']; 
		$ano_obra=$_POST['ano'];
		$pag_obra=$_POST['pagina'];
		$cat_obra=$_POST['categoria'];
		$cod_barra_obra=$_POST['cod_barra'];
		$basico_obra=$_POST['basico'];
		$img_obra=$_FILES['imagem'];
		$editora_obra=$_POST['editora'];
		$edicao_obra=$_POST['edicao'];
		$isbn_obra=$_POST['isbn'];
		$ling_obra=$_POST['linguagem'];
		$valor_obra=$_POST['valor'];

			if(empty($_POST['titulo'])){
				$errors['titulo'] = "Titulo nao pode estar vazio!";
			}

			if (!empty($img_obra["name"])) {
				$largura = 1000;
				$altura = 1000;
				$tamanho = 100000;
				$error = array();
				if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $img_obra["type"])){
					$error[1] = "Isso não é uma imagem.";
					} 
				$dimensoes = getimagesize($img_obra["tmp_name"]);
				if($dimensoes[0] > $largura) {
					$error[2] = "A largura da imagem não deve ultrapassar ".$largura." pixels";
				}
				if($dimensoes[1] > $altura) {
					$error[3] = "Altura da imagem não deve ultrapassar ".$altura." pixels";
				}
				if($img_obra["size"] > $tamanho) {
					$error[4] = "A imagem deve ter no máximo ".$tamanho." bytes";
				}
				if (count($error) == 0) {
					preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_obra["name"], $ext);
					$nome_imagem = md5(uniqid(time())) . "." . $ext[1];
					$caminho_imagem = "../fotos/" . $nome_imagem;
					move_uploaded_file($img_obra["tmp_name"], $caminho_imagem);
				}
				if (count($error) != 0) {
					$errors['imagem'] = implode($error);
				}
			}

if (array_filter($errors)){
	$erro_form = "Há erros no formulário!";
}else{
		$tit_obra = mysqli_real_escape_string($conn, $_POST['titulo']);
		$ano_obra = mysqli_real_escape_string($conn, $_POST['ano']);
		$pag_obra = mysqli_real_escape_string($conn, $_POST['pagina']);
		$cat_obra = mysqli_real_escape_string($conn, $_POST['categoria']);
		$cod_barra_obra = mysqli_real_escape_string($conn, $_POST['cod_barra']);
		$basico_obra = mysqli_real_escape_string($conn, $_POST['basico']);
		$editora_obra = mysqli_real_escape_string($conn, $_POST['editora']);
		$edicao_obra = mysqli_real_escape_string($conn, $_POST['edicao']);
		$isbn_obra = mysqli_real_escape_string($conn, $_POST['isbn']);
		$ling_obra = mysqli_real_escape_string($conn, $_POST['linguagem']);
		$valor_obra = mysqli_real_escape_string($conn, $_POST['valor']);
		if(empty($_POST['autor'])){
			$nome_completo_autor = "Autor Desconhecido";
		}else{
			//separa nome e ultimo nome
			$nome_completo_autor = mysqli_real_escape_string($conn, $_POST['autor']);
			$nomes_autor = explode(' ', $nome_completo_autor);
			$nome_autor = $nomes_autor[0];
			$sobrenome_autor = $nomes_autor[count($nomes_autor) -1];	
			$citacao_autor = mb_strtoupper($sobrenome_autor).", ".$nome_autor;	
		}
		
		$sql_obra = "INSERT INTO obras(tit_obra, ano_obra, pag_obra, cat_obra, cod_barra_obra, basico_obra, img_obra, editora_obra, edicao_obra, isbn_obra, ling_obra, valor_obra) 
		VALUES('$tit_obra','$ano_obra','$pag_obra','$cat_obra','$cod_barra_obra','$basico_obra','$nome_imagem','$editora_obra','$edicao_obra','$isbn_obra','$ling_obra','$valor_obra')";
			
			if(mysqli_query($conn, $sql_obra)){
				$sql_autor = "SELECT * FROM autores WHERE nome_autor = '$nome_completo_autor'";

				$sql_obra = "SELECT * FROM obras WHERE cod_obra=(SELECT max(cod_obra) FROM obras)";
				$result=mysqli_query($conn, $sql_obra);
				$cod_obra = mysqli_fetch_assoc($result);
				mysqli_free_result($result);
				$obra = $cod_obra['cod_obra'];

				$result_autor = mysqli_query($conn, $sql_autor);

					if (mysqli_num_rows($result_autor) > 0){
						$cod_autor = mysqli_fetch_assoc($result_autor);
						$autor = $cod_autor['cod_autor'];
						$sql = "INSERT INTO Obras_has_autores(Obras_cod_obra, Autores_cod_autor) VALUES ('$obra', '$autor')";
						if(mysqli_query($conn, $sql)){
							header('Location: restrito.php');
						}else{
							echo 'query error:' .mysqli_error($conn);
						}	
					}else{
						$sql = "INSERT INTO autores(nome_autor, citacao_autor) VALUES ('$nome_completo_autor', '$citacao_autor')";

						if(mysqli_query($conn, $sql)){

							$sql_autor = 'SELECT * FROM autores WHERE cod_autor=(SELECT max(cod_autor) FROM autores)';
							$result_autor=mysqli_query($conn, $sql_autor);
							$cod_autor = mysqli_fetch_assoc($result_autor);
							mysqli_free_result($result_autor);
							$autor = $cod_autor['cod_autor'];

							$sql = "INSERT INTO Obras_has_autores(Obras_cod_obra, Autores_cod_autor) VALUES ('$obra', '$autor')";
							if(mysqli_query($conn, $sql)){
								header('Location: restrito.php');
							}else{
								echo 'query error:' .mysqli_error($conn);
							}	
						}
					}
			}else{
				echo 'query error:' .mysqli_error($conn);
			}		
		}
	}

?>
<!DOCTYPE html>
<html>
<body>
<script>
window.onload = function() {
  document.getElementById("titulo").focus();
}
</script>
<?php include('../templates/header.php');?>
<section class="container grey-text">
	<h4 class="center">Adicionar uma obra</h4>
	<div class="red-text center"><?php echo $erro_form; ?></div>
	<form action="add.php" method="POST" enctype="multipart/form-data">
		<label>Titulo da Obra:</label>
		<input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($tit_obra)?>">
		<div class="red-text"><?php echo $errors['titulo']; ?></div>
		<label>Autor:</label>
		<input type="text" name="autor" id="autor" value="<?php echo htmlspecialchars($nome_autor)?>">
		<div class="red-text"><?php echo $errors['autor']; ?></div>
		<label>Ano:</label>
		<input type="text" name="ano" value="<?php echo htmlspecialchars($ano_obra)?>">
		<div class="red-text"><?php echo $errors['ano']; ?></div>
		<label>Paginas:</label>
		<input type="text" name="pagina" value="<?php echo htmlspecialchars($pag_obra)?>">
		<div class="red-text"><?php echo $errors['pagina']; ?></div>
		<label>Categoria:</label>
		<input type="text" name="categoria" value="<?php echo htmlspecialchars($cat_obra)?>">
		<div class="red-text"><?php echo $errors['categoria']; ?></div>
		<label>Codigo de Barra:</label>
		<input type="text" name="cod_barra" value="<?php echo htmlspecialchars($cod_barra_obra)?>">
		<div class="red-text"><?php echo $errors['cod_barra']; ?></div>
		<label>Obra Basica:</label>
		<input type="text" name="basico" value="<?php echo htmlspecialchars($basico_obra)?>">
		<div class="red-text"><?php echo $errors['basico']; ?></div>
		<label>Imagem:</label><br><br>
		<input type="file" name="imagem"><br><br>
		<div class="red-text"><?php echo $errors['imagem']; ?></div>
		<label>Editora:</label>
		<input type="text" name="editora" value="<?php echo htmlspecialchars($editora_obra)?>">
		<div class="red-text"><?php echo $errors['editora']; ?></div>
		<label>Edição:</label>
		<input type="text" name="edicao" value="<?php echo htmlspecialchars($edicao_obra)?>">
		<div class="red-text"><?php echo $errors['edicao']; ?></div>
		<label>ISBN:</label>
		<input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn_obra)?>">
		<div class="red-text"><?php echo $errors['isbn']; ?></div>
		<label>Linguagem:</label>
		<input type="text" name="linguagem" value="<?php echo htmlspecialchars($ling_obra)?>">
		<div class="red-text"><?php echo $errors['linguagem']; ?></div>
		<label>Valor:</label>
		<input type="text" name="valor" value="<?php echo htmlspecialchars($valor_obra)?>">
		<div class="red-text"><?php echo $errors['valor']; ?></div>
		<div class="center">
			<input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
		</div>
	</form>
</section>
<?php include('../templates/footer.php');?>
</body>
</html>