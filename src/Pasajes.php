<?php

namespace TrabajoTarjeta;


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

?>