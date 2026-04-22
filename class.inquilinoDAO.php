<?php
Class InquilinoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM inquilinos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }        
    }

    public function buscarTodos() {
        $sql = $this->pdo->prepare("SELECT * FROM inquilinos");
        $dados = $sql->execute();
        $dados = $sql->fetchAll();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function inserir() {
        $sql = $this->pdo->prepare("INSERT INTO inquilinos (nome, email, usuarioId) VALUES (:nome, :email, :usuarioId)");
        $sql->execute([
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':usuarioId' => $this->usuarioId,
        ]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM inquilinos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }
}

?>