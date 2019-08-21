<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Recarga una tarjeta con un monto.
     *
     * @param float $monto
     * @param GestorDeMontosInterface $gestorDeMontos
     *
     * @return bool Devuelve True si el monto a cargar es válido, o False en caso de que no
     * sea valido.
     */
    public function recargar($monto, GestorDeMontosInterface $gestorDeMontos);

    /**
     * Intenta pagar el boleto.
     *
     * @param ColectivoInterface $colectivo El colectivo que requiere el pago
     * @param GestorDeMontosInterface $gestorDeMontos Encargado de chequear montos a pagar
     * 
     * @return bool Retorna True si se pagó con éxito, False en caso contrario
     */
    public function pagarBoleto(ColectivoInterface $colectivo, GestorDeMontosInterface $gestorDeMontos);

}
