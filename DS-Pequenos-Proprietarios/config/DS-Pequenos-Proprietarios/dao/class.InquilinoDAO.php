<?php
class InquilinoDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados): Inquilino {
        $inquilino = new Inquilino();

        $inquilino->setId($dados['id']);
        $inquilino->setNome($dados['nome']);
        $inquilino->setEmail($dados['email']);
        $inquilino->setUsuarioId($dados['usuario_id']);

        return $inquilino;
    }

    public function buscarId(int $id) {
        $sql = $this->pdo->prepare("SELECT * FROM inquilinos WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$dados) { return null; }
        else { return $this->mapTask($dados); }
    }

    public function buscarTodos(): ?array {
        $sql = $this->pdo->prepare("SELECT * FROM inquilinos");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $inquilinos = [];

        foreach ($inquilinos as $i) {
            $inquilinos[] = $this->mapTask($i);
        }

        if (!$inquilinos) { return null; }
        else { return $inquilinos; }
    }

    public function inserir($n, $e, $ui) {
        $sql = $this->pdo->prepare("INSERT INTO inquilinos (nome, email, usuario_id) VALUES (:nome, :email, :usuarioId)");
        $sql->execute([
            ':nome' => $n,
            ':email' => $e,
            ':usuarioId' => $ui,
        ]);

        $id = $this->pdo->lastInsertId();
        
        return $this->buscarId($id);
    }

    public function alterar() {
        
    }

    public function excluirId(int $id): array {
        $sql = $this->pdo->prepare("DELETE FROM inquilinos WHERE id = :id");
        $sql->execute([':id' => $id]);

        return ['sucesso' => true];
    }
}
?>