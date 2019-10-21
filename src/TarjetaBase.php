<?php

namespace TrabajoTarjeta;

trait TarjetaBase{

  protected $saldo = 0;
  protected $tipo;
  protected $id;
  protected $plus;
  
  public function recargar($monto, MontosInterface $montos) {
    if ($montoValidado = $montos->montoACargar($monto)) {
      $this->saldo += $montoValidado;
    } else {
      return false;
    }
    return true;
  }

  /**
  * Devuelve false si no se pudo restar un viaje plus disponible, sino lo resta y devuelve true
  *
  * @return bool
  */
  protected function restarPlus(){
    if($this->plus > 0){
      $this->plus -= 1;
      return true;
    }
    return false;
  }
}

?>