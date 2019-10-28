<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    /**
     * Prueba las respuestas del colectivo dado un boleto
     */
    public function testRespuesta(){
        $boleto = new Boleto;
        $colectivo = new Colectivo;
        $tiempo = new Tiempo;

        $boleto->nuevo(0,Pasajes::Libre,$colectivo,$tiempo);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Pase Libre.");

        $boleto->nuevo(1,Pasajes::Transbordo,$colectivo,$tiempo);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Transbordo. El saldo es 1.");
        
        $boleto->nuevo(2,Pasajes::Medio,$colectivo,$tiempo);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Medio Boleto. El saldo es 2.");
        
        $boleto->nuevo(3,Pasajes::Normal,$colectivo,$tiempo);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Normal. El saldo es 3.");
        
        $boleto->nuevo(4,Pasajes::Plus,$colectivo,$tiempo);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Viaje Plus. El saldo es 4.");
        
        $boleto->nuevo(5,Pasajes::Fallido,$colectivo,$tiempo);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Debe dos viajes plus.");
        
        $boleto->nuevo(null,null,null,null);
        $this->assertEquals($colectivo->pagadoCon($boleto),"Error en la operacion.");
    }
}
