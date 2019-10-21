<?php

namespace TrabajoTarjeta;

class Tarjeta implements PagoRecargableInterface {

  use TarjetaBase;

  const MAX_PLUS = 2;

  protected $boleto;

  public function __construct(BoletoInterface $boleto) {
    $this->boleto = $boleto;
    $this->tipo = Tipos::Normal;
    $this->plus = Tarjeta::MAX_PLUS;
  }

  public function pagarBoleto(CanceladoraInterface $transporte, MontosInterface $montos, TiempoInterface $tiempo, TransbordoInterface $transbordo){
    
    // transbordo
    if($transbordo->validar($this->boleto,$transporte,$tiempo)){
      return $this->boleto->nuevo($this->saldo,Pasajes::Transbordo,$transporte,$tiempo);
    }
    
    $costo_normal = $montos->montoAPagar(Pasajes::Normal);
    
    // pasaje normal
    if($this->saldo >= $costo_normal){
      $this->saldo -= $costo_normal;

      // saldar plus si se puede
      while($this->saldo >= $costo_normal && $this->plus < Tarjeta::MAX_PLUS){
        $this->saldo -= $costo_normal;
        $this->plus++;
      }
    }
    
    // viaje plus
    if($this->plus > 0){
      $this->restarPlus();
      return $this->boleto->nuevo($this->saldo,Pasajes::Plus,$transporte,$tiempo);
    }
    
    // fallo
    return $this->boleto->nuevo($this->saldo,Pasajes::Fallido,$transporte,$tiempo);
  }

}