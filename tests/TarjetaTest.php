<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    const viajes = array("libre" => 0, "medio" => 13.75, "normal" => 27.50);
    const recargas = array(10 => 10, 30 => 30, 50 => 50, 100 => 100, 200 => 200, 947.60 => 1100, 1788.80 => 2200);

    /**
    * Comprueba que es posible pagar un viaje con y sin tener saldo
    */
    public function testPagarSaldo() {
        $gestor = new GestorDeMontos(viajes,recargas);
        $tarjeta = new Tarjeta;
        $colectivo = new Colectivo(145, "Metrobus", 4825);

        $tarjeta->recargar(30, $gestor);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "normal");

        $tarjeta = new Tarjeta;
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "plus");
    }

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */
    public function testCargaSaldo() {
        $tarjeta = new Tarjeta;

        $this->assertTrue($tarjeta->recargar(10));
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);

        $this->assertTrue($tarjeta->recargar(30));
        $this->assertEquals($tarjeta->obtenerSaldo(), 40);

        $this->assertTrue($tarjeta->recargar(947.60));
        $this->assertEquals($tarjeta->obtenerSaldo(), 987.60);

        $this->assertTrue($tarjeta->recargar(1788.80));
        $this->assertEquals($tarjeta->obtenerSaldo(), 2776.48);
    }

    /**
     * Comprueba que al realizar una recarga luego de haber utilizado los viajes plus, estos vuelvan a su valor inicial.
     */
    public function testCargaPlus(){
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $this->assertFalse($tarjeta->restarPlus());
        $tarjeta->recargar(50);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);

        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->obtenerPlus(), 1);
        $tarjeta->recargar(50);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
    }

    /**
     * Comprueba que el monto abonado por la tarjeta es el correcto.
     */
    public function testAbonado(){
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $tarjeta->recargar(100);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->abonado(), TarjetaTest::viajes["normal"] * 3);
        
        $tarjeta = new Tarjeta;
        $tarjeta->restarViaje($colectivo);
        $tarjeta->recargar(100);
        $this->assertEquals($tarjeta->abonado(), TarjetaTest::viajes["normal"] * 2);
    }

    /**
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido() {
      $tarjeta = new Tarjeta;

      $this->assertFalse($tarjeta->recargar(15));
      $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     * Comprueba que es posible obtener el boleto de tipo trasbordo para cada tipo.
     */
    public function testTrasbordo() {
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(100);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $boleto = $colectivo->pagarCon($tarjeta);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $boleto = $colectivo->pagarCon($tarjeta);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "normal");

    }

    public function testTrasbordoMedio() {
        $tarjeta = new TarjetaMedio;
        $tarjeta->recargar(50);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");
    }

    public function testTrasbordoMedioUni() {
        $tarjeta = new TarjetaMedioUni;
        $tarjeta->recargar(50);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");
    }


}
