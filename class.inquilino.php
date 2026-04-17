<?php
Class Inquilino implements JsonSerializable {
    private $id;
    private $nome;
    private $email;
    private $usuarioId;
    
    function getId() { return $this->id; }
    function setNome($n) { $this->nome = $n; }
    function getNome() { return $this->nome; }
    function setEmail($e) { $this->email = $e; }
    function getEmail() { return $this->email; }
    function getUsuarioId() { return $this->usuarioId; }

    function jsonSerialize() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'usuarioId' => $this->usuarioId,
        ];
    }
}
?>