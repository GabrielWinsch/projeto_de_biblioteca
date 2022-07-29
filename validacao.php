<?php
session_start();
$aluno = $_SESSION['login'];
if(!isset($_SESSION['login'])){
    header("Location:../index.php");
}
?>