<?php
//mysql connect
//conecta (servidor, usuario, senha, nome do banco)
$conn = mysqli_connect('localhost','root', '', 'biblioteca_db');
//testa conexão
if(!$conn){
	echo 'Erro na conexão'.mysqli_connect_error();
}
?>
