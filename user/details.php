<?php
include('../config/db_connect.php');
require('../validacao.php');

	if(isset($_GET['id'])){
		$id = mysqli_real_escape_string($conn, $_GET['id']);
		$sql = "SELECT * FROM obras as o LEFT JOIN obras_has_autores as oa ON o.cod_obra = oa.Obras_cod_obra LEFT JOIN autores as a ON oa.Autores_cod_autor = a.cod_autor 
		WHERE cod_obra = $id";
		$result = mysqli_query($conn, $sql);
		$obras = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		mysqli_close($conn);
	}
?>
<!DOCTYPE html>
<html>

<?php include('../templates/header_user.php'); ?>
<h4 class="center grey-text">Detalhes da Obra</h4>
<div class="container center grey-text">
		<?php if($obras): ?>
			<p><?php if($obras['img_obra']){ echo "<img src='../fotos/".$obras['img_obra']."'height='300' alt='Foto de exibição' />";};?></p>
			<p>Titulo: <?php echo $obras['tit_obra']; ?></p>
			<p>Autor: <?php echo $obras['nome_autor']; ?></p>
			<p>Citação do Autor: <?php echo $obras['citacao_autor']; ?></p>
			<p>Quantidade de Paginas: <?php echo $obras['pag_obra']; ?></p>
			<p>Edição: <?php echo $obras['edicao_obra']; ?></p>
			<p>ISBN: <?php echo $obras['isbn_obra']; ?></p>
			<p>Linguagem: <?php echo $obras['ling_obra']; ?></p>
			<p>Categoria: <?php echo $obras['cat_obra']; ?></p>
			<p>Ano de Lançamento: <?php echo $obras['ano_obra']; ?></p>
			<p>Editora: <?php echo $obras['editora_obra']; ?></p>
			<p>Valor: <?php echo $obras['valor_obra'];?></p>
			<p>Codigo de Barra: <?php  $obras['cod_barra_obra'];?></p>
		<?php else: ?>
			<h5>Error 404.</h5>
		<?php endif ?>
	</div>
			<div class="center">
				<a class="btn brand z-depth-1" href="site.php">Voltar</a>
				<a class="btn brand z-depth-1" href="reserva.php?id=<?php echo $obras['cod_obra']?>">Reservar</a>	
			</div>

<?php include('../templates/footer.php'); ?>
</html>