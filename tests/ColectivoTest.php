<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    const viajes = array("libre" => 0, "medio" => 13.75, "normal" => 27.50);
    const recargas = array(10 => 10, 30 => 30, 50 => 50, 100 => 100, 200 => 200, 947.60 => 1100, 1788.80 => 2200);

    protected $gestor;

    public function __construct() {
        $this->gestor = new GestorDeMontos(ColectivoTest::viajes,ColectivoTest::recargas);
    }

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
        $tarjeta->recargar(30, $this->gestor);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(300);
        $this->assertEquals($colectivo->pagarCon($tarjeta)->obtenerValor(), ColectivoTest::viajes["medio"]);
    }

    /**
     * Comprueba que efectivamente se puedan utilizar dos viajes plus.
     */
    public function testHastaDosPLus() {
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(30, $this->gestor);
        $tarjeta->restarViaje($colectivo, $this->gestor);
        $tarjeta->restarViaje($colectivo, $this->gestor);
        $tarjeta->restarViaje($colectivo, $this->gestor);
        $this->assertFalse($tarjeta->restarViaje($colectivo, $this->gestor));
    }

    /**
     * Comprueba que se descuenten correctamente los viajes plus.
     */
    public function testDescuentoDePLus() {
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(30);
        $tarjeta->restarViaje($colectivo, $this->gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
        $tarjeta->restarViaje($colectivo, $this->gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 1);
        $tarjeta->restarViaje($colectivo, $this->gestor);
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
