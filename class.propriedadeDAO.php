<?php
Class PropriedadeDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM propriedades WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }        
    }

    public function buscarTodos() {
        $sql = $this->pdo->prepare("SELECT * FROM propriedades");
        $dados = $sql->execute();
        $dados = $sql->fetchAll();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function inserir() {
        $sql = $this->pdo->prepare("INSERT INTO propriedades (proprietarioId, inquilinoId, endereco, tipo, descricao, aluguel) VALUES (:proprietarioId, :inquilinoId, :endereco, :tipo, :descricao, :aluguel)");
        $sql->execute([
            ':proprietarioId' => $this->proprietarioId,
            ':inquilinoId' => $this->inquilinoId,
            ':endereco' => $this->endereco,
            ':tipo' => $this->tipo,
            ':descricao' => $this->descricao,
            ':aluguel' => $this->aluguel,
        ]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM propriedades WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }
}

?>