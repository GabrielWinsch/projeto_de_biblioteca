<?php
    include('../config/db_connect.php');
    require('../validacao.php');
    date_default_timezone_set("America/Sao_Paulo");
    $aluno = $_SESSION['login'];
    $erro = array('0'=> array('tit_obra'=>'','nome_autor'=>'','data_reserva'=>'','data_retirada'=>'', 'cod_aluno'=>'', 'cod_reserva'=>'', 'nome_aluno'=>''));
    if(isset($_GET['id'])){
        $cod_emprestimo = mysqli_real_escape_string($conn, $_GET['id']);
        
        $sql = "SELECT * FROM obras AS o 
        LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra 
        LEFT JOIN emprestimos AS e ON e.Reserva_cod_reserva = r.cod_reserva
        LEFT JOIN obras_has_autores AS oa ON o.cod_obra = oa.Obras_cod_obra 
        LEFT JOIN autores AS a ON oa.Autores_cod_autor = a.cod_autor
        LEFT JOIN alunos AS al ON al.cod_aluno = r.Alunos_cod_aluno
        WHERE e.cod_emprestimo = '$cod_emprestimo'";
        $result = mysqli_query($conn, $sql);
        $dados = mysqli_fetch_assoc($result);
        session_start();
        $_SESSION['emprestimo'] = $dados;
        mysqli_free_result($result);
        $cod_obra = $dados['cod_obra'];
        $sql = "UPDATE emprestimos SET devolvido = 'S' WHERE cod_emprestimo = '$cod_emprestimo'";
        if(mysqli_query($conn, $sql)){
            $sql = "UPDATE obras SET emprestada = 'N' WHERE cod_obra = '$cod_obra'";
            if(mysqli_query($conn, $sql)){
            header("Location: devolvido.php");
        }else{
            header("Location:emprestimos.php");
        }
        }
    }
    $obras = $_SESSION['emprestimo'];
    $devolvido = date("Y-m-d");
    $devolucao = $obras['data_devolucao'];
    function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat);
    }
    $dateDiff = dateDifference($devolvido, $devolucao);
    $multa = $dateDiff * 0.50;
?>
<!DOCTYPE html>
<html>
<?php include('../templates/header.php'); ?>
<h4 class="center grey-text">Resumo do Emprestimo</h4>
<div class="container center grey-text">
			<p>Titulo: <?php echo $obras['tit_obra']; ?></p>
			<p>Autor: <?php echo $obras['nome_autor']; ?></p>
			<p>Valor: <?php echo $obras['valor_obra'];?></p>
			<p>Codigo de Barra: <?php  $obras['cod_barra_obra'];?></p>
        <?php if($devolvido > $devolucao): ?>
            <p>Devolveu com <?php echo $dateDiff ?> dias de atraso. Aplicar multa de R$ <?php echo $multa ?></p>
		<?php else: ?>
			<h5>Devolveu dentro do prazo. Não aplicar multa.</h5>
		<?php endif ?>
	</div>
			<div class="center">
				<a class="btn brand z-depth-1" href="restrito.php">Voltar</a>	
			</div>

<?php include('../templates/footer.php'); ?>
</html>