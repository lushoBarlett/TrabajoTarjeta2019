<?php

namespace TrabajoTarjeta;

trait TarjetaBase{

  protected $saldo = 0;
  protected $tipo;
  protected $id;
  protected $plus;
  protected $montos;
  protected $boleto;
  protected $transbordo;

  public function __construct(BoletoInterface $boleto, MontosInterface $montos, TransbordoInterface $transbordo) {
    $this->boleto = $boleto;
    $this->montos = $montos;
    $this->transbordo = $transbordo;
    $this->__init();
  }
  
  public function recargar($monto) {
    if ($montoValidado = $this->montos->montoACargar($monto)) {
      $this->saldo += $montoValidado;
      return true;
    }
    return false;
  }

  public function saldo(){
    return $this->saldo;
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