<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {

    protected $saldo;
    protected $pasaje;
    protected $transporte;
    protected $tiempo;

    public function nuevo(Float $saldo = null, $pasaje = null, CanceladoraInterface $transporte = null, TiempoInterface $tiempo = null){
        $this->saldo = $saldo;
        $this->pasaje = $pasaje;
        $this->transporte = $transporte;
        $this->tiempo = $tiempo;
    }
    
    public function saldo(){
        return $this->saldo;
    }

    public function pasaje(){
        return $this->pasaje;
    }

    public function transporte(){
        return $this->transporte;
    }

    public function tiempo(){
        return $this->tiempo;
    }
}
