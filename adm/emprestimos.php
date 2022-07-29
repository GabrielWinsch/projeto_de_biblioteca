<?php
    include('../config/db_connect.php');
    require('../validacao.php');
    date_default_timezone_set("America/Sao_Paulo");
    $erro = array('0'=> array('tit_obra'=>'','nome_autor'=>'','data_reserva'=>'','data_retirada'=>'', 'cod_aluno'=>'', 
    'cod_reserva'=>'', 'nome_aluno'=>'','data_devolucao'=>'','renovado'=>'','cod_emprestimo'=>''));
    if(isset($_GET['id'])){
        $cod_reserva = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "SELECT * FROM obras AS o 
        LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra 
        LEFT JOIN emprestimos AS e ON e.Reserva_cod_reserva = r.cod_reserva
        WHERE r.cod_reserva = '$cod_reserva'";
        $result = mysqli_query($conn, $sql);
        $dados = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $cod_obra = $dados['cod_obra'];
        $data_retirada = date("Y-m-d");
        $d = strtotime("+1 Months"); 
        $data_devolucao = date("Y-m-d", $d);
        $sql = "UPDATE reserva SET finalizada = 'S' WHERE cod_reserva = $cod_reserva;";
        $sql2 = "INSERT INTO emprestimos (Reserva_cod_reserva, data_retirada, data_devolucao, data_renovacao, renovado)
        VALUES ('$cod_reserva','$data_retirada','$data_devolucao','$data_retirada',1)";
        if(mysqli_query($conn, $sql)&&mysqli_query($conn, $sql2)){
            $sql = "UPDATE obras SET emprestada = 'S', reservada = 'N' WHERE cod_obra = '$cod_obra'";
            if(mysqli_query($conn, $sql)){
            header("Location:emprestimos.php");
        }else{
            header("Location:reservas.php");
        }
        }
    }
    $sql = "SELECT * FROM obras AS o
    LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra
    LEFT JOIN emprestimos AS e ON r.cod_reserva = e.Reserva_cod_reserva
    LEFT JOIN obras_has_autores AS oa ON o.cod_obra = oa.Obras_cod_obra 
    LEFT JOIN autores AS a ON oa.Autores_cod_autor = a.cod_autor
    LEFT JOIN alunos AS al ON al.cod_aluno = r.Alunos_cod_aluno
    WHERE o.emprestada = 'S' AND e.devolvido = 'N' ORDER BY o.tit_obra";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
    $obras = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);
    }else{
        $obras = $erro;
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
                    <span class="title">EMPRÉSTIMOS</span>
                    <table>
                        <thead>
                            <tr>
                                <td>Obra</td>
                                <td>Autor</td>
                                <td>Aluno</td>
                                <td>Data Retirada</td>
                                <td>Data Devolução</td>
                                <td>Renovações</td>
                                <td>Devolvido</td>
                            </tr>                
                        </thead>
                        <tbody>
                        <?php foreach($obras as $obra): ?>
                            <tr>
                                <td><?php echo $obra["tit_obra"]; ?></td>
                                <td><?php echo $obra["nome_autor"]; ?></td>
                                <td><?php echo $obra["nome_aluno"]; ?></td>
                                <td><?php echo $obra["data_retirada"]; ?></td>
                                <td><?php echo $obra["data_devolucao"]; ?></td>
                                <td><?php echo $obra["renovado"]; ?></td>
                                <td><a class="brand-text" href="<?php $id = $obra['cod_emprestimo']; if($id > 0){echo "devolvido.php?id=".$id;}?>">Devolvido</a></td>
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