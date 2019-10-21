<?php

namespace TrabajoTarjeta;


class TarjetaLibre implements PagoRecargableInterface {

  use TarjetaBase;

  public function __construct() {
    $this->tipo = Tipos::Libre;
  }

  public function pagarBoleto(CanceladoraInterface $transporte, MontosInterface $montos, TiempoInterface $tiempo, TransbordoInterface $transbordo){
    return $this->boleto->nuevo($this->saldo,Pasajes::Libre,$transporte,$tiempo);
  }

}
