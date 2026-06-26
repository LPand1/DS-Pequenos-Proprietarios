<?php
Class UsuarioDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::getConexao();
    }

    private function mapTask(array $dados): Usuario {
        $usuario = new Usuario();
        $usuario->setId($dados['id']);
        $usuario->setSenha($dados['senha']);
        $usuario->setCpf($dados['cpf']);
        return $usuario;
    }

    public function buscarPorCpf(string $cpf): ?Usuario {
        $sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE cpf = :cpf");
        $sql->execute([':cpf' => $cpf]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->mapTask($dados) : null;
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
        foreach ($dados as $u) {
            $usuarios[] = $this->mapTask($u);
        }

        return $usuarios ?: null;
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

    public function excluirId($id) {
        $sql = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $sql->execute([':id' => $id]);
        return ['sucesso' => true];
    }
}

?>
