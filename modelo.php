<?php

require "singleton.php";

class gestionLibros {
  use Singleton;

  private mysqli $conexion;

  function conexion($servidor = null, $base_de_datos = null, $usuario = null, $contraseña = null) {
    if (isset($this->conexion)) return $this->conexion;

    try {
      $con = new mysqli($servidor, $usuario, $contraseña, $base_de_datos);
      $this->conexion = $con;
      return $con;
    } catch (exception $e) {
      return null;
    }
  }

  /**
  * Ejecuta un bloque de código a prueba de errores.
  * @param Closure $fn El bloque de código a ejecutar.
  * @return mixed El resultado de evaluar el código de $fn o null en caso de
  *   error.
  */
  private function withTry(Closure $fn) {
    try { return $fn(); } catch (exception $e) { return null; }
  }

  /**
  * Devuelve un array con los datos de autores.
  * @param int|null $autor La id del autor.
  * @return array|null Un array de autores si no ha habido errores.
  */
  function consultarAutores(?int $autor = null): ?array {
    return $this->withTry(function() use ($autor) {
      if ($autor !== null) {
        $sql = "SELECT * FROM Autor WHERE id = " . $autor;
        $rset = $this->conexion()->query($sql);
        return $rset->fetch_all(MYSQLI_ASSOC);
      } else {
        $sql = "SELECT * FROM Autor";
        $rset = $this->conexion()->query($sql);
        return $rset->fetch_all(MYSQLI_ASSOC);
      }
    });
  }

  /**
  * Devuelve un array con datos de libros.
  * @param int|null $autor La id del autor.
  * @return array|null Un array de libros si no ha habido errores.
  */
  function consultarLibros(?int $autor = null): ?array {
    return $this->withTry(function() use ($autor) {
      if ($autor !== null) {
        $sql = "SELECT * FROM Libro WHERE id_autor = " . $autor;
      } else {
        $sql = "SELECT * FROM Libro";
      }
      $rset = $this->conexion()->query($sql);
      return $rset->fetch_all(MYSQLI_ASSOC);
    });
  }

  /**
  * Devuelve un array con los datos de un libro.
  * @param int|null $autor La id del libro.
  * @return array|null Un array con los datos del libro si no ha habido errores.
  */
  function consultarDatosLibro(int $libro): ?array {
    return $this->withTry(function() use ($libro) {
      $sql = "SELECT * FROM Libro WHERE id = " . $libro;
      return $this->conexion()->query($sql)->fetch_all(MYSQLI_ASSOC)[0];
    });
  }

  /**
  * Ejecuta una sentencia sql a prueba de errores.
  * @param string $sql La sentencia sql.
  * @return bool false si ha habido error.
  */
  private function tryExec(string $sql): bool {
    return (bool) $this->withTry(function() use ($sql) {
      $this->conexion()->query($sql);
      return !$this->conexion()->error;
    });
  }

  /**
  * Borra a un autor y los libros que ha escrito.
  * @param int $autor La id del autor.
  * @return bool true si el borrado tuvo éxito.
  */
  function borrarAutor(int $autor): bool {
    $this->conexion()->autocommit(false);
    $this->conexion()->begin_transaction();
    $ok1 = $this->tryExec("DELETE FROM Autor WHERE id = " . $autor);
    $ok2 = $this->tryExec("DELETE FROM Libro WHERE id_autor = " . $autor);
    $ok1 && $ok2 ? $this->conexion()->commit() : $this->conexion()->rollback();
    $this->conexion()->autocommit(true);
    return $ok1 && $ok2;
  }

  /**
  * Borra un libro.
  * @param int $libro La id del libro.
  * @return bool true si el borrado tuvo éxito.
  */
  function borrarLibro(int $libro): bool {
    return $this->tryExec("DELETE FROM Libro WHERE id = " . $libro);
  }

  /**
  * Busca autores cuyo nombre y apellidos contienen un texto
  * @param string $str El texto a buscar
  * @return array El listado de autores con sus libros.
  */
  function consultarAutoresLibros(string $str): array {
    // Sacamos la informacion de todos los autores cuyo nombre o apellidos
    // contienen $str y le juntamos los datos de los libros de esos autores.
    $info = $this->conexion()->query("SELECT * FROM autor WHERE CONCAT(nombre, apellidos) REGEXP '$str'")->fetch_all(MYSQLI_ASSOC);
    foreach ($info as &$autor) {
      $id = $autor["id"];
      $sql = "SELECT * FROM libro WHERE id_autor='$id'";
      $autor["libros"] = $this->conexion()->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    return $info;
  }
}

/**
* Obtiene la referencia a una única instancia de gestionLibros e inicializa la
* conexión con la base de datos si no está ya inicializada.
* @return gestionLibros La instancia singleton de gestionLibros.
*/
function modelo(): gestionLibros {
  $modelo = gestionLibros::getInstance();
  $modelo->conexion("localhost", "jacob_test", "jacobubbl", "ox.3WQzPa2-caballo");
  return $modelo;
}

?>
