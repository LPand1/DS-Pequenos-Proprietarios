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

function responder($dados, int $codigo = 200): void {
    http_response_code($codigo);
    echo json_encode($dados ?? [], JSON_UNESCAPED_UNICODE);
    exit;
}

function obterBearerToken(): ?string {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s(\S+)/', $header, $m)) {
        return $m[1];
    }
    return null;
}

function exigirJwt(): array {
    $token = obterBearerToken();
    if (!$token) {
        responder(['erro' => 'Token não informado'], 401);
    }
    try {
        return Jwt::decode($token);
    } catch (Throwable $e) {
        responder(['erro' => 'Token inválido ou expirado'], 401);
    }
    return [];
}

function corpoJson(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

try {
    $metodo  = $_SERVER['REQUEST_METHOD'];
    $uri     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base    = rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
    $caminho = substr($uri, strlen($base));
    $caminho = preg_replace('#^/index\.php#', '', $caminho);
    $partes  = explode('/', trim($caminho, '/'));

    // Ex: /arquivos/42/download  → $partes = ['arquivos', '42', 'download']
    // Ex: /arquivos/upload       → $partes = ['arquivos', 'upload']
    $recurso    = $partes[0] ?? '';
    $parte1     = $partes[1] ?? '';   // pode ser um ID numérico ou palavra-chave
    $subRecurso = $partes[2] ?? '';   // 'download', 'foto', etc.

    // Parse do ID somente se $parte1 for numérico
    $id = ($parte1 !== '' && ctype_digit($parte1))
        ? (int) $parte1
        : null;

    // ── Rotas públicas ─────────────────────────────────────────────────────────
    if ($recurso === 'login' && $metodo === 'POST') {
        (new AuthController())->login();
        exit;
    }
    if ($recurso === 'cadastro' && $metodo === 'POST') {
        (new AuthController())->cadastro();
        exit;
    }
    if ($recurso === '') {
        responder(['erro' => 'Recurso não especificado na URI'], 400);
    }

    // ── Todas as demais rotas exigem JWT ───────────────────────────────────────
    $jwt       = exigirJwt();
    $usuarioId = (int) ($jwt['sub'] ?? 0);

    // ── POST /propriedades/{id}/foto ───────────────────────────────────────────
    if ($recurso === 'propriedades' && $id !== null && $subRecurso === 'foto' && $metodo === 'POST') {
        $resultado = (new PropriedadeController())->uploadFoto($id, $usuarioId);
        if (is_array($resultado) && isset($resultado['erro'])) {
            responder($resultado, 400);
        }
        responder($resultado ?? ['sucesso' => true]);
    }

    // ── POST /arquivos/upload ──────────────────────────────────────────────────
    if ($recurso === 'arquivos' && $parte1 === 'upload' && $metodo === 'POST') {
        $resultado = (new ArquivoController())->uploadPdf();
        if (is_array($resultado) && isset($resultado['erro'])) {
            responder($resultado, 400);
        }
        responder($resultado, 201);
    }

    // ── GET /arquivos/{id}/download ────────────────────────────────────────────
    // Serve o binário PDF — deve ficar antes do switch genérico
    if ($recurso === 'arquivos' && $id !== null && $subRecurso === 'download' && $metodo === 'GET') {
        (new ArquivoController())->download($id);
        // download() chama exit() internamente
    }

    // ── Roteamento genérico ────────────────────────────────────────────────────
    $mapa = [
        'usuarios'      => UsuarioController::class,
        'proprietarios' => ProprietarioController::class,
        'inquilinos'    => InquilinoController::class,
        'propriedades'  => PropriedadeController::class,
        'gastos'        => GastoController::class,
        'arquivos'      => ArquivoController::class,
    ];

    if (!array_key_exists($recurso, $mapa)) {
        responder(['erro' => 'Rota não encontrada'], 404);
    }

    $classe = $mapa[$recurso];
    if (!class_exists($classe)) {
        error_log("[ROUTES ERROR] Classe {$classe} não encontrada para o recurso '{$recurso}'");
        responder(['erro' => "Controller '{$classe}' não encontrado"], 500);
    }

    $controller = new $classe();
    $body = corpoJson();

    switch ($metodo) {
        case 'GET':
            if ($recurso === 'gastos' && isset($_GET['propriedade_id'])) {
                responder($controller->buscarPorPropriedade((int) $_GET['propriedade_id']) ?? []);
            }
            if ($recurso === 'arquivos' && isset($_GET['propriedade_id'])) {
                responder($controller->buscarPorPropriedade((int) $_GET['propriedade_id']) ?? []);
            }
            if ($recurso === 'propriedades' && $id === null) {
                responder($controller->buscarPorUsuario($usuarioId) ?? []);
            }
            if ($id !== null) {
                $resultado = $controller->buscarId($id);
                if ($resultado === null) {
                    responder(['erro' => 'Registro não encontrado'], 404);
                }
                responder($resultado);
            }
            responder($controller->buscarTodos() ?? []);
            break;

        case 'POST':
            $resultado = match ($recurso) {
                'proprietarios' => $controller->inserir($body['nome'] ?? '', (int) ($body['usuarioId'] ?? 0)),
                'inquilinos'    => $controller->inserir($body['nome'] ?? '', $body['email'] ?? '', (int) ($body['usuarioId'] ?? 0)),
                'propriedades'  => $controller->inserirParaProprietario($usuarioId, $body),
                'gastos'        => $controller->inserir(
                    (float) ($body['valor'] ?? 0),
                    $body['data'] ?? '',
                    (float) ($body['total'] ?? 0),
                    (int) ($body['propriedadeId'] ?? 0),
                    $body['descricao'] ?? '',
                    $body['inquilino'] ?? ''
                ),
                'arquivos' => $controller->inserir(
                    $body['nome'] ?? '',
                    $body['path'] ?? '',
                    (int) ($body['propriedadeId'] ?? 0)
                ),
                default => null,
            };
            if (is_array($resultado) && isset($resultado['erro'])) {
                responder($resultado, 400);
            }
            responder($resultado, 201);
            break;

        case 'PUT':
        case 'PATCH':
            if ($id === null) {
                responder(['erro' => 'ID obrigatório para atualizar'], 400);
            }
            if (!method_exists($controller, 'atualizar')) {
                responder(['erro' => 'Atualização não suportada para este recurso'], 405);
            }
            $resultado = $controller->atualizar($id, $body);
            if (is_array($resultado) && isset($resultado['erro'])) {
                responder($resultado, 400);
            }
            responder($resultado ?? ['sucesso' => true]);
            break;

        case 'DELETE':
            if ($id === null) {
                responder(['erro' => 'ID obrigatório para excluir'], 400);
            }
            responder($controller->excluirId($id));
            break;

        default:
            header('Allow: GET, POST, PUT, PATCH, DELETE');
            responder(['erro' => 'Método não permitido'], 405);
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