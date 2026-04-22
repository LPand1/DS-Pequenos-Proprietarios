<?php
Class GastroController {
    private $gastoDAO;

    public function __construct() {
        $this->gastoDAO = new GastoDAO();
    }

    public function buscarTodos() {
        $dados = $this->gastoDAO->buscarTodos();

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

        $dados = $this->gastoDAO->buscarId($id);

        if (!$dados) {
            http_response_code(404);
            echo json_encode(['erro' => 'Gasto não encontrado']);
            return;
        }

        http_response_code(200);
        echo json_encode($dados);
    }

     public function inserir() {
        $body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['valor']) || !isset($body['data'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos "valor" e "data" são obrigatórios']);
            return;
        }

        $gasto = new Gasto();
        $gasto->setValor($body['valor']);
        $gasto->setData($body['data']);
        $gasto->setTotal($body['total']);
        $gasto->setPropriedadeId($body['propriedadeId']);

        $resultado = $this->gastoDAO->inserir($gasto);

        if (!$resultado) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir gasto']);
            return;
        }

        http_response_code(201);
        echo json_encode(['mensagem' => 'Gasto inserido com sucesso']);
    }
    
    public function excluirId($id) {
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $existe = $this->gastoDAO->buscarId($id);

        if (!$existe) {
            http_response_code(404);
            echo json_encode(['erro' => 'Gasto não encontrado']);
            return;
        }

        $this->gastoDAO->excluirId($id);

        http_response_code(200);
        echo json_encode(['mensagem' => 'Gasto excluído com sucesso']);
    }
}

?>