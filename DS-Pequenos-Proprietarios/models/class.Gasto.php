<?php
Class Gasto implements JsonSerializable {
    private $id;
    private $valor;
    private $data;
    private $total;
    private $propriedadeId;

    public function getId() { return $this->id; }
    public function setValor($v) { $this->valor = $v; }
    public function getValor() { return $this->valor; }
    public function setData($d) { $this->data = $d; }
    public function getData() { return $this->data; }
    public function setTotal($t) { $this->total = $t; }
    public function getTotal() { return $this->total; }
    public function setPropriedadeId($pId) { $this->propriedadeId = $pId; }
    public function getPropriedadeId() { return $this->propriedadeId; }

    public function jsonSerialize() : mixed {
        return [
            'id' => $this->id,
            'valor' => $this->valor,
            'data' => $this->data,
            'total' => $this->total,
            'propriedadeId' => $this->propriedadeId,
        ];
    }
}

?>