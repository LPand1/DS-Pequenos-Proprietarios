<?php
Class GastoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM gastos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function buscarTodos() {
        $sql = $this->pdo->prepare("SELECT * FROM gastos");
        $sql->execute();
        $dados = $sql->fetchAll();
        if (!$dados) { return null; }
        else { return $dados; }
    } 

    public function inserir(Gasto $gasto) {
        $sql = $this->pdo->prepare("INSERT INTO gastos (valor, data, total, propriedade_id
        VALUES (:valor, :data, :total, :propriedade_id))");
        $sql->execute([
            ':valor' => $gasto->getValor(),
            ':data' => $gasto->getData(),
            ':total' => $gasto->getTotal(),
            ':propriedade_id' => $gasto->getPropriedadeId(),
        ]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM gastos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }
}

?>