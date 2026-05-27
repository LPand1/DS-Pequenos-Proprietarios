<?php

header('Content-Type: application/json; charset=utf-8');

set_exception_handler(function (Throwable $e) {
    http_response_code(500);
    $debug = (getenv('APP_ENV') !== 'production');
    echo json_encode([
        'erro'    => 'Erro interno no servidor',
        'detalhe' => $debug ? $e->getMessage() : null,
    ]);
    error_log('[ROUTES ERROR] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    exit;
});

try {
    $metodo  = $_SERVER['REQUEST_METHOD'];
    $uri     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base    = rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
    $caminho = substr($uri, strlen($base));
    $partes  = explode('/', trim($caminho, '/'));

    $recurso = $partes[0] ?? '';
    $idBruto = $partes[1] ?? null;

    $id = ($idBruto !== null && $idBruto !== '')
        ? filter_var($idBruto, FILTER_VALIDATE_INT)
        : null;
    if ($id === false) {
        $id = null;
    }

    if ($recurso === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Recurso não especificado na URI']);
        exit;
    }

    $mapa = [
        'usuarios'      => UsuarioController::class,
        'proprietarios' => ProprietarioController::class,
        'inquilinos'    => InquilinoController::class,
        'propriedades'  => PropriedadeController::class,
        'gastos'        => GastoController::class,
        'arquivos'      => ArquivoController::class,
    ];

    if (!array_key_exists($recurso, $mapa)) {
        http_response_code(404);
        echo json_encode(['erro' => 'Rota não encontrada']);
        exit;
    }

    $classe = $mapa[$recurso];

    if (!class_exists($classe)) {
        http_response_code(500);
        echo json_encode(['erro' => "Controller '{$classe}' não encontrado"]);
        error_log("[ROUTES ERROR] Classe {$classe} não encontrada para o recurso '{$recurso}'");
        exit;
    }

    $controller = new $classe();

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

        case 'PUT':
        case 'PATCH':
            if ($id !== null) {
                $controller->atualizar($id);
            } else {
                http_response_code(400);
                echo json_encode(['erro' => 'ID obrigatório para atualizar']);
            }
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
            header('Allow: GET, POST, PUT, PATCH, DELETE');
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
    }

} catch (Throwable $e) {
    http_response_code(500);
    $debug = (getenv('APP_ENV') !== 'production');
    echo json_encode([
        'erro'    => 'Erro interno no servidor',
        'detalhe' => $debug ? $e->getMessage() : null,
    ]);
    error_log('[ROUTES ERROR] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    exit;
}
