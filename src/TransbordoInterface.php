<?php

namespace TrabajoTarjeta;

interface TransbordoInterface {
    
    /**
     * Devuelve true o false de si se puede hacer transbordo.
     * 
     * @param TarjetaInterface $tarjeta Tarjeta con la cual se paga
     * @param ColectivoInterface $colectivo Colectivo en el cual se intenta hacer transbordo
     * @param TiempoInterface $tiempo Gestor de tiempo global
     * 
     * @return bool
     */
    public function sePuedeTransbordo(TarjetaInterface $tarjeta, ColectivoInterface $colectivo, TiempoInterface $tiempo);
}