<?php

namespace TrabajoTarjeta;

interface TiempoInterface {

    public function time();

}

class TiempoFalso implements TiempoInterface {

    protected $tiempo;

    public function __construct($iniciarEn = 0) {

        $this->tiempo = $iniciarEn;

    }

    /**
     *  Avanza el tiempo falso en una determinada cantidad de segundos.
     */
    public function avanzar($segundos) {

        $this->tiempo += $segundos;

    }

    /**
     *  Devuelve el valor actual del tiempo falso.
     * 
     *  @return tiempo
     */
    public function time() {

        return $this->tiempo;

    }

    
}