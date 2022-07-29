<?php
include('../config/db_connect.php');
require('../validacao.php');
date_default_timezone_set("America/Sao_Paulo");
if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql ="SELECT * FROM obras as o LEFT JOIN obras_has_autores as oa ON o.cod_obra = oa.Obras_cod_obra LEFT JOIN autores as a ON oa.Autores_cod_autor = a.cod_autor WHERE o.cod_obra = '$id' ORDER BY o.tit_obra;";
    $result = mysqli_query($conn, $sql);
    $obras = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_close($conn);

    $_SESSION['obras'] = $obras;
}
if(isset($_POST['submit'])){

    $obras = $_SESSION['obras'];
    $usuario = $_SESSION['login'];
    $sql = "SELECT * FROM alunos WHERE nome_aluno = '$usuario'";
    $result = mysqli_query($conn, $sql);
    $usuario_cod = mysqli_fetch_assoc($result);
    $cod_usuario = $usuario_cod['cod_aluno'];
    $cod_obra = $obras['cod_obra'];
    $obra = $obras['tit_obra'];
    $autor = $obras['nome_autor'];
    $data_retirada = mysqli_real_escape_string($conn, $_POST['data_retirada']);
    $data_reserva = date("Y-m-d");

    $sql = "INSERT INTO reserva(Alunos_cod_aluno, Obras_cod_obra, data_reserva, data_retirada, cancelada, finalizada) 
    VALUES ('$cod_usuario', '$cod_obra', '$data_reserva', '$data_retirada', 'N', 'N')";
    if(mysqli_query($conn, $sql)){
        $sql = "UPDATE obras SET reservada = 'S' WHERE cod_obra = '$cod_obra'";
        if(mysqli_query($conn, $sql)){
        unset($_SESSION["obras"]);
        header ("Location: reservados.php");
    }
    }else{
        echo "DEU ERRADO";
        print_r($obras);
        print_r($usuario_cod);
        echo $cod_usuario;
        echo $cod_obra;
        echo $obra;
        echo $autor;
        echo $data_retirada;
        echo $data_reserva;
    }
}
?>
<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <title>IBook.com</title>
</head>
<?php include('../templates/header_user.php'); ?>
<link rel="stylesheet" type="text/css" href="../style/dashboard.css">
<body>
    <form method="POST" action="reserva.php">
        <label>Obra: <?php echo $obras['tit_obra']?></label><br>
        <label>Autor: <?php echo $obras['nome_autor']?></label><br>
        <label>Data para Retirada: </label>
        <input type="date" name="data_retirada" id="data_retirada" value="<?php echo date("Y-m-d") ?>" min="<?php echo date("Y-m-d")?>"max="<?php $d = strtotime("+1 Months"); echo date("Y-m-d", $d)?>">
        <input type="submit" name="submit" value="submit">
    </form>
</body>
<?php include('../templates/footer.php');?>
</html>