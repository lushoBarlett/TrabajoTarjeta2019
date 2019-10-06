<?php

namespace TrabajoTarjeta;

interface MedioInterface {

    /**
     * Chequea si se cumplen las reglas de la tarjeta para pagar medio.
     * 
     * @return bool
     */
    function sePuedePagar();

}

?>