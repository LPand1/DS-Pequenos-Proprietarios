<?php
Class ProprietarioDAO {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados) : Proprietario {
        $proprietario = new Proprietario();

        $proprietario->setId($data['id']);
        $proprietario->setNome($data['nome']);
        $proprietario->setUsuarioId($data['usuario_id']);

        return $proprietario;
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
        $dados = $sql->fetchAll();    
        
        $proprietarios = [];

        foreach($proprietarios as $p) {
            $proprietarios[] = $this->mapTask[$d];
        }

        if (!$proprietarios) { return $null; }
        else { return $proprietarios; }
    }

    public function inserir($n, $ui) {
        $sql = $this->pdo->prepare("INSERT INTO proprietarios (nome, usuario_id) VALUES (:nome, :usuarioId)");
        $sql->execute([
            ':nome' => $n,
            ':usuarioId' => $ui,
        ]);
        
        $id = $this->pdo->lastInsertId();

        return $this->getId($id);
    }

    public function alterar($n, $ui) {
        $sql = $this->pdo->prepare("UPDATE proprietarios SET (nome=:nome, usuario_id=:usuarioId)");
        $sql->execute([
            ':nome' => $n,
            ':usuarioId' => $ui,
        ]);

        return [
            'sucesso' => true,
        ];
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM proprietarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        
        return [
            'sucesso' => true,
        ];
    }
}

?>