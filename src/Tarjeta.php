<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

    protected $tiempo;
    protected $saldo = 0;
    protected $plus_disponibles = 2;
    protected $tipo = 'normal';
    protected $id;
    protected $recarga_plus = 0; //0 no recargo plus, 1 1, 2 2
    protected $ultimoColectivo;
    protected $ultimoTrasbordo = false; //true el ultimo fue trasbordo false no
    protected $ultimoPago;

    public function __construct() {
      $this->tiempo = new TiempoFalso;
      $this->ultimoColectivo = new Colectivo(0,0,0);
    }

    public function recargar($monto, GestorDeMontosInterface $gestorDeMontos) {
      if ($montoValidado = $gestorDeMontos->montoACargar($monto)) {
        $this->saldo += $montoValidado;
      } else {
        return false;
      }
      if($this->saldo > ($gestorDeMontos->montoAPagar($tipo)*2)){
        if($this->plus_disponibles != 2){
          $this->saldo -= ($this->costo * (2 - $this->plus_disponibles));
          if($this->plus_disponibles === 0){
            $recarga_plus = 2;
          }else {
            $recarga_plus = 1;
          }
          $this->plus_disponibles = 2;
        }
      }
      return true;
    }

    public function pagarBoleto(ColectivoInterface $colectivo, GestorDeMontosInterface $gestorDeMontos){
      $costo = $gestorDeMontos->montoAPagar($this->tipo);
      if($this->sePuedeTransbordo($colectivo)){
        $costo = $costo * 0.33;
        $this->saldo -= $costo;
        $this->ultimoColectivo = $colectivo;
        $this->ultimoPago = $this->obtenerTiempo();
        $this->ultimoTrasbordo = False;
        return true;
      }else{
        if($this->saldo > $costo){
          $this->saldo -= $costo;
          $this->ultimoColectivo = $colectivo;
          $this->ultimoPago = $this->obtenerTiempo();
          $this->ultimoTrasbordo = True;
          return true;
        }else if($this->saldo < $this->costo && $this->plus_disponibles > 0){
          $this->restarPlus();
          $this->ultimoColectivo = $colectivo;
          $this->ultimoPago = $this->obtenerTiempo();
          return true;
        }else{
          return false;
      }
    }
  }

  /**
   * Devuelve false, si no se pudo restar un viaje plus, sino lo resta.
   *
   * @return bool
   */

    public function restarPlus(){
      if($this->plus_disponibles != 0){
        $this->plus_disponibles -= 1;
      }else{
        return false;
      }
    }

    /**
     * Devuelve la cantidad de plus disponibles de 0 a 2.
     *
     * @return int
     */

    public function obtenerPlus(){
      return $this->plus_disponibles;
    }

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     */

    public function obtenerSaldo() {
      return $this->saldo;
    }

    /**
     * Devuelve el costo del pasaje.
     *
     * @return float
     */

    public function obtenerCosto() {
      return $this->costo;
    }

    /**
     * Devuelve el tipo de la tarjeta.
     *
     * @return string
     */

    public function obtenerTipo(){
      return $this->tipo;
    }

    /**
     * Devuelve el ID de la tarjeta.
     *
     * @return int
     */

    public function obtenerID(){
      return $this->id;
    }

    /**
     * Devuelve la fecha actual.
     *
     * @return date
     */

    public function obtenerTiempo() {
      return $this->tiempo->time();
    }

    /**
     *@param int
     *
     * Avanza el tiempo.
     *
     * @return date
     */

    public function avanzarTiempo($tiempo) {
      return $this->tiempo->avanzar($tiempo);
    }

    /**
     * Devuelve el monto abonado en el ultimo pago de boleto.
     *
     * @return float
     */

    public function abonado(){ //al recargar se llama y calcula el monto total del viaje
        if($this->recarga_plus === 0){
          return $this->costo;
        }else if($this->recarga_plus === 1){
          $this->recarga_plus = 0;
          return ($this->costo * 2);
        }else{
          $this->recarga_plus = 0;
          return ($this->costo * 3);
          }
      }

     /**
      * Devuelve true en caso de poderse pagar un transbordo y false en el contrario.
      *
      * @return bool
      */

    public function sePuedeTransbordo($colectivo){
          if($colectivo->linea() != $this->ultimoColectivo->linea() && $this->ultimoTrasbordo === true && $this->saldo > $this->costo){
            $dia = date('w', $this->obtenerTiempo());
            $hora = date('G', $this->obtenerTiempo());

            if($dia > 0 && $dia < 6 && $hora > 6 && $hora < 22 && ($this->obtenerTiempo - $this->ultimoPago) < 3600){
              return true;
            }
            if($dia == 6 && $hora > 6 && $hora < 14 && ($this->obtenerTiempo - $this->ultimoPago) < 3600){
              return true;
            }
            if($dia == 6 && $hora > 14 && $hora < 22 && ($this->obtenerTiempo - $this->ultimoPago) < 5400){
              return true;
            }
            if(($dia == 0 /*|| feriado()*/ && $hora > 6 && $hora < 22 && ($this->obtenerTiempo - $this->ultimoPago)) < 5400){
              return true;
            }
        }
        else {
          return false;
        }
      }
}
