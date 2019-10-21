<?php

namespace TrabajoTarjeta;

class Transbordo implements TransbordoInterface {
    
    protected $feriados;

    public function __construct(Array $feriados){
        $this->feriados = $feriados;
    }

    public function validar(BoletoInterface $boleto, CanceladoraInterface $canceladora, TiempoInterface $tiempo){
        return false;
    }
}