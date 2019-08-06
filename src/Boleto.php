<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {

  /**
   *
   *Variables contenidas en el boleto
   *
   */

    protected $valor;
    protected $colectivo;
    protected $fecha;
    protected $tipoTarj;
    protected $abonado;
    protected $saldo;
    protected $idTarj;
    protected $tipo;
    protected $linea;

    public function __construct($valor, $colectivo, $tarjeta, $tipo) {
        $this->valor = $valor;
        $this->colectivo = $colectivo;
        $this->fecha = date("d/m/Y H:i:s"); //reemplazar por la clase tiempo
        $this->tipoTarj = $tarjeta->obtenerTipo();
        $this->saldo = $tarjeta->obtenerSaldo();
        $this->idTarj = $tarjeta->obtenerID();
        $this->tipo = $tipo;
        $this->linea = $colectivo->linea();
        $this->abonado = $tarjeta->abonado();
    }

    /**
     * Devuelve el valor del boleto.
     *
     * @return int
     */

    public function obtenerValor() {
        return $this->valor;
    }

    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     *
     * @return ColectivoInterface
     */

    public function obtenerColectivo() {
        return $this->colectivo;
    }

    /**
     * Devuelve la fecha en el momento en que se crea el boleto.
     *
     * @return fecha
     */

    public function obtenerFecha() {
        return $this->fecha;
    }

    /**
     * Devuelve el tipo de la tarjeta (medio, universitario, completo).
     *
     * @return string
     */

    public function obtenerTipoTarj() {
        return $this->tipoTarj;
    }

    /**
     * Devuelve el tipo de boleto (medio, plus, normal)
     *
     * @return string
     */

    public function obtenerTipo() {
        return $this->tipo;
    }

    /**
     * Devuelve un numero que representa la linea de colectivo.
     *
     * @return int
     */

    public function obtenerLinea() {
        return $this->linea;
    }

    /**
     * Devuelve el total abonado.
     *
     * @return float
     */

    public function obtenerAbonado() {
        return $this->abonado;
    }

    /**
     * Devuelve el saldo de la tarjeta.
     *
     * @return float
     */

    public function obtenerSaldo() {
        return $this->saldo;
    }

    /**
     * Devuelve el ID de la tarjeta con la que se pago el boleto.
     *
     * @return int
     */

    public function obtenerIdTarj() {
        return $this->idTarj;
    }

}
