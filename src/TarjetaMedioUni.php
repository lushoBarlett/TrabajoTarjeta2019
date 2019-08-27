<?php

namespace TrabajoTarjeta;


class TarjetaMedioUni extends Tarjeta implements MedioInterface {
  protected $ultimoPagoMedio;
  protected $mediosDisponibles = 2;

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
