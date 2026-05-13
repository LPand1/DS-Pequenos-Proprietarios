<?php
class GastoDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados): Gasto {
        $gasto = new Gasto();

        $gasto->setId($dados['id']);
        $gasto->setValor($dados['valor']);
        $gasto->setData($dados['data']);
        $gasto->setTotal($dados['total']);
        $gasto->setPropriedadeId($dados['propriedade_id']);

        return $gasto;
    }

    public function buscarId(int $id) {
        $sql = $this->pdo->prepare("SELECT * FROM gastos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$dados) { return null; }
        else { return $this->mapTask($dados); }
    }

    public function buscarTodos(): ?array {
        $sql = $this->pdo->prepare("SELECT * FROM gastos");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $gastos = [];

        foreach ($gastos as $g) {
            $gastos[] = $this->mapTask($g);
        }

        if (!$gastos) { return null; }
        else { return $gastos; }
    }

    public function inserir($v, $d, $t, $pi) {
        $sql = $this->pdo->prepare("INSERT INTO gastos (valor, data, total, propriedade_id) VALUES (:valor, :data, :total, :propriedadeId)");
        $sql->execute([
            ':valor' => $v,
            ':data' => $d,
            ':total' => $t,
            ':propriedadeId' => $pi,
        ]);

        $id = $this->pdo->lastInsertId();
        return $this->buscarId($id);
    }

    public function alterar() {
        
    }

    public function excluirId(int $id): array {
        $sql = $this->pdo->prepare("DELETE FROM gastos WHERE id = :id");
        $sql->execute([':id' => $id]);

        return ['sucesso' => true];
    }
}
?>