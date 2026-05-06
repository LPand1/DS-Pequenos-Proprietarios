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

    public function inserir(Propriedade $propriedade) {
        $sql = $this->pdo->prepare("INSERT INTO propriedades (proprietarioId, inquilinoId, endereco, tipo, descricao, aluguel) VALUES (:proprietarioId, :inquilinoId, :endereco, :tipo, :descricao, :aluguel)");
        $sql->execute([
            ':proprietarioId' => $propriedade->getProprietarioId(),
            ':inquilinoId' => $propriedade->getInquilinoId(),
            ':endereco' => $propriedade->getEndereco(),
            ':tipo' => $propriedade->getTipo(),
            ':descricao' => $propriedade->getDescricao(),
            ':aluguel' => $propriedade->getAluguel(),
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