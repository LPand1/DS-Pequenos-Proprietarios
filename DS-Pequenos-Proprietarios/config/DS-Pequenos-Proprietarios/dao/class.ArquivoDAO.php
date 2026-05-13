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

        return $arquivo;
    }

    public function buscarId(int $id) {
        $sql = $this->pdo->prepare("SELECT * FROM arquivos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$dados) { return null; }
        else { return $this->mapTask($dados); }
    }

    public function buscarTodos(): ?array {
        $sql = $this->pdo->prepare("SELECT * FROM arquivos");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $arquivos = [];

        foreach ($arquivos as $a) {
            $arquivos[] = $this->mapTask($a);
        }

        if (!$arquivos) { return null; }
        else { return $arquivos; }
    }

    public function inserir($n, $p) {
        $sql = $this->pdo->prepare("INSERT INTO arquivos (nome, path) VALUES (:nome, :path)");
        $sql->execute([
            ':nome' => $n,
            ':path' => $p,
        ]);

        $id = $this->pdo->lastInsertId();
        return $this->buscarId($id);
    }

    public function excluirId(int $id): array {
        $sql = $this->pdo->prepare("DELETE FROM arquivos WHERE id = :id");
        $sql->execute([':id' => $id]);

        return ['sucesso' => true];
    }
}
?>