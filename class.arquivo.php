<?php
Class Arquivo implements JsonSerializable {
    private $id;
    private $nome;
    private $path;

    function getId() { return $this->id; }
    function setNome($n) { $this->nome = $n; }
    function getNome() { return $this->nome; }
    function setPath($p) { $this->path = $p; }
    function getPath() { return $this->path; }

    function jsonSerialize() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'path' => $this->path,
        ];
    }
}

?>