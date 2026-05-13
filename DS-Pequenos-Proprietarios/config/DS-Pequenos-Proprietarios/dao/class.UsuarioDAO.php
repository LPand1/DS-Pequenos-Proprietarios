<?php
Class UsuarioDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados) : Usuario {
        $usuario = new Usuario();

        $usuario->setId($dados['id']);
        $usuario->setSenha($dados['senha']);
        $usuario->setCpf($dados['cpf']);

        return $usuario;
    }
    
    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$dados) { return null; }
        else { return $this->mapTask($dados); }
    }

    public function buscarTodos() {
        $sql = $this->pdo->prepare("SELECT * FROM usuarios");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $usuarios = [];

        foreach($usuarios as $u) {
            $usuarios[] = $this->mapTask[$u];
        }

        if (!$usuarios) { return null; }
        else { return $usuarios; }
    }

    public function inserir($senha, $cpf) {
        $sql = $this->pdo->prepare("INSERT INTO usuarios (senha, cpf) VALUES (:senha, :cpf)");
        $sql->execute([
            ':senha' => $senha,
            ':cpf' => $cpf,
        ]);
        
        $id = $this->pdo->lastInsertId();

        return $this->buscarId($id);
    }

    public function alterar($id, $senha, $cpf) {
        $sql = $this->pdo->prepare("UPDATE usuarios SET (senha=:senha, cpf=:cpf)");
        $sql->execute([
            ':id' => $id,
            ':senha' => $senha,
            ':cpf' => $cpf,
        ]);
        
        return ['sucesso' => true];
    }

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM usuario WHERE id = :id");
        $sql->execute([':id' => $id]);
        
        return ['sucesso' => true];
    }

    public function excluirTodos() {
        $this->pdo->execute("TRUNCATE TABLE arquivos;");
        $this->pdo->execute("TRUNCATE TABLE gastos;");
        $this->pdo->execute("TRUNCATE TABLE propriedades;");
        $this->pdo->execute("TRUNCATE TABLE inquilinos;");
        $this->pdo->execute("TRUNCATE TABLE proprietarios;");
        $this->pdo->execute("TRUNCATE TABLE usuarios;");

        return ['sucesso' => true];
    }
}

?>