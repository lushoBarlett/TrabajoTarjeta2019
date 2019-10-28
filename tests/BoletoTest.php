<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

    /**
     * Comprueba que se carguen los datos correctamente.
     */
    public function testRegistro(){
        $boleto = new Boleto;
        $boleto->nuevo(0,Pasajes::Normal, new Colectivo, new Tiempo);
        $this->assertEquals($boleto->pasaje(),Pasajes::Normal);
        $this->assertEquals($boleto->transporte(),new Colectivo);
        $this->assertEquals($boleto->tiempo(),new Tiempo);
    }

}
