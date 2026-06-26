<?php
Class PropriedadeController {
    private $propriedadeDAO;
    private const FOTO_MAX_BYTES = 5 * 1024 * 1024;
    private const FOTO_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct() {
        $this->propriedadeDAO = new PropriedadeDAO();
    }

    private function diretorioUploads(): string {
        return dirname(__DIR__) . '/uploads/propriedades';
    }

    private function caminhoAbsoluto(?string $fotoPath): ?string {
        if (!$fotoPath) {
            return null;
        }
        return dirname(__DIR__) . '/' . ltrim($fotoPath, '/');
    }

    private function removerArquivoFoto(?string $fotoPath): void {
        $caminho = $this->caminhoAbsoluto($fotoPath);
        if ($caminho && is_file($caminho)) {
            unlink($caminho);
        }
    }

    private function verificarProprietarioDoImovel(int $usuarioId, int $propriedadeId): array {
        $proprietario = (new ProprietarioDAO())->buscarPorUsuarioId($usuarioId);
        if (!$proprietario) {
            return ['erro' => 'Apenas proprietários podem alterar fotos'];
        }

        $propriedade = $this->propriedadeDAO->buscarId($propriedadeId);
        if (!$propriedade) {
            return ['erro' => 'Imóvel não encontrado'];
        }

        if ($propriedade->getProprietarioId() !== $proprietario->getId()) {
            return ['erro' => 'Sem permissão para este imóvel'];
        }

        return ['propriedade' => $propriedade];
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

    public function uploadFoto(int $id, int $usuarioId) {
        $verificacao = $this->verificarProprietarioDoImovel($usuarioId, $id);
        if (isset($verificacao['erro'])) {
            return $verificacao;
        }

        $propriedade = $verificacao['propriedade'];

        if (!isset($_FILES['foto']) || !is_uploaded_file($_FILES['foto']['tmp_name'])) {
            return ['erro' => 'Nenhuma foto enviada'];
        }

        $arquivo = $_FILES['foto'];
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return ['erro' => 'Erro ao enviar a foto'];
        }

        if ($arquivo['size'] > self::FOTO_MAX_BYTES) {
            return ['erro' => 'A foto deve ter no máximo 5 MB'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);

        if (!isset(self::FOTO_MIMES[$mime])) {
            return ['erro' => 'Formato inválido. Use JPG, PNG ou WebP'];
        }

        $ext = self::FOTO_MIMES[$mime];
        $dir = $this->diretorioUploads();
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return ['erro' => 'Não foi possível criar a pasta de uploads'];
        }

        $nomeArquivo = $id . '.' . $ext;
        $destino = $dir . '/' . $nomeArquivo;
        $fotoPath = 'uploads/propriedades/' . $nomeArquivo;

        $this->removerArquivoFoto($propriedade->getFotoPath());

        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            return ['erro' => 'Não foi possível salvar a foto'];
        }

        return $this->propriedadeDAO->atualizarFotoPath($id, $fotoPath);
    }

    public function excluirId($id) {
        $propriedade = $this->propriedadeDAO->buscarId($id);
        if ($propriedade) {
            $this->removerArquivoFoto($propriedade->getFotoPath());
        }
        return $this->propriedadeDAO->excluirId($id);
    }
}

?>
