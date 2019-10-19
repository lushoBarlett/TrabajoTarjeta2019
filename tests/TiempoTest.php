<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TiempoTest extends TestCase {

    /**
     * Comprueba que al no poner nada se inicia en el epoch
     */
    public function testEpoch(){
        $tiempo = new Tiempo();
        $this->assertEquals($tiempo->tiempo("Y-m-d h:i:s"), "1970-01-01 00:00:00");
    }

    /**
     * Comprueba que avanza el tiempo correctamente
     */
    public function testAvance(){
        $tiempo = new Tiempo(mktime(0,0,0,1,1,1970));
        $tiempo->avanzar(86461);
        $this->assertEquals($tiempo->tiempo("Y-m-d h:i:s"), "1970-01-02 00:01:01");
    }
}
