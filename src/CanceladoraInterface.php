<?php

namespace TrabajoTarjeta;

interface CanceladoraInterface {

    /**
     * Devuelve un mensaje según el boleto.
     *
     * @param BoletoInterface $boleto Información del último pago.
     *
     * @return string Mensaje relacionado al pago.
     */
    public function pagadoCon(BoletoInterface $boleto);

}
