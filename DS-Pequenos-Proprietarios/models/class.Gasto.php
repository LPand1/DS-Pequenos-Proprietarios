<?php
Class Gasto implements JsonSerializable {
    private $id;
    private $valor;
    private $data;
    private $total;
    private $propriedadeId;
    private $descricao;
    private $inquilino;

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setValor($v) { $this->valor = $v; }
    public function getValor() { return $this->valor; }
    public function setData($d) { $this->data = $d; }
    public function getData() { return $this->data; }
    public function setTotal($t) { $this->total = $t; }
    public function getTotal() { return $this->total; }
    public function setPropriedadeId($pId) { $this->propriedadeId = $pId; }
    public function getPropriedadeId() { return $this->propriedadeId; }
    public function setDescricao($d) { $this->descricao = $d; }
    public function getDescricao() { return $this->descricao; }
    public function setInquilino($i) { $this->inquilino = $i; }
    public function getInquilino() { return $this->inquilino; }

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'valor' => $this->valor,
            'data' => $this->data,
            'total' => $this->total,
            'propriedadeId' => $this->propriedadeId,
            'descricao' => $this->descricao,
            'inquilino' => $this->inquilino,
        ];
    }
}

?>
