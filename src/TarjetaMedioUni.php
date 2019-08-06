<?php

namespace TrabajoTarjeta;


class TarjetaMedioUni extends Tarjeta {
  protected $tipo = 'medio';
  protected $ultimoPago;
  protected $ultimoPagoMedio;
  protected $mediosDisponibles = 2;
  protected $costo = 7.40;
  public function restarViaje($colectivo){
    if($this->sePuedeTransbordo($colectivo)){
      $this->costo = $this->costo * 0.77;
      $this->saldo -= $this->costo;
      $this->ultimoColectivo = $colectivo;
      $this->ultimoPago = $this->obtenerTiempo();
      $this->ultimoTrasbordo = False; 
      return 't';
    }else{
      if($this->sePuedePagar() === true){
        $this->costo = 7.40;
        if($this->saldo > $this->costo){
          $this->saldo -= $this->costo;
          $this->ultimoPago = $this->obtenerTiempo();
          $this->ultimoPagoMedio = $this->obtenerTiempo();
          $this->mediosDisponibles -= 1;
          $this->ultimoTrasbordo = True; 
          return true;
        }else if($this->saldo < $this->costo && $this->plus_disponibles > 0){
          $this->restarPlus();
          $this->ultimoPago = $this->obtenerTiempo();
          return 'p';
        }else {
          return false;
        }
      }else{
        $this->costo = 14.80;
        if($this->saldo > $this->costo){
          $this->saldo -= $this->costo;
          $this->ultimoPago = $this->obtenerTiempo();
          return true;
        }else if($this->saldo < $this->costo && $this->plus_disponibles > 0){
          $this->restarPlus();
          $this->ultimoPago = $this->obtenerTiempo();
          return 1;
        }else {
          return false;
        }
      }
    }
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
