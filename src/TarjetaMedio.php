<?php

namespace TrabajoTarjeta;


class TarjetaMedio extends Tarjeta {
  protected $tipo = 'medio';
  protected $ultimoPago;
  protected $costo = 7.40;

  public function restarViaje($colectivo){
    if($this->sePuedeTransbordo($colectivo)){
      $this->costo = $this->costo * 0.33;
      $this->saldo -= $this->costo;
      $this->ultimoColectivo = $colectivo;
      $this->ultimoPago = $this->obtenerTiempo();
      $this->ultimoTrasbordo = False; 
      return 't';
    }else{
      if($this->sePuedePagar() === true){
        if($this->saldo > $this->costo){
          $this->saldo -= $this->costo;
          $this->ultimoPago = $this->obtenerTiempo();
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
        return false;
      }
    }
  }
    public function sePuedePagar(){
      $ahora = $this->obtenerTiempo();
      if(($ahora - $this->ultimoPago) >= 300){
        return true;
      }else{
        return false;
      }
    }

}