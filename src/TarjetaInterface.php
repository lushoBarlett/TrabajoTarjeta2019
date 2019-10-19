<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

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
     * @param ColectivoInterface $colectivo El colectivo que requiere el pago
     * @param MontosInterface $montos Encargado de chequear montos a pagar
     * @param TransbordoInterface $transbordo Encargado de chequear reglas de transbordo
     * @param TiempoInterface $tiempo Reloj global
     * 
     * @return bool Retorna True si se pagó con éxito, False en caso contrario
     */
    public function pagarBoleto(ColectivoInterface $colectivo, MontosInterface $montos, TransbordoInterface $transbordo, TiempoInterface $tiempo);
    
    /**
     * Obtiene la informacion de la tarjeta.
     * 
     * @return array Array asociativo [propiedad => valor]
     */
    public function informacion();

}

?>