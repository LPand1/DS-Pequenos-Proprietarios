<?php
class ArquivoDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados): Arquivo {
        $arquivo = new Arquivo();
        $arquivo->setId($dados['id']);
        $arquivo->setNome($dados['nome']);
        $arquivo->setPath($dados['path']);
        $arquivo->setPropriedadeId($dados['propriedade_id']);
        return $arquivo;
    }

    public function buscarId(int $id) {
        $sql = $this->pdo->prepare("SELECT * FROM arquivos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->mapTask($dados) : null;
    }

    public function buscarPorPropriedade(int $propriedadeId): array {
        $sql = $this->pdo->prepare("SELECT * FROM arquivos WHERE propriedade_id = :propriedadeId ORDER BY id DESC");
        $sql->execute([':propriedadeId' => $propriedadeId]);
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $arquivos = [];
        foreach ($dados as $a) {
            $arquivos[] = $this->mapTask($a);
        }
        return $arquivos;
    }

    public function buscarTodos(): ?array {
        $sql = $this->pdo->prepare("SELECT * FROM arquivos");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $arquivos = [];
        foreach ($dados as $a) {
            $arquivos[] = $this->mapTask($a);
        }
        return $arquivos ?: null;
    }

    public function inserir($n, $p, $propriedadeId) {
        $sql = $this->pdo->prepare("INSERT INTO arquivos (nome, path, propriedade_id) VALUES (:nome, :path, :propriedadeId)");
        $sql->execute([
            ':nome' => $n,
            ':path' => $p,
            ':propriedadeId' => $propriedadeId,
        ]);
        return $this->buscarId((int) $this->pdo->lastInsertId());
    }

    public function excluirId(int $id): array {
        $sql = $this->pdo->prepare("DELETE FROM arquivos WHERE id = :id");
        $sql->execute([':id' => $id]);
        return ['sucesso' => true];
    }
}
?>
