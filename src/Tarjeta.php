<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

    protected $tiempo;
    protected $costo = 14.80;
    protected $saldo = 0;
    protected $plus_disponibles = 2;
    protected $tipo = 'normal';
    protected $id;
    protected $recarga_plus = 0;//0 no recargo plus, 1 1, 2 2
    protected $ultimoColectivo;
    protected $ultimoTrasbordo = false; //true el ultimo fue trasbordo false no
    protected $ultimoPago;
    protected $feriados = array(0, 41, 42, 91, 120, 144, 167 , 170, 189, 231, 287, 322, 341, 359);

    public function __construct() {
      $this->tiempo = New TiempoFalso;
      $this->ultimoColectivo = New Colectivo(0,0,0);
    }

    /**
     *@param float
     *
     * Devuelve un bool, en el caso de que el monto se pueda recargar será true (y se recargará), sino false.
     *
     * @return bool
     */

    public function recargar($monto) {
      if (in_array($monto, array(10,20,30,50,100))) {
        $this->saldo += $monto;
      }
      else if($monto == 510.15) {
        $this->saldo += ($monto + 81.93);
      }
      else if($monto == 962.59) {
        $this->saldo += ($monto + 221.58);
      }
      else {
        return false;
      }
      if($this->saldo > ($this->costo*2)){
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

    /**
     *@param ColectivoInterface
     *
     * Descuenta un viaje de ser posible, sino devuelve false. Al restar un viaje, devuelve t si es transbordo, p si es plus, y true si es boleto normal
     *
     * @return float, char
     */

    public function restarViaje($colectivo){
      $this->costo = 14.80;
      if($this->sePuedeTransbordo($colectivo)){
        $this->costo = $this->costo * 0.33;
        $this->saldo -= $this->costo;
        $this->ultimoColectivo = $colectivo;
        $this->ultimoPago = $this->obtenerTiempo();
        $this->ultimoTrasbordo = False;
        return 't';
      }else{
        if($this->saldo > $this->costo){
          $this->saldo -= $this->costo;
          $this->ultimoColectivo = $colectivo;
          $this->ultimoPago = $this->obtenerTiempo();
          $this->ultimoTrasbordo = True;
          return true;
        }else if($this->saldo < $this->costo && $this->plus_disponibles > 0){
          $this->restarPlus();
          $this->ultimoColectivo = $colectivo;
          $this->ultimoPago = $this->obtenerTiempo();
          return 'p';
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
            if(($dia == 0 || $this->esFeriado(date('z', $this->obtenerTiempo())) && $hora > 6 && $hora < 22 && ($this->obtenerTiempo - $this->ultimoPago)) < 5400){
              return true;
            }
        }
        else {
          return false;
        }
      }

      /**
       *@param int
       *
       * Devuelve true si el dia que se le pasa es un feriado, false en el contrario
       *
       * @return bool
       */

      protected function esFeriado($dia){
        array_search($dia, $this->feriados) != null;
      }
}
