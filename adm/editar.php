<?php
include('../config/db_connect.php');
require('../validacao.php');
	// check GET request id param
	if(isset($_GET['id'])){
		// escape sql chars
		$id = mysqli_real_escape_string($conn, $_GET['id']);
		// make sql
		$sql = "SELECT * FROM obras as o LEFT JOIN obras_has_autores as oa ON o.cod_obra = oa.Obras_cod_obra LEFT JOIN autores as a ON a.cod_autor = oa.Autores_cod_autor 
		WHERE o.cod_obra = $id";
		// get the query result
		$result = mysqli_query($conn, $sql);
		// fetch result in array format
		$obras = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		mysqli_close($conn);
		$_SESSION['obras'] = $obras;
	}
$erro_form = "";
$cod_obra = $tit_obra = $ano_obra = $pag_obra = $cat_obra = $cod_barra_obra = $basico_obra = $img_obra = $editora_obra = $edicao_obra = $isbn_obra = $ling_obra = $valor_obra = '';
$errors = array('titulo'=>'','ano'=>'', 'pagina'=>'', 'categoria'=>'','cod_barra'=>'','basico'=>'','imagem'=>'','editora'=>'','edicao'=>'','isbn'=>'','linguagem'=>'','valor'=>'');
if(isset($_POST['salvar'])){
		$obras = $_SESSION['obras'];
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
		if(empty($_POST['autor'])){
			$errors['autor'] = "Autor nao pode estar vazio!";
		}
		if(!empty($img_obra["name"])){
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
		$cod_obra = mysqli_real_escape_string($conn, $_POST['id']);
		//separa nome e ultimo nome
		$nome_completo_autor = mysqli_real_escape_string($conn, $_POST['autor']);
		$nomes_autor = explode(' ', $nome_completo_autor);
		$nome_autor = $nomes_autor[0];
		$sobrenome_autor = $nomes_autor[count($nomes_autor) -1];	
		$citacao_autor = mb_strtoupper($sobrenome_autor).", ".$nome_autor;	

		//cria Sql
		$sql = "UPDATE obras SET tit_obra = '$tit_obra', ano_obra = '$ano_obra', pag_obra = '$pag_obra', cat_obra = '$cat_obra', cod_barra_obra = '$cod_barra_obra',
		 basico_obra = '$basico_obra', img_obra = '$nome_imagem', editora_obra = '$editora_obra', edicao_obra = '$edicao_obra', isbn_obra = '$isbn_obra', 
		 ling_obra = '$ling_obra', valor_obra = '$valor_obra' WHERE cod_obra = '$cod_obra'";
		
		//salve no db e check
		if(mysqli_query($conn, $sql)){//Sucesso
			$sql = "SELECT * FROM obras as o LEFT JOIN obras_has_autores as oa ON o.cod_obra = oa.Obras_cod_obra LEFT JOIN autores as a ON a.cod_autor = oa.Autores_cod_autor 
			WHERE o.cod_obra = $cod_obra";
			$result = mysqli_query($conn, $sql);
			$obras = mysqli_fetch_assoc($result);
			$nome_antigo_autor = $obras['nome_autor'];
			$cod_autor = $obras['cod_autor'];
			//teste se o autor ja existe no banco
			if($nome_completo_autor == $nome_antigo_autor){
				header ('Location: restrito.php');
			}else{
				$sql = "SELECT * FROM autores WHERE nome_autor = '$nome_completo_autor'";
				$result_autor = mysqli_query($conn, $sql);
				if(mysqli_num_rows($result_autor) > 0){
					$autores = mysqli_fetch_assoc($result_autor);
					$cod_autor = $autores['cod_autor'];
					$sql = "UPDATE obras_has_autores SET Autores_cod_autor = '$cod_autor' 
					WHERE Obras_cod_obra = '$cod_obra'";
					if(mysqli_query($conn, $sql)){
						unset($_SESSION["obras"]);
						header ('Location: restrito.php');
					}else{
						echo 'query error:' .mysqli_error($conn);
					}
				}else{
					$sql = "UPDATE autores SET nome_autor = '$nome_completo_autor', citacao_autor = '$citacao_autor' 
					WHERE cod_autor = '$cod_autor'";
					if(mysqli_query($conn, $sql)){
						unset($_SESSION["obras"]);
						header ('Location: restrito.php');
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

if(isset($_POST['excluir'])){
	$cod_obra = mysqli_real_escape_string($conn, $_POST['id']);
	$sql1 = "DELETE FROM obras_has_autores WHERE Obras_cod_obra = '$cod_obra'";
	$sql = "DELETE FROM obras WHERE cod_obra = '$cod_obra'";
	if(mysqli_query($conn, $sql1) && mysqli_query($conn, $sql)){//Sucesso
		unset($_SESSION["obras"]);
		header('Location: restrito.php');

	}else{
		//erro
		echo 'query error:' .mysqli_error($conn);
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
	<h4 class="center">Editar uma obra</h4>
	<div class="red-text center"><?php echo $erro_form; ?></div>
	<form action="editar.php" method="POST" enctype="multipart/form-data">
		<input class="hide" type="text" name="id" value="<?php echo $id?>">

		<label>Titulo:</label>
		<input type="text" name="titulo" id="titulo"value="<?php echo $obras['tit_obra']?>">
		<div class="red-text"><?php echo $errors['titulo']; ?></div>
		<label>Autor:</label>
		<input type="text" name="autor" id="autor"value="<?php echo $obras['nome_autor']?>">
		
		<label>Ano:</label>
		<input type="text" name="ano" value="<?php echo $obras['ano_obra']?>">
		
		<label>Paginas:</label>
		<input type="text" name="pagina" value="<?php echo $obras['pag_obra']?>">
		
		<label>Categoria:</label>
		<input type="text" name="categoria" value="<?php echo $obras['cat_obra']?>">
		
		<label>Codigo de Barra:</label>
		<input type="text" name="cod_barra" value="<?php echo $obras['cod_barra_obra']?>">
		
		<label>Basico:</label>
		<input type="text" name="basico" value="<?php echo $obras['basico_obra']?>">
		
		<label>Imagem:</label><br><br>
		<input type="file" name="imagem"><br><br>
		<div class="red-text"><?php echo $errors['imagem']; ?></div>
		<label>Editora:</label>
		<input type="text" name="editora" value="<?php echo $obras['editora_obra']?>">
		
		<label>Edição:</label>
		<input type="text" name="edicao" value="<?php echo $obras['edicao_obra']?>">
		
		<label>ISBN:</label>
		<input type="text" name="isbn" value="<?php echo $obras['isbn_obra']?>">
		
		<label>Linguagem:</label>
		<input type="text" name="linguagem" value="<?php echo $obras['ling_obra']?>">
		
		<label>Valor:</label>
		<input type="text" name="valor" value="<?php echo $obras['valor_obra']?>">
		
		<div class="center">
			<input type="submit" name="salvar" value="salvar" class="btn brand z-depth-1">
            <a class="btn brand z-depth-1" href="restrito.php">Voltar</a>
			<input type="submit" name="excluir" value="excluir" class="btn brand z-depth-1">
		</div>
	</form>
</section>
<?php include('../templates/footer.php');?>
</body>
</html>