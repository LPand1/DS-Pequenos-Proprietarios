<?php
Class ArquivoController {
    private $arquivoDAO;

    public function __construct() {
        $this->arquivoDAO = new ArquivoDAO();
    }

    public function buscarTodos() {
        return $this->arquivoDAO->buscarTodos();
    }

    public function buscarPorPropriedade(int $propriedadeId) {
        return $this->arquivoDAO->buscarPorPropriedade($propriedadeId);
    }

    public function buscarId($id) {
        return $this->arquivoDAO->buscarId($id);
    }

    public function inserir($n, $p, $propriedadeId) {
        return $this->arquivoDAO->inserir($n, $p, $propriedadeId);
    }
    
    public function excluirId($id) {
        return $this->arquivoDAO->excluirId($id);
    }
}

?>
