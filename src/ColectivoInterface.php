<?php

namespace TrabajoTarjeta;

interface ColectivoInterface {

    /**
     * Paga un viaje en el colectivo con una tarjeta en particular.
     *
     * @param TarjetaInterface $tarjeta Tarjeta con la cual se paga.
     * @param GestorDeMontoInterface $gestor Gestor de pago automático.
     *
     * @return string El saldo de la tarjeta, o un error
     */
    public function pagarCon(TarjetaInterface $tarjeta, MontosInterface $gestor, TiempoInterface $tiempo, TransbordoInterface $transbordo);

    /**
     * Obtiene la informacion de la tarjeta.
     * 
     * @return array Array asociativo [propiedad => valor]
     */
    public function informacion();

}
