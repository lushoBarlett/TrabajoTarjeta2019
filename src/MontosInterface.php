<?php

namespace TrabajoTarjeta;

interface MontosInterface {

    /**
     * Retorna el monto a pagar dado el tipo de la tarjeta
     *
     * @param Pasaje $tipo El tipo de pasaje a pagar
     *
     * @return float El monto que cuesta el pasaje, null si el pasaje no es válido
     * 
     */
    public function montoAPagar($tipo);
    
    /**
     * Retorna la validez de un monto a recargar
     *
     * @param string $monto El monto que se quiere cargar
     *
     * @return float Valor a recargar, null si el monto no es válido
     * 
     */
    public function montoACargar($monto);

}