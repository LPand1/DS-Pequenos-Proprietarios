<?php
class InquilinoController {
    private $inquilinoDAO;

    public function __construct() {
        $this->inquilinoDAO = new InquilinoDAO();
    }

    public function buscarTodos() {
        $dados = $this->inquilinoDAO->buscarTodos();

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

        $dados = $this->inquilinoDAO->buscarId($id);

        if (!$dados) {
            http_response_code(404);
            echo json_encode(['erro' => 'Inquilino não encontrado']);
            return;
        }

        http_response_code(200);
        echo json_encode($dados);
    }

     public function inserir() {
        $body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['nome']) || !isset($body['email']) || !isset($body['usuarioId'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos "nome", "email" e "usuarioId" são obrigatórios']);
            return;
        }

        $inquilino = new Inquilino();
        $inquilino->setNome($body['nome']);
        $inquilino->setEmail($body['email']);
        $inquilino->setUsuarioId($body['usuarioId']);

        $resultado = $this->inquilinoDAO->inserir($inquilino);

        if (!$resultado) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir inquilino']);
            return;
        }

        http_response_code(201);
        echo json_encode(['mensagem' => 'Inquilino inserido com sucesso']);
    }
    
    public function excluirId($id) {
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $existe = $this->inquilinoDAO->buscarId($id);

        if (!$existe) {
            http_response_code(404);
            echo json_encode(['erro' => 'Inquilino não encontrado']);
            return;
        }

        $this->inquilinoDAO->excluirId($id);

        http_response_code(200);
        echo json_encode(['mensagem' => 'Inquilino excluído com sucesso']);
    }
}

?>