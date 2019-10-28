<?php

namespace TrabajoTarjeta;

interface TransbordoInterface {
    
    /**
     * Devuelve true o false de si se puede hacer transbordo.
     * 
     * @param CanceladoraInterface $transporte Transporte usado
     * @param TiempoInterface $tiempo Gestor de tiempo global
     * 
     * @return bool
     */
    public function validar(CanceladoraInterface $transporte, TiempoInterface $tiempo);

    /**
     * Guarda el último boleto que no fue trasbordo para realizar operaciones
     * 
     * @param BoletoInterface $boleto Último boleto pago
     */
    public function ultimoPago(BoletoInterface $boleto);
}