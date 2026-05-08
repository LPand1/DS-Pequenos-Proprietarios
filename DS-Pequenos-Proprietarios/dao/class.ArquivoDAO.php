<?php
Class ArquivoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM arquivos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; } 
    }

    public function buscarTodos() { 
        $sql = $this->pdo->prepare("SELECT * FROM arquivos");
        $sql->execute();
        $dados = $sql->fetchAll();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function inserir(Arquivo $arquivo) {
        $sql = $this->pdo->prepare("INSERT INTO arquivos(nome, path) VALUES (:nome, :path)");
        $sql->execute([':nome' => $arquivo->getNome(), ':path' => $arquivo->getPath()]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM arquivos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }
}

?>