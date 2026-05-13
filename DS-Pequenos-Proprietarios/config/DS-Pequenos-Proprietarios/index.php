<?php
require_once "config/autoload.php";
require_once "config/class.banco.php";

header('Content-Type: application/json; chaset=utf-8');

$u = new Usuario();
$uDAO = new UsuarioDAO();
$uController = new UsuarioController();

require_once "config/class.Banco.php";

$banco = new Banco();
print_r($banco->getConexao());

$conexao = $banco->getConexao();

$stmt = $conexao->query("SELECT * FROM usuarios");
var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));

$uController->inserir(123, '029.496.240-90');
$uController->excluirTodos();

?>