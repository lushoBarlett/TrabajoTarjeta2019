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

    /**
     * Devuelve el numero de linea del colectivo.
     *
     * @return int
     */
    public function linea(){
      return $this->linea;
    }

    /**
     * Devuelve el nombre de la empresa.
     *
     * @return int
     */
    public function empresa(){
      return $this->empresa;
    }

    /**
     * Devuelve el numero del colectivo.
     *
     * @return int
     */
    public function numero(){
      return $this->numero;
    }
    
    /**
     * Genera los valores del boleto dependiendo del tipo de tarjeta y el saldo de la misma.
     * 
     * @return Boleto
     */
    public function pagarCon(TarjetaInterface $tarjeta, GestorDeMontosInterface $gestor){
      $tipoDePasaje = $tarjeta->pagarBoleto($this,$gestor);
      if($tipoDePasaje === Pasajes::Fallido){
        return null;
      }
      return New Boleto($tarjeta->abonado(), $this, $tarjeta, $tipoDePasaje);
    }
}
