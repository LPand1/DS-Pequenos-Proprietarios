<?php
require_once "autoload.php";
require_once "config/conexao.php";

header('Content-Type: application/json; chaset=utf-8');

$usuarios = new UsuarioController();
$propriedades = new PropriedadeController();
?>