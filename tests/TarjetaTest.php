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
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;
        $colectivo = new Colectivo(145, "Metrobus", 4825);

        $tarjeta->recargar(30, $gestor);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), "normal");

        $tarjeta = new Tarjeta;
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), "plus");
    }

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */
    public function testCargaSaldo() {
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;

        $this->assertTrue($tarjeta->recargar(10, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);

        $this->assertTrue($tarjeta->recargar(30, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 40);

        $this->assertTrue($tarjeta->recargar(947.60, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 987.60);

        $this->assertTrue($tarjeta->recargar(1788.80, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 2776.48);
    }

    /**
     * Comprueba que al realizar una recarga luego de haber utilizado los viajes plus, estos vuelvan a su valor inicial.
     */
    public function testCargaPlus(){
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertFalse($tarjeta->restarPlus());
        $tarjeta->recargar(50, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);

        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 1);
        $tarjeta->recargar(50, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
    }

    /**
     * Comprueba que el monto abonado por la tarjeta es el correcto.
     */
    public function testAbonado(){
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->recargar(100, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->abonado(), TarjetaTest::viajes["normal"] * 3);
        
        $tarjeta = new Tarjeta;
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->recargar(100, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->abonado(), TarjetaTest::viajes["normal"] * 2);
    }

    /**
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido() {
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;

        $this->assertFalse($tarjeta->recargar(15, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     * Comprueba que es posible obtener el boleto de tipo trasbordo para cada tipo.
     */
    public function testTrasbordo() {
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(100, $gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), "normal");

    }

    public function testTrasbordoMedio() {
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new TarjetaMedio;
        $tarjeta->recargar(50, $gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");
    }

    public function testTrasbordoMedioUni() {
        $gestor = new GestorDeMontos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new TarjetaMedioUni;
        $tarjeta->recargar(50, $gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");
    }


}
