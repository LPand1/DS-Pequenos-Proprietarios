<?php
Class Proprietario implements JsonSerializable {
    private $id;
    private $nome;
    private $usuarioId;
    
    public function getId() { return $this->id; }
    public function setNome($n) { $this->nome = $n; }
    public function getNome() { return $this->nome; }
    public function setUsuarioId($uId) { $this->usuarioId = $uId; }
    public function getUsuarioId() { return $this->usuarioId; }

    public function jsonSerialize() : mixed {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'usuarioId' => $this->usuarioId,   
        ];
    }
}

?>