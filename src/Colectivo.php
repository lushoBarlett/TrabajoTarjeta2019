<?php

namespace TrabajoTarjeta;

class Colectivo implements CanceladoraInterface {
    
  protected $linea;
  protected $empresa;
  protected $numero;

  public function __construct($linea = 0, $empresa = 0, $numero = 0){
    $this->linea = $linea;
    $this->empresa = $empresa;
    $this->numero = $numero;
  }

  public function pagadoCon(BoletoInterface $boleto){
    switch ($boleto->pasaje()) {
      case Pasajes::Libre:
        return "Pase Libre.";

      case Pasajes::Transbordo:
        return "Transbordo. El saldo es {$boleto->saldo()}.";

      case Pasajes::Medio:
        return "Medio Boleto. El saldo es {$boleto->saldo()}.";

      case Pasajes::Normal:
        return "Normal. El saldo es {$boleto->saldo()}.";

      case Pasajes::Plus:
        return "Viaje Plus. El saldo es {$boleto->saldo()}.";

      case Pasajes::Fallido:
        return "Debe dos viajes plus.";
        
      default:
        return "Error en la operacion.";
    }
  }

}
