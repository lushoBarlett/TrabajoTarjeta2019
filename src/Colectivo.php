<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {
    
  protected $linea;
  protected $empresa;
  protected $numero;

  public function __construct($linea = 0, $empresa = 0, $numero = 0){
    $this->linea = $linea;
    $this->empresa = $empresa;
    $this->numero = $numero;
  }

  public function pagarCon(TarjetaInterface $tarjeta, MontosInterface $gestor, TiempoInterface $tiempo, TransbordoInterface $transbordo){
    $info = $tarjeta->information(); // no hay garantias sobre el contenido, hacer interfaces especificas a las necesidades de las clases

    // hacer trabajo con las dos ultimas clases asi no las paso como parametro en la siguiente funcion

    $tipoDePasaje = $tarjeta->pagarBoleto($this,$gestor,$tiempo,$transbordo);
    if($tipoDePasaje === Pasajes::Fallido){
      return null;
    }
    return "";
  }

  public function information(){
    return (object)[
      "linea" => $this->linea,
      "empresa" => $this->empresa,
      "numero" => $this->numero
    ];
  }
}
