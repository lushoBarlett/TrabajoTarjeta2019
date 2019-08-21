<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

  const viajes = array("libre" => 0, "medio" => 13.75, "normal" => 27.50);

  protected $tiempo;
  protected $saldo = 0;
  protected $plus_disponibles = 2;
  protected $tipo = 'normal';
  protected $id;
  protected $recarga_plus = 0; //0 no recargo plus, 1 1, 2 2
  protected $ultimoColectivo;
  protected $ultimoTrasbordo = false; //true el ultimo fue trasbordo false no
  protected $ultimoPago;
  protected $ultimoCosto;

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
    return true;
  }

  public function pagarBoleto(ColectivoInterface $colectivo, GestorDeMontosInterface $gestorDeMontos){
    $costo = $gestorDeMontos->montoAPagar($this->tipo);
    $this->recarga_plus = 2 - $this->plus_disponibles;

    // hay plus que pagar
    if($this->saldo > $costo * $this->recarga_plus){
      $costo += (Tarjeta::viajes[$this->tipo] * $this->recarga_plus);
      $this->plus_disponibles = 2;
    }
    if($this->sePuedeTransbordo($colectivo)){
      $costo = 0;
      $this->ultimoCosto = $costo;
      $this->ultimoColectivo = $colectivo;
      $this->ultimoPago = $this->obtenerTiempo();
      $this->ultimoTrasbordo = False;
      return true;
    }else{
      if($this->saldo >= $costo){
        $this->saldo -= $costo;
        $this->ultimoCosto = $costo;
        $this->ultimoColectivo = $colectivo;
        $this->ultimoPago = $this->obtenerTiempo();
        $this->ultimoTrasbordo = True;
        return true;
      }else if($this->saldo < $costo && $this->plus_disponibles > 0){
        $this->restarPlus();
        $this->ultimoCosto = $costo;
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
    if($this->plus_disponibles > 0){
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
      return $this->ultimoCosto;
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