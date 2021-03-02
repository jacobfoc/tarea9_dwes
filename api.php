<?php

require "modelo.php";

/**
* Busca la lista de todos los autores.
* @return array Un array de datos de autores.
*/
function get_lista_autores() {
  return modelo()->consultarAutores();
}

/**
* Devuelve informacion de un autor incluyendo sus libros.
* @param int $id La id del autor.
* @return array Un array de datos del autor y de sus libros.
*/
function get_datos_autor($id) {
  $datos_autor = modelo()->consultarAutores($id)[0];
  $datos_autor["libros"] = modelo()->consultarLibros($id);

  return $datos_autor;
}

/**
* Busca la lista de todos los libros.
* @return array Un array de datos de libros.
*/
function get_lista_libros() {
  return array_map(
    function($libro) {
      return ["id" => $libro["id"], "titulo" => $libro["titulo"]];
    },
    modelo()->consultarLibros()
  );
}

/**
* Devuelve informacion de un libro incluyendo el nombre y apellidos de su autor.
* @param int $id La id del libro.
* @return array Un array de datos del libro y el nombre y apellidos de su autor.
*/
function get_datos_libro($id) {
  $libro = modelo()->consultarDatosLibro($id);
  $datos_autor = modelo()->consultarAutores($libro["id_autor"])[0];
  unset($libro["id"]);
  $libro["nombre"] = $datos_autor["nombre"];
  $libro["apellidos"] = $datos_autor["apellidos"];

  return $libro;
}

/**
* Devuelve informacion de todos los autores y de los libros de cada uno de ellos.
* @return array Un array de datos de autores y de sus libros.
*/
function get_datos_todo($str) {
  // $autores = get_lista_autores();
  // $libros = modelo()->consultarLibros();
  // foreach ($autores as &$autor) {
  //   $autor["libros"] = array_values(array_filter($libros, function($libro) use ($autor) {
  //     return $libro["id_autor"] == $autor["id"];
  //   }));
  // }
  // return $autores;
    try {
    return modelo()->consultarAutoresLibros($str);
    } catch (Exception $e) { return $e->getMessage(); }
}

$posibles_URL = [
  "get_lista_autores", "get_datos_autor", "get_lista_libros", "get_datos_libro",
  "proxy", "get_lista_todo"
];

$valor = "Ha ocurrido un error";


if (isset($_GET["action"]) && in_array($_GET["action"], $posibles_URL)) {
  switch ($_GET["action"]) {
    case "get_lista_autores":
      $valor = get_lista_autores();
      break;
    case "get_datos_autor":
      if (isset($_GET["id"])) {
        $valor = get_datos_autor($_GET["id"]);
      } else {
        $valor = "Argumento no encontrado";
      }
      break;
    case "get_lista_libros":
      $valor = get_lista_libros();
      break;
    case "get_datos_libro":
      if (isset($_GET["id"])) {
        $valor = get_datos_libro($_GET["id"]);
      } else {
        $valor = "Argumento no encontrado";
      }
      break;
    case "get_lista_todo":
      // $valor = get_datos_todo();
        if (isset($_GET["str"])) {
        $valor = get_datos_todo($_GET["str"]);
      } else {
        $valor = "Argumento no encontrado";
      }
      break;
    case "proxy":
      if (isset($_GET["url"])) {
        exit(file_get_contents($_GET["url"]));
      } else {
        $valor = "Argumento no encontrado";
      }
      break;
  }
}


//devolvemos los datos serializados en JSON
exit(json_encode($valor));

?>
