<?php
Class Gasto implements JsonSerializable {
    private $id;
    private $valor;
    private $data;
    private $total;
    private $propriedadeId;

    function getId() { return $this->id; }
    function setValor($v) { $this->valor = $v; }
    function getValor() { return $this->valor; }
    function setData($d) { $this->data = $d; }
    function getData() { return $this->data; }
    function setTotal($t) { $this->total = $t; }
    function getTotal() { return $this->total; }
    function getPropriedadeId() { return $this->propriedadeId; }

    function jsonSerialize() {
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