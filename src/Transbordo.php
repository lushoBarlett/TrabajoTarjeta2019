<?php

namespace TrabajoTarjeta;

class Transbordo implements TransbordoInterface {
    
    protected $feriados;

    public function __construct(Array $feriados){
        $this->feriados = $feriados;
    }

    public function sePuedeTransbordo(TarjetaInterface $tarjeta, ColectivoInterface $colectivo, TiempoInterface $tiempo){
        return false;
    }
}