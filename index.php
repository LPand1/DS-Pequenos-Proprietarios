<?php
require_once "config/autoload.php";
require_once "config/class.banco.php";

header('Content-Type: application/json; charset=utf-8');

require_once "routes/rotas.php";

$u = new Usuario();
$uDAO = new UsuarioDAO();
$uController = new UsuarioController();

require_once "config/class.Banco.php";

$banco = new Banco();
print_r($banco->getConexao());

$conexao = $banco->getConexao();

$stmt = $conexao->query("SELECT * FROM usuarios");
var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));

?>