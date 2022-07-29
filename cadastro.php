<?php
  include('config/db_connect.php');
if(isset($_POST['cadastrar'])){
  $login = mysqli_real_escape_string($conn, $_POST['username']);
  $senha = mysqli_real_escape_string($conn, md5($_POST['password']));
  $nome = mysqli_real_escape_string($conn, $_POST['nome_aluno']);

  $sql = "SELECT * FROM alunos WHERE login = '$login'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0){
  echo("<script type='text/javascript'>alert('Usuário ja existe!')</script>");
 }else{
	if($login == "" || $senha == ""){
    echo("<script type='text/javascript'>alert('Preencha todos os campos!')</script>");
  }else{
      $cad = "INSERT INTO alunos (nome_aluno, login, senha) VALUES ('$nome', '$login', '$senha')";
      $cadastrar = mysqli_query($conn, $cad); 
      if($cadastrar){
        echo ("<script type='text/javascript'>alert('Usuário cadastrado com sucesso!');window.location.href='index.php';</script>");
      }else{
        echo ("<script type='text/javascript'>alert('Usuário não pode ser adicionado!')</script>");
      }
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
<?php include('templates/header_login.php'); ?>
<body>
<script>
window.onload = function() {
  document.getElementById("nome_aluno").focus();
}
</script>
<section class="container grey-text">
<h4 class="center">Realizar Cadastro</h4>
<form method="POST" action="cadastro.php">
<label>Nome Completo:</label><input type="text" name="nome_aluno" id="nome_aluno"><br>  
<label>Login:</label><input type="text" name="username" id="login"><br>
<label>Senha:</label><input type="password" name="password" id="senha"><br>
<div class="center">
<input type="submit" value="Cadastrar" id="cadastrar" name="cadastrar" class="btn brand z-depth-1">
</div>
</form>
</section>
</body>
<?php include('templates/footer.php'); ?>
</html>