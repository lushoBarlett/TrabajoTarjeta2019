<?php

namespace TrabajoTarjeta;

interface BoletoInterface {

    /**
     * Registra información sobre un pago
     * 
     * @param float $saldo
     * @param Pasaje $pasaje
     * @param CanceladoraInterface $transporte
     * @param string $tiempo
     */
    public function nuevo($saldo, $pasaje, CanceladoraInterface $transporte, String $tiempo);

    /**
     * Devuelve el saldo restante luego de pagar.
     * 
     * @return float
     */
    public function saldo();

    /**
     * Devuelve el pasaje pagado.
     * 
     * @return Pasaje
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
     * @return string
     */
    public function tiempo();
}
