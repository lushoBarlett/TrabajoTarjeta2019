<?php

namespace TrabajoTarjeta;

class Transbordo implements TransbordoInterface {
    
    protected $feriados;
    protected $medios;
    protected $boleto;
    // números en minutos
    const Regular = 60 * 60;
    const Extendido = 120 * 60;
    const Semana = ["Mon","Tue","Wed","Thu","Fri"];

    public function __construct(Array $feriados = [], Array $medios = []){
        $this->feriados = $feriados;
        $this->medios = $medios;
    }

    public function ultimoPago(BoletoInterface $boleto){
        $this->boleto = $boleto;
    }

    public function validar(CanceladoraInterface $transporte, TiempoInterface $tiempo){
        
        // No se efectuó un viaje
        if($this->boleto->tiempo() === null || $this->boleto->transporte() === null || $this->boleto->pasaje() === null) return False;

        // Se deben viajes plus, no hay transbordo
        if($this->boleto->pasaje() === Pasajes::Plus) return False;
        
        $ventana = Transbordo::Extendido;

        $dia = $tiempo->tiempo("D");
        $fecha = $tiempo->tiempo("m-d");
        $hora = $tiempo->tiempo("H:i:s");
        
        // No es feriado
        if(array_search($fecha,$this->feriados) === false){
            // Sábado y medios feriados entre 6 y 14 hs
            if(($dia == "Sat" || array_search($fecha,$this->medios) !== false) && $hora >= "06:00:00" && $hora < "14:00:00"){
                $ventana = Transbordo::Regular;
            }
            // Dias de semana entre 6 y 22 hs que no son medios feriados
            else if(array_search($dia,Transbordo::Semana) !== false && array_search($fecha,$this->medios) === false && $hora >= "06:00:00" && $hora < "22:00:00"){
                $ventana = Transbordo::Regular;
            }
        }
        
        // Dentro de la ventana de tiempo y diferente colectivo
        $transbordo = (int)$tiempo->tiempo("U") - (int)$this->boleto->tiempo()->tiempo("U");
        if ($transbordo < $ventana && $this->boleto->transporte() != $transporte){
            return True;
        }

        // No hay transbordo
        return False;
    }
}