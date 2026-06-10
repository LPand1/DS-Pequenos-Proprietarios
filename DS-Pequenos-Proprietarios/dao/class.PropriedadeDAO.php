<?php
class PropriedadeDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados): Propriedade {
        $propriedade = new Propriedade();
        $propriedade->setId($dados['id']);
        $propriedade->setProprietarioId($dados['proprietario_id']);
        $propriedade->setInquilinoId($dados['inquilino_id']);
        $propriedade->setEndereco($dados['endereco']);
        $propriedade->setTipo($dados['tipo']);
        $propriedade->setDescricao($dados['descricao']);
        $propriedade->setAluguel($dados['aluguel']);
        if (isset($dados['inquilino_nome'])) {
            $propriedade->setInquilinoNome($dados['inquilino_nome']);
        }
        return $propriedade;
    }

    private function sqlComInquilino(): string {
        return "SELECT p.*, i.nome AS inquilino_nome
                FROM propriedades p
                INNER JOIN inquilinos i ON i.id = p.inquilino_id";
    }

    public function buscarComInquilino($id) {
        $sql = $this->pdo->prepare($this->sqlComInquilino() . " WHERE p.id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->mapTask($dados) : null;
    }

    public function buscarId($id) {
        return $this->buscarComInquilino($id);
    }

    public function buscarPorProprietarioId(int $proprietarioId): array {
        $sql = $this->pdo->prepare($this->sqlComInquilino() . " WHERE p.proprietario_id = :proprietarioId");
        $sql->execute([':proprietarioId' => $proprietarioId]);
        return $this->mapearLista($sql->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPorInquilinoId(int $inquilinoId): array {
        $sql = $this->pdo->prepare($this->sqlComInquilino() . " WHERE p.inquilino_id = :inquilinoId");
        $sql->execute([':inquilinoId' => $inquilinoId]);
        return $this->mapearLista($sql->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarTodos(): ?array {
        $sql = $this->pdo->prepare($this->sqlComInquilino());
        $sql->execute();
        $lista = $this->mapearLista($sql->fetchAll(PDO::FETCH_ASSOC));
        return $lista ?: null;
    }

    private function mapearLista(array $dados): array {
        $propriedades = [];
        foreach ($dados as $d) {
            $propriedades[] = $this->mapTask($d);
        }
        return $propriedades;
    }

    public function inserir($pi, $ii, $e, $t, $d, $a) {
        $sql = $this->pdo->prepare("INSERT INTO propriedades (proprietario_id, inquilino_id, endereco, tipo, descricao, aluguel) VALUES (:proprietarioId, :inquilinoId, :endereco, :tipo, :descricao, :aluguel)");
        $sql->execute([
            ':proprietarioId' => $pi,
            ':inquilinoId' => $ii,
            ':endereco' => $e,
            ':tipo' => $t,
            ':descricao' => $d,
            ':aluguel' => $a,
        ]);
        return $this->buscarComInquilino($this->pdo->lastInsertId());
    }

    public function alterar(int $id, array $dados): ?Propriedade {
        $atual = $this->buscarId($id);
        if (!$atual) { return null; }

        $sql = $this->pdo->prepare("UPDATE propriedades SET inquilino_id = :inquilinoId, endereco = :endereco, tipo = :tipo, descricao = :descricao, aluguel = :aluguel WHERE id = :id");
        $sql->execute([
            ':id' => $id,
            ':inquilinoId' => $dados['inquilinoId'] ?? $atual->getInquilinoId(),
            ':endereco' => $dados['endereco'] ?? $atual->getEndereco(),
            ':tipo' => $dados['tipo'] ?? $atual->getTipo(),
            ':descricao' => $dados['descricao'] ?? $atual->getDescricao(),
            ':aluguel' => $dados['aluguel'] ?? $atual->getAluguel(),
        ]);
        return $this->buscarComInquilino($id);
    }

    public function excluirId($id): array {
        $sql = $this->pdo->prepare("DELETE FROM propriedades WHERE id = :id");
        $sql->execute([':id' => $id]);
        return ['sucesso' => true];
    }
}
?>
