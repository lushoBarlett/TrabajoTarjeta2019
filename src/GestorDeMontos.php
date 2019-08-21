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
        $montosDeViaje = $viaje;
        $montosDeRecarga = $recargas;
    }

    public function montoAPagar($tipo) {
        if(array_key_exists($tipo,$montosDeViaje)) return null;
        return $montosDeViaje[$tipo];
    }

    public function montoACargar($monto) {
        if(array_key_exists($monto,$montosDeRecarga)) return null;
        return $montosDeRecarga[$monto];
    }
}
