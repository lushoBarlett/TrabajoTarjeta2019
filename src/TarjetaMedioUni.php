<?php

namespace TrabajoTarjeta;


class TarjetaMedioUni implements PagoRecargableInterface {
  
  use TarjetaBase;

  const MAX_PLUS = 2;
  const MAX_MEDIOS = 2;

  protected $medios;

  public function __construct() {
    $this->tipo = Tipos::MedioUni;
    $this->plus = TarjetaMedioUni::MAX_PLUS;
    $this->medios = TarjetaMedioUni::MAX_MEDIOS;
  }

  public function pagarBoleto(CanceladoraInterface $transporte, MontosInterface $montos, TiempoInterface $tiempo, TransbordoInterface $transbordo){
    
    // transbordo
    if($transbordo->validar($this->boleto,$transporte,$tiempo)){
      return $this->boleto->nuevo($this->saldo,Pasajes::Transbordo,$transporte,$tiempo);
    }
    
    $costo_normal = $montos->montoAPagar(Pasajes::Normal);
    $costo_medio = $montos->montoAPagar(Pasajes::Medio);
    
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

      return $this->boleto->nuevo($this->saldo,$pasaje,$transporte,$tiempo);
    }
    
    // viaje plus
    if($this->plus > 0){
      $this->restarPlus();
      return $this->boleto->nuevo($this->saldo,Pasajes::Plus,$transporte,$tiempo);
    }
    
    // fallo
    return $this->boleto->nuevo($this->saldo,Pasajes::Fallido,$transporte,$tiempo);
  }
  
  /**
   * Control de tiempo para el medio boleto universitario
   * 
   * @return bool Si se puede pagar o no
   */
  protected function validarMedio(TiempoInterface $tiempo){
    
    // no hubo viajes
    if($this->boleto === null){
      return true;
    }

    $this->resetearMedios($tiempo);

    $ultimotiempo = $this->boleto->tiempo();
    // pasaron más de 5 minutos desde el último uso del medio
    if ($this->boleto->pasaje() != Pasajes::Medio || ((int)$tiempo->tiempo("U") - (int)$ultimotiempo->tiempo("U")) > 5 * 60){
      return true;
    }
    return false;
  }

  /**
   * Resetea los medios boletos disponibles en el día si corresponde
   */
  protected function resetearMedios(TiempoInterface $tiempo){
    if ((int)$tiempo->tiempo("Ymd") - (int)$this->boleto->tiempo("Ymd") > 0){
      $this->medios = TarjetaMedioUni::MAX_MEDIOS;
    }
  }
}
