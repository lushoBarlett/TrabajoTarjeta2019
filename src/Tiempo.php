<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface {

    protected $tiempo;

    public function __construct(Int $iniciarEn = 0) {
        $this->tiempo = $iniciarEn;
    }

    public function avanzar(Int $segundos) {
        $this->tiempo += $segundos;
    }

    public function tiempo(String $formato) {
        return date($formato, $this->tiempo);
    }
}