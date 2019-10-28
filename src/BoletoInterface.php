<?php

namespace TrabajoTarjeta;

interface BoletoInterface {

    /**
     * Registra información sobre un pago
     * 
     * @param float $saldo
     * @param Pasajes $pasaje
     * @param CanceladoraInterface $transporte
     * @param TiempoInterface $tiempo
     */
    public function nuevo(Float $saldo, $pasaje, CanceladoraInterface $transporte, TiempoInterface $tiempo);

    /**
     * Devuelve el saldo restante luego de pagar.
     * 
     * @return float
     */
    public function saldo();

    /**
     * Devuelve el pasaje pagado.
     * 
     * @return Pasajes
     */
    public function pasaje();

    /**
     * Devuelve el transporte usado.
     * 
     * @return CanceladoraInterface
     */
    public function transporte();

    /**
     * Devuelve el tiempo de cancelación.
     * 
     * @return TiempoInterface
     */
    public function tiempo();
}
