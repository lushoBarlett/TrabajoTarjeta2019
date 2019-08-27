<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    const viajes = array("libre" => 0, "medio" => 13.75, "normal" => 27.50);
    const recargas = array("10" => 10, "30" => 30, "50" => 50, "100" => 100, "200" => 200, "947.60" => 1100, "1788.80" => 2200);

    /**
     * Comprueba que siempre es posible pagar con la tarjeta, se cuentan los dos viajes plus.
     */
    public function testlibreSiempreLibre() {
        $gestor = new GestorDeMontos(ColectivoTest::viajes,ColectivoTest::recargas);
        $f = 0;
        $tarjeta = new TarjetaLibre;
        $colectivo = new Colectivo;
        for($i = 0; $i < 10; $i++){
          if($colectivo->pagarCon($tarjeta, $gestor) == false){
            $f++;
          }
        }
        $this->assertEquals($f, 8);
    }

     /**
     * Comprueba que el costo del viaje de la tarjeta del tipo medio sea el correspondiente.
     */
    public function testMedioSiempreMedio() {
        $gestor = new GestorDeMontos(ColectivoTest::viajes,ColectivoTest::recargas);
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(30, $gestor);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(300);
        $this->assertEquals($colectivo->pagarCon($tarjeta, $gestor)->obtenerValor(), ColectivoTest::viajes["medio"]);
    }

    /**
     * Comprueba que efectivamente se puedan utilizar dos viajes plus.
     */
    public function testHastaDosPLus() {
        $gestor = new GestorDeMontos(ColectivoTest::viajes,ColectivoTest::recargas);
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(30, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertFalse($tarjeta->pagarBoleto($colectivo, $gestor));
    }

    /**
     * Comprueba que se descuenten correctamente los viajes plus.
     */
    public function testDescuentoDePLus() {
        $gestor = new GestorDeMontos(ColectivoTest::viajes,ColectivoTest::recargas);
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(30, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 1);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 0);
    }

    /**
     * Comprueba que el tipo del boleto sea libre al utilizar una tarjeta del tipo libre.
     */
    public function testObtenerInfo(){
        $gestor = new GestorDeMontos(ColectivoTest::viajes,ColectivoTest::recargas);
        $colectivo = new Colectivo(142, 'rosario bus', 55);
        $this->assertEquals($colectivo->empresa(), 'rosario bus');
        $this->assertEquals(55, $colectivo->numero());
    }
}
