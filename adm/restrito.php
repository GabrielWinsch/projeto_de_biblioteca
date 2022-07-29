<?php // abre PHP

include('../config/db_connect.php');
require('../validacao.php');

$total_reg = "7";
if(isset($_GET['pagina'])){
    $pagina = $_GET['pagina'];
}else{
    $pagina = 1;
}

if(!$pagina){
    $pc = "1";
}else{
    $pc = $pagina;
}
$inicio = $pc - 1;
$inicio = $inicio * $total_reg;

if(isset($_GET['pesquisa'])){
    $pesquisa = mysqli_real_escape_string($conn, $_GET['pesquisa']);
}else{$pesquisa = "";}

$sql = "SELECT * FROM obras as o 
LEFT JOIN obras_has_autores as oa ON o.cod_obra = oa.Obras_cod_obra 
LEFT JOIN autores as a ON oa.Autores_cod_autor = a.cod_autor
WHERE o.tit_obra LIKE '%$pesquisa%' OR
a.nome_autor LIKE '%$pesquisa%'
ORDER BY o.tit_obra";
$limite = mysqli_query($conn, "$sql LIMIT $inicio, $total_reg");
$result=mysqli_query($conn, $sql);

$tr = mysqli_num_rows($result);
$tp = ceil($tr / $total_reg);

if(mysqli_num_rows($limite) > 0){
    $obras = mysqli_fetch_all($limite, MYSQLI_ASSOC);
    mysqli_free_result($limite);
    mysqli_close($conn);
}else{
      $obras = array('0'=> array('tit_obra'=>'','cat_obra'=>'','ling_obra'=>'','nome_autor'=>'', 'cod_obra'=>''));
      echo "<script type='text/javascript'>alert('Nenhuma obra corresponde a pesquisa');window.location = 'restrito.php'</script>";
}
?>

<!DOCTYPE html>
<html>
<?php include('../templates/header.php'); ?>
<link rel="stylesheet" type="text/css" href="../style/dashboard.css">

<div id="content">
                <div id="tabelaUsuarios">
                <form action="restrito.php" method="GET">
                        <div class="center">
                        <label>Pesquisar:</label>
                        <input type="text" name="pesquisa" value="" id="pesquisa">
                        <button class="btn brand z-depth-0" type="submit" value="pesquisar" name="button">Pesquisar</button>
                        </div>
                    </form>
                    <div class="center">
                    <td><a class="brand-text" href="restrito.php?<?php if(isset($pesquisa)){echo "pesquisa=$pesquisa&";}?>pagina=<?php if($pc>1){echo $pc -1;}else{echo $pc;};?>">Anterior <--</a></td>
                    <td><a class="brand-text"><?php echo "pág $pc de $tp" ?></td>
                    <td><a class="brand-text" href="restrito.php?<?php if(isset($pesquisa)){echo "pesquisa=$pesquisa&";}?>pagina=<?php if($pc<$tp){echo $pc +1;}else{echo $pc;};?>">--> Proxima</a></td>
                    </div>
                    <span class="title">Lista de Obras</span>
                    <table>
                        <thead>
                            <tr>
                                <td>Titulo</td>
                                <td>Categoria</td>
                                <td>Linguagem</td>
                                <td>Autor</td>
                                <td>Reservada</td>
                                <td>Emprestada</td>
								<td>Mais Info</td>
                                <td>Editar</td>
                            </tr>                
                        </thead>
					
                        <tbody>
						<?php foreach($obras as $obra): ?>
                            <tr>
                                <td><?php echo $obra["tit_obra"]; ?></td>
                                <td><?php echo $obra["cat_obra"]; ?></td>
                                <td><?php echo $obra["ling_obra"]; ?></td>
                                <td><?php echo $obra["nome_autor"]; ?></td>
                                <td><?php echo $obra["reservada"]; ?></td>
                                <td><?php echo $obra["emprestada"]; ?></td>
                                <td><a class="brand-text" href="details.php?id=<?php echo $obra['cod_obra']?>">Mais Info</a></td>
								<td><a class="brand-text" href="editar.php?id=<?php echo $obra['cod_obra']?>">Editar</a></td>
                            </tr>
						<?php endforeach; ?>
                        </tbody>            
                    </table>
                </div>            
	</div>
	
<?php include('../templates/footer.php');?>
</body>
</html>