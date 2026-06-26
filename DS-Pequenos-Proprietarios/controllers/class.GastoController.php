<?php
Class GastoController {
    private $gastoDAO;

    public function __construct() {
        $this->gastoDAO = new GastoDAO();
    }

    public function buscarTodos() {
        return $this->gastoDAO->buscarTodos();
    }

    public function buscarPorPropriedade(int $propriedadeId) {
        return $this->gastoDAO->buscarPorPropriedade($propriedadeId);
    }

    public function buscarId($id) {
        return $this->gastoDAO->buscarId($id);
    }

    public function inserir($v, $d, $t, $pi, $desc = '', $inq = '') {
        return $this->gastoDAO->inserir($v, $d, $t, $pi, $desc, $inq);
    }

    public function atualizar($id, array $body) {
        if (!empty($body['inquilino'])) {
            $inquilino = (new InquilinoDAO())->buscarPorNome(trim($body['inquilino']));
            if (!$inquilino) {
                return ['erro' => 'Inquilino não encontrado. Use o nome cadastrado no sistema.'];
            }
            $body['inquilino'] = $inquilino->getNome();
        }
        return $this->gastoDAO->alterar((int) $id, $body);
    }
    
    public function excluirId($id) {
        return $this->gastoDAO->excluirId($id);
    }
}

?>
