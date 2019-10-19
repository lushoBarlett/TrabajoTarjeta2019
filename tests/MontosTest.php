<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class MontosTest extends TestCase {

    /**
     * Comprueba que devuelve null para pagos no validos y un valor para las demas
     */
    public function testPago(){
        $montos = new Montos(["2" => "valor"],[]);
        $this->assertEquals(null, $montos->montoAPagar("0"));
        $this->assertEquals("valor", $montos->montoAPagar("2"));
    }

    /**
     * Comprueba que devuelve null para cargas no validas y un valor para las demas
     */
    public function testCarga(){
        $montos = new Montos([],["2" => "valor"]);
        $this->assertEquals(null, $montos->montoACargar("0"));
        $this->assertEquals("valor", $montos->montoACargar("2"));
    }

}
