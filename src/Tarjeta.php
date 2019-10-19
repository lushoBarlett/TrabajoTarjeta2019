<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

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
    $this->ultimoColectivo = new Colectivo(0,0,0);
  }

  public function recargar($monto, MontosInterface $montos) {
    if ($montoValidado = $montos->montoACargar($monto)) {
      $this->saldo += $montoValidado;
    } else {
      return false;
    }
    return true;
  }

  public function pagarBoleto(ColectivoInterface $colectivo, MontosInterface $montos, GestorDeTransbordoInterface $gestorDeTransbordo){
    $costo = $montos->montoAPagar($this->tipo);
    $this->recarga_plus = 2 - $this->plus_disponibles;

    // hay plus que pagar y se pueden pagar
    if($this->saldo > $montos->montoAPagar(Tipos::Normal) * $this->recarga_plus){
      $costo += $this->montos->montoAPagar(Tipos::Normal) * $this->recarga_plus;
      $this->plus_disponibles = 2;
      $this->recarga_plus = 0;
    }

    // transbordo
    if($gestorDeTransbordo->sePuedeTransbordo($tarjeta,$colectivo)){
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

  public function information(){
    return (object)[
      "saldo" => $this->saldo,
      "plus_disponibles" => $this->plus_disponibles,
      "tipo" => $this->tipo,
      "id" => $this->id,
      "recarga_plus" => $this->recarga_plus,
      "ultimoColectivo" => $this->ultimoColectivo,
      "ultimoTransbordo" => $this->ultimoTrasbordo,
      "ultimoPago" => $this->ultimoPago,
      "ultimoCosto" => $this->ultimoCosto
    ];
  }

/**
 * Devuelve false si no se pudo restar un viaje plus disponible, sino lo resta y devuelve true
 *
 * @return bool
 */
  protected function restarPlus(){
    if($this->plus_disponibles > 0){
      $this->plus_disponibles -= 1;
    }else{
      return false;
    }
    return true;
  }
}