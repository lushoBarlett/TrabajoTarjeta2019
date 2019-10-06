<?php

namespace TrabajoTarjeta;


class TarjetaMedio extends Tarjeta implements MedioInterface {

  public function __construct() {
    parent::__construct();
    $this->tipo = Tipos::Medio;
  }

  public function pagarBoleto(ColectivoInterface $colectivo, GestorDeMontosInterface $gestorDeMontos){
    $result = parent::pagarBoleto($colectivo,$gestorDeMontos);
    if($this->sePuedePagar){
      $result = $result == Pasajes::Normal ? Pasajes::Completo : $result;
    }
    return $result;
  }

  /**
   * Control de tiempo para el medio boleto
   * 
   * @return bool Si se puede pagar o no
   */
  protected function sePuedePagar(){
    $now = $this->obtenerTiempo();
    if(($now - $this->ultimoPago) >= 300){
      return true;
    }else{
      return false;
    }
  }

}