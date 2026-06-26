<?php
// config/cors.php — incluído no topo do index.php

$origem = $_SERVER['HTTP_ORIGIN'] ?? '*';

// Em produção, restrinja para o domínio real do frontend:
// $permitidos = ['https://seusite.com', 'http://localhost:5500'];
// if (!in_array($origem, $permitidos)) { http_response_code(403); exit; }

header('Access-Control-Allow-Origin: ' . $origem);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responde o preflight e encerra — nenhuma rota precisa ser processada
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
