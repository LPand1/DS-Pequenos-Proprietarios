<?php
Class Usuario implements jsonSerializable {
    private $id;
    private $senha;
    private $cpf;

    function getId() { return $this->id; }
    function setSenha($s) { $this->senha = $s; }
    function getSenha() { return $this->senha; }
    function setCpf($c) { $this->cpf = $c; }
    function getCpf() { return $this->cpf; }

    function jsonSerialize() {
        return  [
            'id' => $this->id,
            'senha' => $this->senha,
            'cpf' => $this->cpf,
        ];
    }
}

?>