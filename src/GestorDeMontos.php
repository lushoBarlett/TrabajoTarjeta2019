<?php

namespace TrabajoTarjeta;

class GestorDeMontos implements GestorDeMontosInterface {
    
    protected $montosDeViaje;
    protected $montosDeRecarga;

    /**
     * @param array $viaje Array asociativo donde las llaves son los tipos validos de la tarjeta
     * y los valores son los montos asociados a dichos tipos
     * 
     * @param array $recargas Array asociativo donde las llaves son los tipos validos de la tarjeta
     * y los valores son los montos asociados a dichos tipos
     */
    public function __construct($viaje = [], $recargas = []) {
        $this->montosDeViaje = $viaje;
        $this->montosDeRecarga = $recargas;
    }

    public function montoAPagar($tipo) {
        if(!array_key_exists($tipo,$this->montosDeViaje)) return null;
        return $this->montosDeViaje[$tipo];
    }

    public function montoACargar($monto) {
        if(!array_key_exists((string)$monto,$this->montosDeRecarga)) return null;
        return $this->montosDeRecarga[(string)$monto];
    }
}
