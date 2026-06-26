<?php
class AuthController {
    public function login(): void {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $cpf = preg_replace('/\D/', '', trim($body['cpf'] ?? ''));
        $senha = $body['senha'] ?? '';

        if ($cpf === '' || $senha === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'CPF e senha são obrigatórios']);
            return;
        }

        $usuario = (new UsuarioDAO())->buscarPorCpf($cpf);

        if (!$usuario || $usuario->getSenha() !== $senha) {
            http_response_code(401);
            echo json_encode(['erro' => 'Credenciais inválidas']);
            return;
        }

        $perfil = $this->montarPerfil($usuario);
        if (!$perfil) {
            http_response_code(401);
            echo json_encode(['erro' => 'Usuário sem perfil cadastrado']);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'token' => Jwt::encode(['sub' => $usuario->getId(), 'cpf' => $usuario->getCpf(), 'tipo' => $perfil['tipo']]),
            'usuario' => $perfil,
        ]);
    }

    public function cadastro(): void {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $tipo = strtolower(trim($body['tipo'] ?? ''));
        $cpf = preg_replace('/\D/', '', trim($body['cpf'] ?? ''));
        $senha = $body['senha'] ?? '';
        $nome = trim($body['nome'] ?? '');
        $email = trim($body['email'] ?? '');

        if (!in_array($tipo, ['proprietario', 'inquilino'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Tipo deve ser proprietario ou inquilino']);
            return;
        }

        if ($cpf === '' || $senha === '' || $nome === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'CPF, senha e nome são obrigatórios']);
            return;
        }

        if ($tipo === 'inquilino' && $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail é obrigatório para inquilino']);
            return;
        }

        $usuarioDAO = new UsuarioDAO();
        if ($usuarioDAO->buscarPorCpf($cpf)) {
            http_response_code(409);
            echo json_encode(['erro' => 'CPF já cadastrado']);
            return;
        }

        $usuario = $usuarioDAO->inserir($senha, $cpf);

        if ($tipo === 'proprietario') {
            (new ProprietarioDAO())->inserir($nome, $usuario->getId());
        } else {
            (new InquilinoDAO())->inserir($nome, $email, $usuario->getId());
        }

        $perfil = $this->montarPerfil($usuario);

        http_response_code(201);
        echo json_encode([
            'token' => Jwt::encode(['sub' => $usuario->getId(), 'cpf' => $usuario->getCpf(), 'tipo' => $perfil['tipo']]),
            'usuario' => $perfil,
            'mensagem' => 'Cadastro realizado com sucesso',
        ]);
    }

    private function montarPerfil(Usuario $usuario): ?array {
        $proprietario = (new ProprietarioDAO())->buscarPorUsuarioId($usuario->getId());
        if ($proprietario) {
            return [
                'id' => $usuario->getId(),
                'cpf' => $usuario->getCpf(),
                'nome' => $proprietario->getNome(),
                'tipo' => 'proprietario',
                'perfilId' => $proprietario->getId(),
            ];
        }

        $inquilino = (new InquilinoDAO())->buscarPorUsuarioId($usuario->getId());
        if ($inquilino) {
            return [
                'id' => $usuario->getId(),
                'cpf' => $usuario->getCpf(),
                'nome' => $inquilino->getNome(),
                'tipo' => 'inquilino',
                'perfilId' => $inquilino->getId(),
            ];
        }

        return null;
    }
}
