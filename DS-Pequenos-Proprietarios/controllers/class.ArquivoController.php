<?php
Class ArquivoController {
    private $arquivoDAO;
    private const PDF_MAX_BYTES = 20 * 1024 * 1024;

    public function __construct() {
        $this->arquivoDAO = new ArquivoDAO();
    }

    private function raizApp(): string {
        // dirname(__DIR__) sobe um nível a partir da pasta do controller
        // Ajuste se a estrutura de pastas for diferente
        return dirname(__DIR__);
    }

    private function caminhoAbsoluto(?string $path): ?string {
        if (!$path) return null;
        return $this->raizApp() . '/' . ltrim($path, '/');
    }

    public function buscarTodos() {
        return $this->arquivoDAO->buscarTodos();
    }

    public function buscarPorPropriedade(int $propriedadeId) {
        return $this->arquivoDAO->buscarPorPropriedade($propriedadeId);
    }

    public function buscarId($id) {
        return $this->arquivoDAO->buscarId($id);
    }

    /** Inserção legada via JSON body. */
    public function inserir($n, $p, $propriedadeId) {
        return $this->arquivoDAO->inserir($n, $p, $propriedadeId);
    }

    /** Recebe multipart: $_FILES['pdf'], $_POST['nome'], $_POST['propriedadeId'] */
    public function uploadPdf() {
        $propriedadeId = (int) ($_POST['propriedadeId'] ?? 0);
        $nome          = trim($_POST['nome'] ?? '');

        if ($propriedadeId <= 0) return ['erro' => 'propriedadeId inválido'];
        if ($nome === '')        return ['erro' => 'Informe um nome para o arquivo'];

        if (empty($_FILES['pdf']['tmp_name']) || !is_uploaded_file($_FILES['pdf']['tmp_name'])) {
            return ['erro' => 'Nenhum PDF enviado'];
        }

        $arquivo = $_FILES['pdf'];

        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return ['erro' => 'Erro no upload (código ' . $arquivo['error'] . ')'];
        }
        if ($arquivo['size'] > self::PDF_MAX_BYTES) {
            return ['erro' => 'O PDF deve ter no máximo 20 MB'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            return ['erro' => 'Apenas arquivos PDF são aceitos (detectado: ' . $mime . ')'];
        }

        $dir = $this->raizApp() . '/uploads/arquivos';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return ['erro' => 'Não foi possível criar a pasta de uploads'];
        }

        $nomeArquivo = $propriedadeId . '_' . uniqid() . '.pdf';
        $destino     = $dir . '/' . $nomeArquivo;
        $path        = 'uploads/arquivos/' . $nomeArquivo;

        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            return ['erro' => 'Não foi possível salvar o PDF no servidor'];
        }

        $resultado = $this->arquivoDAO->inserir($nome, $path, $propriedadeId);
        if (is_array($resultado) && isset($resultado['erro'])) {
            @unlink($destino);
        }
        return $resultado;
    }

    /**
     * Serve o PDF binário com os headers corretos.
     * Chama exit() — deve ser a última operação do ciclo de request.
     */
    public function download(int $id): void {
        $registro = $this->arquivoDAO->buscarId($id);

        if (!$registro) {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['erro' => 'Arquivo não encontrado']);
            exit;
        }

        $caminho = $this->caminhoAbsoluto($registro->getPath());

        if (!$caminho || !is_file($caminho)) {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['erro' => 'Arquivo PDF não encontrado no servidor']);
            exit;
        }

        // Remove qualquer header já definido (especialmente Content-Type: application/json)
        header_remove();
        http_response_code(200);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . rawurlencode($registro->getNome()) . '.pdf"');
        header('Content-Length: ' . filesize($caminho));
        header('Cache-Control: private, max-age=300');
        header('X-Content-Type-Options: nosniff');

        readfile($caminho);
        exit;
    }

    public function excluirId($id) {
        $registro = $this->arquivoDAO->buscarId($id);
        if ($registro) {
            $caminho = $this->caminhoAbsoluto($registro->getPath());
            if ($caminho && is_file($caminho)) {
                @unlink($caminho);
            }
        }
        return $this->arquivoDAO->excluirId($id);
    }
}
?>