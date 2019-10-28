<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TransbordoTest extends TestCase {
    
    private $feriados = ["01-01"];
    private $medios = ["01-02"];

    public function true(){
        return [
            /**
             * Días de semana de 6 a 22 hs, 60 minutos de trabsbordo
             */
            [new Tiempo(strtotime("2019-10-21 06:00:01")),new Tiempo(strtotime("2019-10-21 07:00:00"))],
            [new Tiempo(strtotime("2019-10-22 10:00:01")),new Tiempo(strtotime("2019-10-22 11:00:00"))],
            [new Tiempo(strtotime("2019-10-23 13:30:00")),new Tiempo(strtotime("2019-10-23 14:29:59"))],
            [new Tiempo(strtotime("2019-10-24 16:15:15")),new Tiempo(strtotime("2019-10-24 17:15:14"))],
            [new Tiempo(strtotime("2019-10-25 21:00:00")),new Tiempo(strtotime("2019-10-25 21:59:59"))],
            /**
             * Sábados y medios feriados de 6 a 14 hs, 60 minutos de transbordo
             */
            [new Tiempo(strtotime("2019-10-26 05:45:00")),new Tiempo(strtotime("2019-10-26 06:44:59"))],
            [new Tiempo(strtotime("2019-01-02 13:00:00")),new Tiempo(strtotime("2019-01-02 13:59:59"))],
            /**
             * Domingos y feriados todo el día, 120 minutos de transbordo
             */
            [new Tiempo(strtotime("2019-10-27 00:00:00")),new Tiempo(strtotime("2019-10-27 01:59:59"))],
            [new Tiempo(strtotime("2019-10-27 22:00:00")),new Tiempo(strtotime("2019-10-27 23:59:59"))],
            [new Tiempo(strtotime("2019-01-01 00:00:00")),new Tiempo(strtotime("2019-01-01 01:59:59"))],
            [new Tiempo(strtotime("2019-01-01 22:00:00")),new Tiempo(strtotime("2019-01-01 23:59:59"))],
            [new Tiempo(strtotime("2019-01-02 13:00:00")),new Tiempo(strtotime("2019-01-02 13:59:59"))],
            /**
             * De noche, 120 minutos de transbordo
             */
            [new Tiempo(strtotime("2019-10-21 00:00:00")),new Tiempo(strtotime("2019-10-21 01:59:59"))],
            [new Tiempo(strtotime("2019-10-27 22:00:00")),new Tiempo(strtotime("2019-10-27 23:59:59"))],
            [new Tiempo(strtotime("2019-10-27 14:00:00")),new Tiempo(strtotime("2019-10-27 15:59:59"))],
            [new Tiempo(strtotime("2019-01-02 00:00:00")),new Tiempo(strtotime("2019-01-02 01:59:59"))],
            [new Tiempo(strtotime("2019-01-02 22:00:00")),new Tiempo(strtotime("2019-01-02 23:59:59"))],
            [new Tiempo(strtotime("2019-01-02 14:00:00")),new Tiempo(strtotime("2019-01-02 15:59:59"))]
        ];
    }
    
    public function false(){
        return [
            /**
             * Días de semana de 6 a 22 hs, 60 minutos de trabsbordo
             */
            [new Tiempo(strtotime("2019-10-21 06:00:00")),new Tiempo(strtotime("2019-10-21 07:00:00"))],
            [new Tiempo(strtotime("2019-10-22 10:00:00")),new Tiempo(strtotime("2019-10-22 11:00:00"))],
            [new Tiempo(strtotime("2019-10-23 13:30:00")),new Tiempo(strtotime("2019-10-23 14:30:00"))],
            [new Tiempo(strtotime("2019-10-24 16:15:15")),new Tiempo(strtotime("2019-10-24 17:15:15"))],
            [new Tiempo(strtotime("2019-10-25 20:59:59")),new Tiempo(strtotime("2019-10-25 21:59:59"))],
            /**
             * Sábados y medios feriados de 6 a 14 hs, 60 minutos de transbordo
             */
            [new Tiempo(strtotime("2019-10-26 05:45:00")),new Tiempo(strtotime("2019-10-26 06:45:00"))],
            [new Tiempo(strtotime("2019-01-02 12:59:59")),new Tiempo(strtotime("2019-01-02 13:59:59"))],
            /**
             * Domingos y feriados todo el día, 120 minutos de transbordo
             */
            [new Tiempo(strtotime("2019-10-27 00:00:00")),new Tiempo(strtotime("2019-10-27 02:00:00"))],
            [new Tiempo(strtotime("2019-10-27 22:00:00")),new Tiempo(strtotime("2019-10-28 00:00:00"))],
            [new Tiempo(strtotime("2019-01-01 00:00:00")),new Tiempo(strtotime("2019-01-01 02:00:00"))],
            [new Tiempo(strtotime("2019-01-01 22:00:00")),new Tiempo(strtotime("2019-01-02 00:00:00"))],
            [new Tiempo(strtotime("2019-01-02 13:00:00")),new Tiempo(strtotime("2019-01-02 15:00:00"))],
            /**
             * De noche, 120 minutos de transbordo
             */
            [new Tiempo(strtotime("2019-10-21 00:00:00")),new Tiempo(strtotime("2019-10-21 02:00:00"))],
            [new Tiempo(strtotime("2019-10-27 22:00:00")),new Tiempo(strtotime("2019-10-28 00:00:00"))],
            [new Tiempo(strtotime("2019-10-27 14:00:00")),new Tiempo(strtotime("2019-10-27 16:00:00"))],
            [new Tiempo(strtotime("2019-01-02 00:00:00")),new Tiempo(strtotime("2019-01-02 02:00:00"))],
            [new Tiempo(strtotime("2019-01-02 22:00:00")),new Tiempo(strtotime("2019-01-03 00:00:00"))],
            [new Tiempo(strtotime("2019-01-02 14:00:00")),new Tiempo(strtotime("2019-01-02 16:00:00"))]
        ];
    }

    /**
     * @dataProvider true
     * Prueba el transbordo cuando debería devolver true
     */
    public function testTrue(TiempoInterface $tiempo1, TiempoInterface $tiempo2){
        $transbordo = new Transbordo($this->feriados,$this->medios);
        $colectivo1 = new Colectivo(0,0,0);
        $colectivo2 = new Colectivo(1,1,1);
        $boleto = new Boleto;
        $boleto->nuevo(null,Pasajes::Fallido,$colectivo1,$tiempo1);
        
        $transbordo->ultimoPago($boleto);
        $this->assertTrue($transbordo->validar($colectivo2,$tiempo2));
    }

    /**
     * @dataProvider false
     * Prueba el transbordo cuando debería devolver false
     */
    public function testFalse(TiempoInterface $tiempo1, TiempoInterface $tiempo2){
        $transbordo = new Transbordo($this->feriados,$this->medios);
        $colectivo1 = new Colectivo(0,0,0);
        $colectivo2 = new Colectivo(1,1,1);
        $boleto = new Boleto;
        $boleto->nuevo(null,Pasajes::Fallido,$colectivo1,$tiempo1);

        $transbordo->ultimoPago($boleto);
        $this->assertFalse($transbordo->validar($colectivo2,$tiempo2));
    }
    
    /**
     * Prueba que con transportes iguales no vale el transbordo
     */
    public function testTransporte(){
        $transbordo = new Transbordo;
        $colectivo1 = new Colectivo;
        $boleto = new Boleto;
        $boleto->nuevo(null,Pasajes::Fallido,$colectivo1,new Tiempo);
        
        $transbordo->ultimoPago($boleto);
        $this->assertFalse($transbordo->validar($colectivo1,new Tiempo));
    }
    
    /**
     * Prueba que si el boleto no está inicializado, no hay transbordo
     */
    public function testPrimerViaje(){
        $transbordo = new Transbordo;
        $colectivo1 = new Colectivo(0,0,0);
        $colectivo2 = new Colectivo(1,1,1);
        $boleto = new Boleto;
        $boleto->nuevo(null,null,null,null);
        
        $transbordo->ultimoPago($boleto);
        $this->assertFalse($transbordo->validar($colectivo2,new Tiempo));
    }
    
    /**
     * Prueba que si el boleto fue plus, no hay transbordo
     */
    public function testViajePlus(){
        $transbordo = new Transbordo;
        $colectivo1 = new Colectivo(0,0,0);
        $colectivo2 = new Colectivo(1,1,1);
        $boleto = new Boleto;
        $boleto->nuevo(null,Pasajes::Plus,$colectivo1,new Tiempo);
        
        $transbordo->ultimoPago($boleto);
        $this->assertFalse($transbordo->validar($colectivo2,new Tiempo));
    }
}
