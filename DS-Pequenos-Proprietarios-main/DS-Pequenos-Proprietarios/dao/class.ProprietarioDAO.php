<?php
Class ProprietarioDAO {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados): Proprietario {
        $proprietario = new Proprietario();
        $proprietario->setId($dados['id']);
        $proprietario->setNome($dados['nome']);
        $proprietario->setUsuarioId($dados['usuario_id']);
        return $proprietario;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?Proprietario {
        $sql = $this->pdo->prepare("SELECT * FROM proprietarios WHERE usuario_id = :usuarioId LIMIT 1");
        $sql->execute([':usuarioId' => $usuarioId]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->mapTask($dados) : null;
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM proprietarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$dados) { return null; }
        else { return $this->mapTask($dados); }
    }

    public function buscarTodos() {
        $sql = $this->pdo->prepare("SELECT * FROM proprietarios");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);    
        
        $proprietarios = [];
        foreach ($dados as $p) {
            $proprietarios[] = $this->mapTask($p);
        }

        return $proprietarios ?: null;
    }

    public function inserir($n, $ui) {
        $sql = $this->pdo->prepare("INSERT INTO proprietarios (nome, usuario_id) VALUES (:nome, :usuarioId)");
        $sql->execute([
            ':nome' => $n,
            ':usuarioId' => $ui,
        ]);
        
        $id = $this->pdo->lastInsertId();
        return $this->buscarId($id);
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM proprietarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        return ['sucesso' => true];
    }
}

?>
