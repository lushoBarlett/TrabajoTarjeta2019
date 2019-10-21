<?php

namespace TrabajoTarjeta;

interface TiempoInterface {

/**
 * Avanza el tiempo falso en una determinada cantidad de segundos.
 * 
 * @param int $segundos
 */
public function avanzar(Int $segundos);

/**
 * Devuelve el valor actual del tiempo falso.
 * 
 * @param string $formato Formato deseado, como en la funcion date()
 * 
 * @return string
 */
public function tiempo(String $formato);

}

?>