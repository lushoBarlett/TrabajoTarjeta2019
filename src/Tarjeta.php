<?php

namespace TrabajoTarjeta;

/**
 * Siempre debe coincidir con todos los tipos de PAGO
 */
class Tipos {
  // precio completo
  const Normal = 0;
  // mitad de precio
  const Medio = 1;
  // gratis
  const Libre = 2;
}

/**
 * Tipos de operaciones de pago
 */
class Pasajes {
  // operacion fallida
  const Fallido = -1;
  // precio normal dependiendo de la tarjeta
  const Normal = 0;
  // precio completo independientemente de la tarjeta
  const Completo = 1;
  // precio completo prestado
  const Plus = 2;
  // precio del transbordo (suponiendo un unico precio de transbordo)
  const Transbordo = 3;
}

class Tarjeta implements TarjetaInterface {

  protected $tiempo;
  protected $saldo = 0;
  protected $plus_disponibles = 2;
  protected $tipo;
  protected $id;
  protected $recarga_plus = 0; //0 no recargo plus, 1 1, 2 2
  protected $ultimoColectivo;
  protected $ultimoTrasbordo = false; //true el ultimo fue trasbordo false no
  protected $ultimoPago;
  protected $ultimoCosto;

  public function __construct() {
    $this->tipo = Tipos::Normal;
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

  public function pagarBoleto(ColectivoInterface $colectivo, GestorDeMontosInterface $gestorDeMontos, $override = null){
    $costo;
    // fuerza un costo (opcional), los chequeos de si se puede corren igual
    if($override){
      $costo = $gestorDeMontos->montoAPagar($override);
    } else{
      $costo = $gestorDeMontos->montoAPagar($this->tipo);
    }
    $this->recarga_plus = 2 - $this->plus_disponibles;

    // hay plus que pagar y se pueden pagar
    if($this->saldo > $gestorDeMontos->montoAPagar(Tipos::Normal) * $this->recarga_plus){
      $costo += $gestorDeMontos->montoAPagar(Tipos::Normal) * $this->recarga_plus;
      $this->plus_disponibles = 2;
      $this->recarga_plus = 0;
    }

    // transbordo
    if($this->sePuedeTransbordo($colectivo)){
      $costo = 0;
      $this->ultimoTrasbordo = False;
      $pasaje = Pasajes::Transbordo;
    }
    // pasaje normal de la tarjeta, cambia depende de la tarjeta
    else if($this->saldo >= $costo){
      $this->saldo -= $costo;
      $this->ultimoTrasbordo = True;
      $pasaje = Pasajes::Normal;
    }
    // viaje plus, hay solo dos y salen lo que un precio de tarjeta normal
    else if($this->saldo < $costo && $this->plus_disponibles > 0){
      $this->restarPlus();
      $pasaje = Pasajes::Plus;
    }
    // falla el proceso, no se puede viajar
    else{
      return Pasajes::Fallido;
    }
    $this->ultimoCosto = $costo;
    $this->ultimoColectivo = $colectivo;
    $this->ultimoPago = $this->obtenerTiempo();
    return $pasaje;
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
    if($colectivo->linea() != $this->ultimoColectivo->linea() && $this->ultimoTrasbordo === true && $this->saldo > $this->costo && $this->plus_disponibles == 2){
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