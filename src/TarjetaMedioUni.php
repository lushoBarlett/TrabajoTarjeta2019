<?php

namespace TrabajoTarjeta;


class TarjetaMedioUni implements PagoRecargableInterface {
  
  use TarjetaBase;

  const MAX_PLUS = 2;
  const MAX_MEDIOS = 2;

  protected $medios;

  private function __init() {
    $this->tipo = Tipos::MedioUni;
    $this->plus = TarjetaMedioUni::MAX_PLUS;
    $this->medios = TarjetaMedioUni::MAX_MEDIOS;
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
    $costo_medio = $this->montos->montoAPagar(Pasajes::Medio);
    
    // pasaje normal
    if($this->saldo >= $costo_medio){

      $pasaje;

      // medio
      if($this->validarMedio($tiempo)){
        $this->saldo -= $costo_medio;
        $this->medios--;
        $pasaje = Pasajes::Medio;
      }
      // normal
      else if($this->saldo >= $costo_normal){
        $this->saldo -= $costo_normal;
        $pasaje = Pasajes::Normal;
      }

      // saldar plus si se puede
      while($this->saldo >= $costo_normal && $this->plus < TarjetaMedioUni::MAX_PLUS){
        $this->saldo -= $costo_normal;
        $this->plus++;
      }

      $this->boleto->nuevo($this->saldo,$pasaje,$transporte,$tiempo);
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
  
  /**
   * Control de tiempo para el medio boleto universitario
   * 
   * @return bool Si se puede pagar o no
   */
  protected function validarMedio(TiempoInterface $tiempo){

    // no hubo viajes
    if($this->boleto->pasaje() === null) return true;

    $this->resetearMedios($tiempo);

    $ultimotiempo = $this->boleto->tiempo();
    // pasaron más de 5 minutos desde el último uso del medio o el último boleto no fue medio, y hay medios disponibles
    if (($this->boleto->pasaje() != Pasajes::Medio || ((int)$tiempo->tiempo("U") - (int)$ultimotiempo->tiempo("U")) > 5 * 60) && $this->medios > 0){
      return true;
    }
    return false;
  }

  /**
   * Resetea los medios boletos disponibles en el día si corresponde
   */
  protected function resetearMedios(TiempoInterface $tiempo){
    if ((int)$tiempo->tiempo("Ymd") - (int)$this->boleto->tiempo()->tiempo("Ymd") > 0){
      $this->medios = TarjetaMedioUni::MAX_MEDIOS;
    }
  }
}
