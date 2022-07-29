<?php
    include('../config/db_connect.php');
    require('../validacao.php');
    date_default_timezone_set("America/Sao_Paulo");
    $aluno = $_COOKIE['login'];
    $erro = array('0'=> array('tit_obra'=>'','nome_autor'=>'','data_reserva'=>'','data_retirada'=>'', 'cod_aluno'=>'', 'cod_reserva'=>'', 'nome_aluno'=>''));
    $sql = "SELECT * FROM obras AS o 
    LEFT JOIN obras_has_autores AS oa ON o.cod_obra = oa.Obras_cod_obra 
    LEFT JOIN autores AS a ON oa.Autores_cod_autor = a.cod_autor 
    LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra
    LEFT JOIN alunos AS al ON al.cod_aluno = r.Alunos_cod_aluno
    WHERE r.cancelada = 'N' AND r.finalizada = 'N' ORDER BY o.tit_obra";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
    $obras = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    }else{
        $obras = $erro;
    }
    if(isset($_GET['id'])){
        $cod_reserva = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "SELECT * FROM obras AS o LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra WHERE r.cod_reserva = '$cod_reserva'";
        $result = mysqli_query($conn, $sql);
        $dados = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        $cod_obra = $dados['cod_obra'];
        $sql = "UPDATE reserva SET cancelada = 'S' WHERE cod_reserva = $cod_reserva;";
        if(mysqli_query($conn, $sql)){
            $sql = "UPDATE obras SET reservada = 'N' WHERE cod_obra = '$cod_obra'";
            if(mysqli_query($conn, $sql)){
            header("Location: reservas.php");
        }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>IBook.com</title>
</head>
<?php include('../templates/header.php'); ?>
<link rel="stylesheet" type="text/css" href="../style/dashboard.css">
<body>
<div id="content">
                <div id="tabelaUsuarios">
                    <span class="title">RESERVAS</span>
                    <table>
                        <thead>
                            <tr>
                                <td>Obra</td>
                                <td>Autor</td>
                                <td>Aluno</td>
                                <td>Data Reserva</td>
                                <td>Data Retirada</td>
                                <td>Cancelar Reserva</td>
                                <td>Finalizar Reserva</td>
                            </tr>                
                        </thead>
                        <tbody>
                        <?php foreach($obras as $obra): ?>
                            <tr>
                                <td><?php echo $obra["tit_obra"]; ?></td>
                                <td><?php echo $obra["nome_autor"]; ?></td>
                                <td><?php echo $obra["nome_aluno"]; ?></td>
                                <td><?php echo $obra["data_reserva"]; ?></td>
                                <td><?php echo $obra["data_retirada"]; ?></td>
                                <td><a class="brand-text" href="reservas.php<?php $id = $obra['cod_reserva']; if($id > 0){echo "?id=".$id;}?>">Cancelar Reserva</a></td>
                                <td><a class="brand-text" href="emprestimos.php<?php $id = $obra['cod_reserva']; if($id > 0){echo "?id=".$id;}?>">Finalizar Reserva</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>            
                    </table>
                </div>  
                <a href="restrito.php" class="btn brand z-depth-0">VOLTAR</a>          
	</div>
</body>
<?php include('../templates/footer.php');?>
</html>