<?php
Class Banco {
    private static ?PDO $conexao = null;

    private string $host = 'localhost';
    private string $db = 'pequenos_proprietarios';
    private string $user = 'root';
    private string $password = '';
    private string $charset = 'utf8mb4';

    public static function getConexao() : PDO {
        if (self::$conexao === null) {
            $banco = new self();
            self::$conexao = $banco->conectar();
        }
        
        return self::$conexao;
    }

    private function conectar() : PDO {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        
        $opcoes = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            return new PDO($dsn, $this->user, $this->password, $opcoes);
        } catch (PDOException $e) {
            throw new RuntimeException('Erro ao conectar ao banco: ' . $e->getMessage());

        }
    }
}

?>