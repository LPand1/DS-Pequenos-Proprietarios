<?php

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$partes = explode('/', trim($uri, '/'));
$recurso = $partes[0] ?? '';
$id = $partes[1] ?? null;

switch ($recurso) {
    case 'usuarios':
        $controller = new UsuarioController();
        break;
    
    case 'proprietarios':
        $controller = new ProprietarioController();
        break;

    case 'inquilinos':
        $controller = new InquilinoController();
        break;

    case 'propriedades':
        $controller = new PropriedadeController();
        break;

    case 'gastos':
        $controller = new GastoController();
        break;

    case 'arquivos':
        $controller = new ArquivoController();
        break;

    default:
        http_response_code(404);
        echo json_encode(['erro' => 'Rota não encontrada']);
        exit;
}

switch ($metodo) {
    case 'GET':
        if ($id !== null) {
            $controller->buscarId($id);
        } else {
            $controller->buscarTodos();
        }
        break;

    case 'POST':
        $controller->inserir();
        break;

    case 'DELETE':
        if ($id !== null) {
            $controller->excluirId($id);
        } else {
            http_response_code(400);
            echo json_encode(['erro' => 'ID obrigatório para excluir']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não permitido']);
        exit;
}