<?php

namespace TrabajoTarjeta;

interface MontosInterface {

    /**
     * Retorna el monto a pagar dado el tipo de la tarjeta
     *
     * @param string $tipo El tipo de la tarjeta
     *
     * @return float El monto que la tarjeta debe pagar, null si el tipo no es válido
     * 
     */
    public function montoAPagar($tipo);
    
    /**
     * Retorna la validez de un monto a recargar
     *
     * @param float $monto El monto que se quiere cargar
     *
     * @return float Valor a recargar, null si el monto no es válido
     * 
     */
    public function montoACargar($monto);

}