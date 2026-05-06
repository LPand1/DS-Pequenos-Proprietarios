<?php
Class UsuarioDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function buscarTodos() {
        $sql = "SELECT * FROM usuarios";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $dados = $stmt->fetchAll();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function inserir(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (senha, cpf) VALUES (:senha, :cpf)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':senha' => $usuario->getSenha(),
            ':cpf' => $usuario->getCpf(),
        ]);
        $dados = $stmt->fetchAll();
        if (!$dados) { return null; }
        else { return $dados; }
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM usuario WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch();
        if (!$dados) { return null; }
        else { return $dados; }
    }
}

?>