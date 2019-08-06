<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    /**
     * Comprueba que siempre es posible pagar con la tarjeta, se cuentan los dos viajes plus.
     */
    public function testlibreSiempreLibre() {
        $f = 0;
        $tarjeta = new TarjetaLibre;
        $colectivo = new Colectivo;
        for($i = 0; $i < 10; $i++){
          if($colectivo->pagarCon($tarjeta) == false){
            $f++;
          }
        }
        $this->assertEquals($f, 8);
    }

     /**
     * Comprueba que el costo del viaje de la tarjeta del tipo medio sea el correspondiente.
     */
    public function testMedioSiempreMedio() {
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(30);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(300);
        $this->assertEquals($colectivo->pagarCon($tarjeta)->obtenerValor(), 14.80/2);
    }

    /**
     * Comprueba que efectivamente se puedan utilizar dos viajes plus.
     */
    public function testHastaDosPLus() {
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(20);
        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $this->assertFalse($tarjeta->restarViaje($colectivo));
    }

    /**
     * Comprueba que se descuenten correctamente los viajes plus.
     */
    public function testDescuentoDePLus() {
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(20);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->obtenerPlus(), 1);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->obtenerPlus(), 0);
    }

    /**
     * Comprueba que el tipo del boleto sea libre al utilizar una tarjeta del tipo libre.
     */
    public function testObtenerInfo(){
        $colectivo = new Colectivo(142, 'rosario bus', 55);
        $this->assertEquals($colectivo->empresa(), 'rosario bus');
        $this->assertEquals(55, $colectivo->numero());
    }
}
