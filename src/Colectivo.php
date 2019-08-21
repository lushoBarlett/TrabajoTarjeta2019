<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {
    protected $linea;
    protected $empresa;
    protected $numero;

    const viajes = array("libre" => 0, "medio" => 13.75, "normal" => 27.50);
    const recargas = array("10" => 10, "30" => 30, "50" => 50, "100" => 100, "200" => 200, "947.60" => 1100, "1788.80" => 2200);
    
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
      $pago = $tarjeta->pagarBoleto($this,$gestor);
      if($pago === false){
        return false;//no tiene saldo
      }else if($pago === true){
        return New Boleto($tarjeta->abonado(), $this, $tarjeta, 'normal');//boleto normal
      }else if ($pago === 'p'){
        return New Boleto($tarjeta->abonado(), $this, $tarjeta, 'plus');//boleto plus
      }else if ($pago === 't'){
        return New Boleto($tarjeta->abonado(), $this, $tarjeta, 'trasbordo');// boleto trasbordo
      }
    }
}
