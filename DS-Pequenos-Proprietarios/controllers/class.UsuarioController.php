<?php
Class UsuarioController {
    private $usuarioDAO;

    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function buscarTodos() {
        $dados = $this->usuarioDAO->buscarTodos();

        if (!$dados) {
            http_response_code(200);
            echo json_encode($dados ?: []);
            return;
        } else {
            http_response_code(200);
            echo json_encode($dados);
        }
    }

    public function buscarId($id) {
        if (!$id || !is_numeric($id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $dados = $this->usuarioDAO->buscarId($id);

        if (!$dados) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado']);
            return;
        }

        http_response_code(200);
        echo json_encode($dados);
    }

    public function inserir() {
        $body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['senha']) || !isset($body['cpf'])) {
            http_response_code(404);
            echo json_encode(['erro' => 'Campos "senha" e "cpf" são obrigatórios']);
            return;
        }

        $usuario = new Usuario();
        $usuario->setSenha($body['senha']);
        $usuario->setCpf($body['cpf']);

        $resultado = $this->usuarioDAO->inserir($usuario);

        if (!$resultado) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir usuário']);
            return;
        }

        http_response_code(201);
        echo json_encode(['mensagem' => 'Usuário inserido com sucesso']);
    }

    public function excluirId($id) {
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $existe = $this->usuarioDAO->buscarId($id);

        if (!$existe) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado']);
            return;
        }

        $this->usuarioDAO->excluirId($id);

        http_response_code(200);
        echo json_encode(['mensagem' => 'Usuário excluído com sucesso']);
    }
}

?>  