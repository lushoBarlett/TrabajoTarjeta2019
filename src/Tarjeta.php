<?php

namespace TrabajoTarjeta;

class Tarjeta implements PagoRecargableInterface {

  use TarjetaBase;

  const MAX_PLUS = 2;

  private function __init(){
    $this->tipo = Tipos::Normal;
    $this->plus = Tarjeta::MAX_PLUS;
  }
  
  public function pagarBoleto(CanceladoraInterface $transporte, TiempoInterface $tiempo){
    
    // transbordo
    if($this->boleto->pasaje() != Pasajes::Transbordo){
      $this->transbordo->ultimoPago($this->boleto);
    }
    if($this->transbordo->validar($transporte,$tiempo)){
      $this->boleto->nuevo($this->saldo,Pasajes::Transbordo,$transporte,$tiempo);
      return $this->boleto;
    }
    
    $costo_normal = $this->montos->montoAPagar(Pasajes::Normal);
    
    // pasaje normal
    if($this->saldo >= $costo_normal){
      $this->saldo -= $costo_normal;

      // saldar plus si se puede
      while($this->saldo >= $costo_normal && $this->plus < Tarjeta::MAX_PLUS){
        $this->saldo -= $costo_normal;
        $this->plus++;
      }

      $this->boleto->nuevo($this->saldo,Pasajes::Normal,$transporte,$tiempo);
      return $this->boleto;
    }
    
    // viaje plus
    if($this->plus > 0){
      $this->restarPlus();
      $this->boleto->nuevo($this->saldo,Pasajes::Plus,$transporte,$tiempo);
      return $this->boleto;
    }
    
    // fallo
    $this->boleto->nuevo($this->saldo,Pasajes::Fallido,$transporte,$tiempo);
    return $this->boleto;
  }

}