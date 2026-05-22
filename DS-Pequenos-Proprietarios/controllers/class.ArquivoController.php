<?php
Class ArquivoController {
    private $arquivoDAO;

    public function __construct() {
        $this->arquivoDAO = new ArquivoDAO();
    }

    public function buscarTodos() {
        /*$dados = $this->arquivoDAO->buscarTodos();

        if (!$dados) {
            http_response_code(200);
            echo json_encode($dados ?: []);
            return;
        } else {
            http_response_code(200);
            echo json_encode($dados);
        }*/

        return $this->arquivoDAO->buscarTodos();
    }

    public function buscarId($id) {
        /*if (!$id || !is_numeric($id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $dados = $this->arquivoDAO->buscarId($id);

        if (!$dados) {
            http_response_code(404);
            echo json_encode(['erro' => 'Arquivo não encontrado']);
            return;
        }

        http_response_code(200);
        echo json_encode($dados);*/

        return $this->arquivoDAO->buscarId($id);
    }

    public function inserir($n, $p) {
        /*$body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['nome']) || !isset($body['path'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos "nome" e "path" são obrigatórios']);
            return;
        }

        $arquivo = new Arquivo();
        $arquivo->setNome($body['nome']);
        $arquivo->setPath($body['path']);

        $resultado = $this->arquivoDAO->inserir($arquivo);

        if (!$resultado) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir arquivo']);
            return;
        }

        http_response_code(201);
        echo json_encode(['mensagem' => 'Arquivo inserido com sucesso']);*/

        return $this->arquivoDAO->insert($n, $p);
    }
    
    public function excluirId($id) {
        /*if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $existe = $this->arquivoDAO->buscarId($id);

        if (!$existe) {
            http_response_code(404);
            echo json_encode(['erro' => 'Arquivo não encontrado']);
            return;
        }

        $this->arquivoDAO->excluirId($id);

        http_response_code(200);
        echo json_encode(['mensagem' => 'Arquivo excluído com sucesso']);*/

        return $this->arquivoDAO->excluirId($id);
    }
}

?>