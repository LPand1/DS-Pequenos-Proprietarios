<?php
Class ProprietarioController {
    private $proprietarioDAO;

    public function __construct() {
        $this->proprietarioDAO = new ProprietarioDAO();
    }

    public function buscarTodos() {
        $dados = $this->proprietarioDAO->buscarTodos();

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

        $dados = $this->proprietarioDAO->buscarId($id);

        if (!$dados) {
            http_response_code(404);
            echo json_encode(['erro' => 'Proprietario não encontrado']);
            return;
        }

        http_response_code(200);
        echo json_encode($dados);
    }

     public function inserir() {
        $body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['nome']) || !isset($body['usuarioId'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos "nome" e "usuarioId" são obrigatórios']);
            return;
        }

        $proprietario = new Proprietario();
        $proprietario->setNome($body['nome']);
        $proprietario->setUsuarioId($body['usuarioId']);

        $resultado = $this->proprietarioDAO->inserir($proprietario);

        if (!$resultado) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir proprietario']);
            return;
        }

        http_response_code(201);
        echo json_encode(['mensagem' => 'Proprietario inserido com sucesso']);
    }
    
    public function excluirId($id) {
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $existe = $this->proprietarioDAO->buscarId($id);

        if (!$existe) {
            http_response_code(404);
            echo json_encode(['erro' => 'Proprietario não encontrado']);
            return;
        }

        $this->proprietarioDAO->excluirId($id);

        http_response_code(200);
        echo json_encode(['mensagem' => 'Proprietario excluído com sucesso']);
    }
}

?>