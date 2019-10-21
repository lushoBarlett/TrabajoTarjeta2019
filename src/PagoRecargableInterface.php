<?php

namespace TrabajoTarjeta;

interface PagoRecargableInterface {

    /**
     * Recarga una tarjeta con un monto.
     *
     * @param float $monto
     * @param MontosInterface $montos
     *
     * @return bool Devuelve True si el monto a cargar es válido, o False en caso de que no
     * sea valido.
     */
    public function recargar($monto, MontosInterface $montos);

    /**
     * Intenta pagar el boleto.
     *
     * @param CanceladoraInterface $transporte El transporte que requiere el pago
     * @param MontosInterface $montos Encargado de chequear montos a pagar
     * @param TiempoInterface $tiempo Reloj global
     * @param TransbordoInterface $transbordo Encargado de chequear reglas de transbordo
     * 
     * @return BoletoInterface Retorna un boleto con la informacion del pago
     */
    public function pagarBoleto(CanceladoraInterface $transporte, MontosInterface $montos, TiempoInterface $tiempo, TransbordoInterface $transbordo);

}

?>