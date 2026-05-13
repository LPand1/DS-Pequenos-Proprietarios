<?php
Class Usuario implements jsonSerializable {
    private $id;
    private $senha;
    private $cpf;

    public function setId($id) { $this->id = $id; } 
    public function getId() { return $this->id; }
    public function setSenha($s) { $this->senha = $s; }
    public function getSenha() { return $this->senha; }
    public function setCpf($c) { $this->cpf = $c; }
    public function getCpf() { return $this->cpf; }

    public function jsonSerialize() : mixed {
        return  [
            'id' => $this->id,
            'senha' => $this->senha,
            'cpf' => $this->cpf,
        ];
    }
}

?>