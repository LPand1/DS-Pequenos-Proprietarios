<?php
Class PropriedadeController {
    private $propriedadeDAO;

    public function __construct() {
        $this->propriedadeDAO = new PropriedadeDAO();
    }

    public function buscarPorUsuario(int $usuarioId) {
        $proprietario = (new ProprietarioDAO())->buscarPorUsuarioId($usuarioId);
        if ($proprietario) {
            return $this->propriedadeDAO->buscarPorProprietarioId($proprietario->getId());
        }

        $inquilino = (new InquilinoDAO())->buscarPorUsuarioId($usuarioId);
        if ($inquilino) {
            return $this->propriedadeDAO->buscarPorInquilinoId($inquilino->getId());
        }

        return [];
    }

    public function buscarTodos() {
        return $this->propriedadeDAO->buscarTodos();
    }

    public function buscarId($id) {
        return $this->propriedadeDAO->buscarComInquilino($id);
    }

    public function inserirParaProprietario(int $usuarioId, array $body) {
        $proprietario = (new ProprietarioDAO())->buscarPorUsuarioId($usuarioId);
        if (!$proprietario) {
            return ['erro' => 'Apenas proprietários podem cadastrar imóveis'];
        }

        $inquilinoNome = trim($body['inquilinoNome'] ?? '');
        if ($inquilinoNome === '') {
            return ['erro' => 'Informe o nome do inquilino'];
        }

        $inquilino = (new InquilinoDAO())->buscarPorNome($inquilinoNome);
        if (!$inquilino) {
            return ['erro' => 'Inquilino não encontrado. Peça para ele se cadastrar no sistema.'];
        }

        return $this->propriedadeDAO->inserir(
            $proprietario->getId(),
            $inquilino->getId(),
            $body['endereco'] ?? '',
            (int) ($body['tipo'] ?? 1),
            $body['descricao'] ?? '',
            (float) ($body['aluguel'] ?? 0)
        );
    }

    public function inserir($pi, $ii, $e, $t, $d, $a) {
        return $this->propriedadeDAO->inserir($pi, $ii, $e, $t, $d, $a);
    }
    
    public function atualizar($id, array $body) {
        if (!empty($body['inquilinoNome'])) {
            $inquilino = (new InquilinoDAO())->buscarPorNome(trim($body['inquilinoNome']));
            if (!$inquilino) {
                return ['erro' => 'Inquilino não encontrado'];
            }
            $body['inquilinoId'] = $inquilino->getId();
        }
        return $this->propriedadeDAO->alterar((int) $id, $body);
    }

    public function excluirId($id) {
        return $this->propriedadeDAO->excluirId($id);
    }
}

?>
