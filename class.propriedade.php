<?php
Class Propriedade implements JsonSerializable {
    private $id;
    private $proprietarioId;
    private $inquilinoId;
    private $endereco;
    private $tipo;
    private $descricao;
    private $aluguel;

    function getId() { return $this->id; }
    function getProprietarioId() { return $this->proprietarioId; }
    function getInquilinoId() { return $this->inquilinoId; }
    function setEndereco($e) { $this->endereco = $e; }
    function getEndereco() { return $this->endereco; }
    function setTipo($t) { $this->tipo = $t; }
    function getTipo() { return $this->id; }
    function setDescricao($d) { $this->descricao = $d; }
    function getDescricao() { return $this->id; }
    function setAluguel($a) { $this->aluguel = $a; }
    function getAluguel() { return $this->aluguel; }

    function jsonSerialize() {
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