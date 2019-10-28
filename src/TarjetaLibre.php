<?php

namespace TrabajoTarjeta;


class TarjetaLibre implements PagoRecargableInterface {

  use TarjetaBase;

  private function __init() {
    $this->tipo = Tipos::Libre;
  }

  public function pagarBoleto(CanceladoraInterface $transporte, TiempoInterface $tiempo){
    $this->boleto->nuevo($this->saldo,Pasajes::Libre,$transporte,$tiempo);
    return $this->boleto;
  }

}
