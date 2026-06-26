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
        $gasto->setDescricao($dados['descricao'] ?? '');
        $gasto->setInquilino($dados['inquilino'] ?? '');
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
        foreach ($dados as $g) {
            $gastos[] = $this->mapTask($g);
        }

        return $gastos ?: null;
    }

    public function buscarPorPropriedade(int $propriedadeId): ?array {
        $sql = $this->pdo->prepare("SELECT * FROM gastos WHERE propriedade_id = :propriedadeId ORDER BY data DESC");
        $sql->execute([':propriedadeId' => $propriedadeId]);
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $gastos = [];
        foreach ($dados as $g) {
            $gastos[] = $this->mapTask($g);
        }

        return $gastos ?: [];
    }

    public function inserir($v, $d, $t, $pi, $desc = '', $inq = '') {
        $sql = $this->pdo->prepare("INSERT INTO gastos (valor, data, total, propriedade_id, descricao, inquilino) VALUES (:valor, :data, :total, :propriedadeId, :descricao, :inquilino)");
        $sql->execute([
            ':valor' => $v,
            ':data' => $d ?: date('Y-m-d'),
            ':total' => $t,
            ':propriedadeId' => $pi,
            ':descricao' => $desc,
            ':inquilino' => $inq,
        ]);

        $id = $this->pdo->lastInsertId();
        return $this->buscarId((int) $id);
    }

    public function alterar(int $id, array $dados): ?Gasto {
        $sql = $this->pdo->prepare("UPDATE gastos SET valor = :valor, data = :data, total = :total, descricao = :descricao, inquilino = :inquilino WHERE id = :id");
        $sql->execute([
            ':id' => $id,
            ':valor' => $dados['valor'] ?? 0,
            ':data' => $dados['data'] ?? date('Y-m-d'),
            ':total' => $dados['total'] ?? 0,
            ':descricao' => $dados['descricao'] ?? '',
            ':inquilino' => $dados['inquilino'] ?? '',
        ]);
        return $this->buscarId($id);
    }

    public function excluirId(int $id): array {
        $sql = $this->pdo->prepare("DELETE FROM gastos WHERE id = :id");
        $sql->execute([':id' => $id]);
        return ['sucesso' => true];
    }
}
?>
