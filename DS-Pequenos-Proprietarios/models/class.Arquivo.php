<?php
Class Arquivo implements JsonSerializable {
    private $id;
    private $nome;
    private $path;
    private $propriedadeId;

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNome($n) { $this->nome = $n; }
    public function getNome() { return $this->nome; }
    public function setPath($p) { $this->path = $p; }
    public function getPath() { return $this->path; }
    public function setPropriedadeId($pId) { $this->propriedadeId = $pId; }
    public function getPropriedadeId() { return $this->propriedadeId; }

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'path' => $this->path,
            'propriedadeId' => $this->propriedadeId,
        ];
    }
}

?>
