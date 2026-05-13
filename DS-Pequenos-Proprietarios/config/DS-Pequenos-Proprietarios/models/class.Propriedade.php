<?php
Class Propriedade implements JsonSerializable {
    private $id;
    private $proprietarioId;
    private $inquilinoId;
    private $endereco;
    private $tipo;
    private $descricao;
    private $aluguel;

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setProprietarioId($pId) { $this->proprietarioId = $pId; }
    public function getProprietarioId() { return $this->proprietarioId; }
    public function setInquilinoId($iId) { $this->inquilinoId = $iId; }
    public function getInquilinoId() { return $this->inquilinoId; }
    public function setEndereco($e) { $this->endereco = $e; }
    public function getEndereco() { return $this->endereco; }
    public function setTipo($t) { $this->tipo = $t; }
    public function getTipo() { return $this->id; }
    public function setDescricao($d) { $this->descricao = $d; }
    public function getDescricao() { return $this->id; }
    public function setAluguel($a) { $this->aluguel = $a; }
    public function getAluguel() { return $this->aluguel; }

    public function jsonSerialize() : mixed {
        return [
            'id' => $this->id,
            'proprietarioId' => $this->id,
            'inquilinoId' => $this->inquilinoId,
            'endereco' => $this->endereco,
            'tipo' => $this->tipo,
            'descricao' => $this->descricao,
            'aluguel' => $this->aluguel,
        ];
    }
}

?>