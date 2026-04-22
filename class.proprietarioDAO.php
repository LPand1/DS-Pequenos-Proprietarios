<?php
Class Proprietario {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    public function buscarId($id) {
        $sql = "SELECT * FROM proprietarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function buscarTodos() {
        $sql = $this->pdo->prepare("SELECT * FROM proprietarios");
        $sql->execute();
        $dados = $sql->fetchAll();    
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function inserir(Proprietario $proprietario) {
        $sql = $this->pdo->prepare("INSERT INTO proprietarios (nome, usuario_id) VALUES (:nome, :usuario_id)");
        $sql->execute([
            'nome' => $proprietario->getNome(),
            'usuario_id' => $proprietario->getUsuarioId(),
        ]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM proprietarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }
}

?>