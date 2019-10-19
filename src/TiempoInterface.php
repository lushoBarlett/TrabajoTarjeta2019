<?php

interface TiempoInterface {

/**
 * Avanza el tiempo falso en una determinada cantidad de segundos.
 * 
 * @param Int $segundos
 */
public function avanzar(Int $segundos);

/**
 * Devuelve el valor actual del tiempo falso.
 * 
 * @param String $formato Formato deseado, como en la funcion date()
 * 
 * @return tiempo
 */
public function tiempo(String $formato);

}

?>