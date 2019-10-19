<?php

namespace TrabajoTarjeta;


class TarjetaLibre extends Tarjeta {

  public function __construct() {
    parent::__construct();
    $this->tipo = Tipos::Libre;
  }

  public function pagarBoleto(ColectivoInterface $colectivo){
    return Pasajes::Normal;
  }

}
