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

        return $propriedade;
    }

    public function buscarId($id) {
        $sql = $this->pdo->prepare("SELECT * FROM propriedades WHERE id = :id");
        $sql->execute([':id' => $id]);
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$dados) { return null; }
        else { return $this->mapTask($dados); }
    }

    public function buscarTodos(): ?array {
        $sql = $this->pdo->prepare("SELECT * FROM propriedades");
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);

        $propriedades = [];
        
        foreach ($dados as $d) {
            $propriedades[] = $this->mapTask($d);
        }

        if (!$propriedades) { return null; }
        else { return $propriedades; }
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

        $id = $this->pdo->lastInsertId();
        return $this->buscarId($id);
    }

    public function alterar() {
        
    }

    public function excluirId($id): array {
        $sql = $this->pdo->prepare("DELETE FROM propriedades WHERE id = :id");
        $sql->execute([':id' => $id]);
        return ['sucesso' => true];
    }
}
?>