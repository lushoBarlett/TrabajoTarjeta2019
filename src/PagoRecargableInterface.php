<?php

namespace TrabajoTarjeta;

interface PagoRecargableInterface {

    /**
     * Recarga una tarjeta con un monto.
     *
     * @param float $monto
     *
     * @return bool Devuelve True si el monto a cargar es válido y carga dicho monto, o False en caso de que no
     * sea valido.
     */
    public function recargar($monto);

    /**
     * Intenta pagar el boleto.
     *
     * @param CanceladoraInterface $transporte El transporte que requiere el pago
     * @param TiempoInterface $tiempo Reloj global
     * 
     * @return BoletoInterface Retorna un boleto con la informacion del pago
     */
    public function pagarBoleto(CanceladoraInterface $transporte, TiempoInterface $tiempo);

    /**
     * Devuelve el saldo.
     * 
     * @return float
     */
    public function saldo();

}

?>