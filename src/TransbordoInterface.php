<?php

namespace TrabajoTarjeta;

interface TransbordoInterface {
    
    /**
     * Devuelve true o false de si se puede hacer transbordo.
     * 
     * @param PagoRecargableInterface $pago Medio de pago
     * @param CanceladoraInterface $canceladora Canceladora con información sobre el transporte
     * @param TiempoInterface $tiempo Gestor de tiempo global
     * 
     * @return bool
     */
    public function validar(PagoRecargableInterface $pago, CanceladoraInterface $canceladora, TiempoInterface $tiempo);
}