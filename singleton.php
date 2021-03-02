<?php

trait Singleton {
  private static self $INSTANCE;

  /**
  * Obtiene referencia a una única instancia de la clase que usa este trait.
  * @return static La instancia singleton de la clase que usa este trait.
  */
  static function getInstance(): self {
    return self::$INSTANCE ??= new static();
  }
}
