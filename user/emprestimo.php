<?php
    include('../config/db_connect.php');
    require('../validacao.php');
    date_default_timezone_set("America/Sao_Paulo");
    $erro = array('0'=> array('tit_obra'=>'','nome_autor'=>'','data_reserva'=>'','data_retirada'=>'', 'cod_aluno'=>'', 
    'cod_reserva'=>'', 'nome_aluno'=>'','data_devolucao'=>'','renovado'=>'','cod_emprestimo'=>''));
    if(isset($_GET['cod_emprestimo'])){
        $cod_emprestimo = mysqli_real_escape_string($conn, $_GET['cod_emprestimo']);
        $sql = "SELECT * FROM obras AS o 
        LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra 
        LEFT JOIN emprestimos AS e ON e.Reserva_cod_reserva = r.cod_reserva
        WHERE e.cod_emprestimo = '$cod_emprestimo'";
        $result = mysqli_query($conn, $sql);
        $dados = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $renovado = $dados['renovado'];
        
        if($renovado >= 3){
            echo "<script type='text/javascript'>alert('Esta obra não pode mais ser renovada!');</script>";
        }else{
            $renovar = $renovado + 1;

            $d1 = $dados['data_devolucao'];
            $data_renovado = date("Y-m-d");
            $d2 = "+10 day";
            $d3 = strtotime($d1 . $d2); 
            $data_devolucao = date('Y-m-d', $d3);

            $sql = "UPDATE emprestimos SET data_devolucao = '$data_devolucao', data_renovacao = '$data_renovado', renovado = '$renovar' WHERE cod_emprestimo = $cod_emprestimo;";

            if(mysqli_query($conn, $sql)){
                header("Location:emprestimo.php");
            }else{
                echo "Erro no SQL";
            }
        }
    }
    $nome_aluno = $_SESSION['login'];
    $sql = "SELECT * FROM obras AS o
    LEFT JOIN reserva AS r ON o.cod_obra = r.Obras_cod_obra
    LEFT JOIN emprestimos AS e ON r.cod_reserva = e.Reserva_cod_reserva
    LEFT JOIN obras_has_autores AS oa ON o.cod_obra = oa.Obras_cod_obra 
    LEFT JOIN autores AS a ON oa.Autores_cod_autor = a.cod_autor
    LEFT JOIN alunos AS al ON al.cod_aluno = r.Alunos_cod_aluno
    WHERE o.emprestada = 'S' AND e.devolvido = 'N' AND al.nome_aluno = '$nome_aluno' ORDER BY o.tit_obra";
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
<?php include('../templates/header_user.php'); ?>
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
                                <td>Renovar</td>
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
                                <td><a class="brand-text" href="emprestimo.php<?php $id = $obra['cod_emprestimo']; if($id > 0){echo "?cod_emprestimo=".$id;} ?>">Renovar Obra</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>            
                    </table>
                </div>  
                <a href="site.php" class="btn brand z-depth-0">VOLTAR</a>          
	</div>
</body>
<?php include('../templates/footer.php');?>
</html>