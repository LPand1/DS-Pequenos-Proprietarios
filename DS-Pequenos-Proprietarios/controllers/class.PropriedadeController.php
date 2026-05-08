<?php
Class PropriedadeController {
    private $propriedadeDAO;

    public function __construct() {
        $this->propriedadeDAO = new PropriedadeDAO();
    }

    public function buscarTodos() {
        /*$dados = $this->propriedadeDAO->buscarTodos();

        if (!$dados) {
            http_response_code(200);
            echo json_encode($dados ?: []);
            return;
        } else {
            http_response_code(200);
            echo json_encode($dados);
        }*/

        return $this->propriedadeDAO->buscarTodos();
    }

    public function buscarId($id) {
        /*if (!$id || !is_numeric($id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $dados = $this->propriedadeDAO->buscarId($id);

        if (!$dados) {
            http_response_code(404);
            echo json_encode(['erro' => 'Propriedade não encontrada']);
            return;
        }

        http_response_code(200);
        echo json_encode($dados);*/

        return $this->propriedadeDAO->buscarId($id);
    }

     public function inserir($pi, $ii, $e, $t, $d, $a) {
        /*$body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['proprietarioId']) || !isset($body['inquilinoId']) || !isset($body['endereco'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos "proprietarioId", "inquilinoId" e "endereço" são obrigatórios']);
            return;
        }

        $propriedade = new Propriedade();
        $propriedade->setProprietarioId($body['proprietarioId']);
        $propriedade->setInquilinoId($body['inquilinoId']);
        $propriedade->setEndereco($body['endereco']);
        $propriedade->setTipo($body['tipo']);
        $propriedade->setDescricao($body['descricao']);
        $propriedade->setAluguel($body['aluguel']);

        $resultado = $this->propriedadeDAO->inserir($propriedade);

        if (!$resultado) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir propriedade.']);
            return;
        }

        http_response_code(201);
        echo json_encode(['mensagem' => 'Propriedade inserida com sucesso']);*/

        return $this->propriedadeDAO->inserir($pi, $ii, $e, $t, $d, $a);
    }
    
    public function excluirId($id) {
        /*if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $existe = $this->propriedadeDAO->buscarId($id);

        if (!$existe) {
            http_response_code(404);
            echo json_encode(['erro' => 'Propriedade não encontrada']);
            return;
        }

        $this->propriedadeDAO->excluirId($id);

        http_response_code(200);
        echo json_encode(['mensagem' => 'Propriedade excluída com sucesso']);*/

        return $this->propriedadeDAO->excluirId($id);
    }
}

?>