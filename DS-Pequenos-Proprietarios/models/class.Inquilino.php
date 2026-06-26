<?php
Class Inquilino implements JsonSerializable {
    private $id;
    private $nome;
    private $email;
    private $usuarioId;
    
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNome($n) { $this->nome = $n; }
    public function getNome() { return $this->nome; }
    public function setEmail($e) { $this->email = $e; }
    public function getEmail() { return $this->email; }
    public function setUsuarioId($uId) { $this->usuarioId = $uId; }
    public function getUsuarioId() { return $this->usuarioId; }

    public function jsonSerialize() : mixed {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'usuarioId' => $this->usuarioId,
        ];
    }
}
?>