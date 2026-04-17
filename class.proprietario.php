<?php
Class Proprietario implements JsonSerializable {
    private $id;
    private $nome;
    private $usuarioId;
    
    function getId() { return $this->id; }
    function setNome($n) { $this->nome = $n; }
    function getNome() { return $this->nome; }
    function getUsuarioId() { return $this->usuarioId; }

    function jsonSerialize() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'usuarioId' => $this->usuarioId,   
        ];
    }
}

?>