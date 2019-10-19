<?php

namespace TrabajoTarjeta;


class TarjetaMedioUni extends Tarjeta {
  protected $ultimoPagoMedio;
  protected $mediosDisponibles = 2;

  public function __construct() {
    parent::__construct();
    $this->tipo = Tipos::Medio;
  }

  public function pagarBoleto(ColectivoInterface $colectivo){
    $result = parent::pagarBoleto($colectivo);
    if($this->sePuedePagar()){
      $result = $result == Pasajes::Normal ? Pasajes::Completo : $result;
    }
    return $result;
  }
    /**
     *  Determina si es posible realizar un pago dependiendo de los limites propuestos.
     */
  public function sePuedePagar(){
    $ahora = $this->obtenerTiempo();
    if(date('d', $ahora) == (date('d', $this->ultimoPagoMedio) + 1)) {
    $this->mediosDisponibles = 2;
    }
    if(($ahora - $this->ultimoPago) >= 300 && $this->mediosDisponibles != 0){
      return true;
    }else{
      return false;
    }
  }
}
