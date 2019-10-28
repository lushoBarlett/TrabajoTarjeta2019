<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetasTest extends TestCase {
    
    private $viajes = array(Pasajes::Transbordo => 0, Pasajes::Libre => 0, Pasajes::Medio => 2, Pasajes::Normal => 2);
    private $recargas = array(1 => 1, 2 => 2, 3 => 3, 4 => 5, 5 => 10);
    
    public function pagadores() {
        return [
            [new Tarjeta(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo)],
            [new TarjetaMedio(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo)],
            [new TarjetaMedioUni(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo)]
        ];
    }
    
    public function medios() {
        return [
            [new TarjetaMedio(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo)],
            [new TarjetaMedioUni(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo)]
        ];
    }
    
    /**
     * @dataProvider pagadores
     * Comprueba que es posible pagar un viaje con y sin tener saldo
     */
    public function testPagarSaldo(PagoRecargableInterface $tarjeta) {
        
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tiempo = new Tiempo;
        
        $tarjeta->recargar(2);
        $boleto = $tarjeta->pagarBoleto($colectivo,$tiempo);
        $this->assertNotEquals($boleto->pasaje(), Pasajes::Fallido);
        
        $boleto = $tarjeta->pagarBoleto($colectivo,$tiempo);
        $this->assertEquals($boleto->pasaje(), Pasajes::Plus);
    }
    
    /**
     * @dataProvider pagadores
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido
     */
    public function testCargaSaldo(PagoRecargableInterface $tarjeta) {
        
        $this->assertTrue($tarjeta->recargar(1));
        $this->assertTrue($tarjeta->recargar(2));
        $this->assertTrue($tarjeta->recargar(4));
        
        $boleto = $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($boleto->saldo(), 6);
    }
    
    /**
     * @dataProvider pagadores
     * Comprueba que al realizar una recarga luego de haber utilizado los viajes plus, estos vuelvan a su valor inicial.
     */
    public function testCargaPlus(PagoRecargableInterface $tarjeta){
        
        $boleto = $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($boleto->pasaje(),Pasajes::Plus);
        $boleto = $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($boleto->pasaje(),Pasajes::Plus);
        
        $tarjeta->recargar(5);
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($tarjeta->saldo(), 4);
        
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        
        $boleto = $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($boleto->pasaje(),Pasajes::Plus);
    }
    
    /**
     * @dataProvider pagadores
     * Comprueba que el monto abonado por la tarjeta es el correcto.
     */
    public function testAbonado(PagoRecargableInterface $tarjeta){

        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $tarjeta->recargar(4);
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($this->recargas[4] - $tarjeta->saldo(), $this->viajes[Pasajes::Normal] * 2);
        
        $tarjeta = new Tarjeta(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo);
        
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $tarjeta->recargar(5);
        $tarjeta->pagarBoleto(new Colectivo, new Tiempo);
        $this->assertEquals($this->recargas[5] - $tarjeta->saldo(), $this->viajes[Pasajes::Normal] * 3);
    }
    
    /**
     * @dataProvider pagadores
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido(PagoRecargableInterface $tarjeta) {

        $this->assertFalse($tarjeta->recargar(-1));
        $this->assertEquals($tarjeta->saldo(), 0);
    }
    
    /**
     * @dataProvider pagadores
     * Comprueba que es posible hacer transbordo.
     */
    public function testTrasbordo(PagoRecargableInterface $tarjeta) {

        $colectivo1 = new Colectivo(145, "Metrobus", 4825);
        $colectivo2 = new Colectivo(456, "Rosariobus", 1234);
        $tiempo = new Tiempo;
        
        $tarjeta->recargar(2);
        $tarjeta->pagarBoleto($colectivo1,$tiempo);
        $tiempo->avanzar(30 * 60);
        $boleto = $tarjeta->pagarBoleto($colectivo2,$tiempo);
        
        $this->assertEquals($boleto->pasaje(), Pasajes::Transbordo);
    }

    /**
     * Prueba que la tarjeta libre siempre devuelva el pasaje libre
     */
    public function testLibre(){

        $tarjeta = new TarjetaLibre(new Boleto,new Montos,new Transbordo);
        
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo);
        $this->assertEquals($boleto->pasaje(), Pasajes::Libre);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo(1000000));
        $this->assertEquals($boleto->pasaje(), Pasajes::Libre);
        $boleto = $tarjeta->pagarBoleto(new Colectivo(1,1,1),new Tiempo);
        $this->assertEquals($boleto->pasaje(), Pasajes::Libre);
    }
    
    /**
     * @dataProvider medios
     * Comprueba que el medio boleto no funcione dentro de los 5 minutos
     */
    public function testMedio(PagoRecargableInterface $tarjeta){
        
        $tarjeta->recargar(5);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo);
        $this->assertEquals($boleto->pasaje(), Pasajes::Medio);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo(5 * 60));
        $this->assertEquals($boleto->pasaje(), Pasajes::Normal);
    }
    
    /**
     * Comprueba que el medio boleto universitario tenga solo dos medios por día
     */
    public function testDosMedios(){
        
        $tarjeta = new TarjetaMedioUni(new Boleto,new Montos($this->viajes,$this->recargas),new Transbordo);
        
        $tarjeta->recargar(5);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo(10 * 60));
        $this->assertEquals($boleto->pasaje(), Pasajes::Medio);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo(20 * 60));
        $this->assertEquals($boleto->pasaje(), Pasajes::Medio);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo(30 * 60));
        $this->assertEquals($boleto->pasaje(), Pasajes::Normal);
        $boleto = $tarjeta->pagarBoleto(new Colectivo,new Tiempo(24 * 60 * 60));
        $this->assertEquals($boleto->pasaje(), Pasajes::Medio);
    }
}
