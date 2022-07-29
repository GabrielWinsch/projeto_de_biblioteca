<?php
  include('config/db_connect.php');
  if(isset($_POST['entrar'])){

    $login = mysqli_real_escape_string($conn, $_POST['nome']);
    $senha = mysqli_real_escape_string($conn, md5($_POST['senha']));

    $sql = "SELECT * FROM alunos WHERE login = '$login' AND senha = '$senha'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) <= 0){
      echo "<script type='text/javascript'>alert('Login e/ou senha incorretos');</script>";

    }else{
      $adm = mysqli_fetch_assoc($result);
      $nome_completo_aluno = $adm['nome_aluno'];
      $nomes_aluno = explode(' ', $nome_completo_aluno);
      $nome_aluno = $nomes_aluno[0];

      if($adm['adm'] == 1){
        session_start();
        $_SESSION['login'] = $nome_completo_aluno;
      header("Location:adm/restrito.php");
      }else{
        session_start();
        $_SESSION['login'] = $nome_completo_aluno;
      header("Location:user/site.php");
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>IBook</title>
</head>
<?php include('templates/header_cadastro.php'); ?>
<body>
<script>
window.onload = function() {
  document.getElementById("nome").focus();
}
</script>
<section class="container grey-text">
<h4 class="center">Login</h4>
  <form method="POST" action="index.php">
  <label>Login:</label><input type="text" name="nome" id="nome"><br>
  <label>Senha:</label><input type="password" name="senha" id="senha"><br>
  <div class="center">
  <input type="submit" value="entrar" id="entrar" name="entrar" class="btn brand z-depth-1">
</div>
</form>
</section>
</body>
<?php include('templates/footer.php'); ?>
</html>