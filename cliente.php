<html>
 <body>

<?php
// Si se ha hecho una peticion que busca informacion de un autor "get_datos_autor" a traves de su "id"...
if (isset($_GET["action"]) && isset($_GET["id"]) && $_GET["action"] == "get_datos_autor") {
    //Se realiza la peticion a la api que nos devuelve el JSON con la información de los autores
    $app_info = file_get_contents('https://w3.abanico.net/jacob/tarea7/api.php?action=get_datos_autor&id=' . $_GET["id"]);
    // Se decodifica el fichero JSON y se convierte a array
    $app_info = json_decode($app_info, true);
?>
    <table>
        <tr>
          <td>Nombre: </td><td> <?php echo $app_info["nombre"] ?></td>
        </tr>
        <tr>
          <td>Apellidos: </td><td> <?php echo $app_info["apellidos"] ?></td>
        </tr>
        <tr>
          <td>Fecha de nacimiento: </td><td> <?php echo $app_info["nacionalidad"] ?></td>
        </tr>
        <tr>
          <td>libros: </td>
            <td>
            <?php foreach($app_info["libros"] as $idx => $libro): ?>
              <a href="<?php echo "https://w3.abanico.net/jacob/tarea7/cliente.php?action=get_datos_libro&id=" . $libro["id"]  ?>">
              <?php if ($idx > 0) echo ", " ?>
              <?php echo $libro["titulo"] ?>
              </a>
            <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <br />
    <!-- Enlace para volver a la lista de autores -->
    <a href="https://w3.abanico.net/jacob/tarea7/cliente.php?action=get_lista_autores" alt="Lista de autores">Volver a la lista de autores</a>
<?php
} else if (@$_GET["action"] == "get_lista_libros") {
    $lista_libros = file_get_contents('https://w3.abanico.net/jacob/tarea7/api.php?action=get_lista_libros');
    $lista_libros = json_decode($lista_libros, true);
    ?>
    <ul>
    <!-- Mostramos una entrada por cada libro -->
    <?php foreach($lista_libros as $libro): ?>
      <li>
        <!-- Enlazamos cada nombre de libro con su informacion -->
        <a href="<?php echo "https://w3.abanico.net/jacob/tarea7/cliente.php?action=get_datos_libro&id=" . $libro["id"]  ?>">
          <?php echo $libro["titulo"] ?>
        </a>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php
} else if (@$_GET["action"] == "get_datos_libro" && isset($_GET["id"])) {
    $datos = file_get_contents('https://w3.abanico.net/jacob/tarea7/api.php?action=get_datos_libro&id=' . $_GET["id"]);
    $datos = json_decode($datos, true);
    echo "<ul>";
    echo "<li>Título: ", $datos["titulo"], "</li>";
    echo "<li>Fecha de publicación: ", $datos["f_publicacion"], "</li>";
    echo "<li>Autor: ", "<a href=https://w3.abanico.net/jacob/tarea7/cliente.php?action=get_datos_autor&id=", $datos["id_autor"], ">", $datos["nombre"], " ", $datos["apellidos"], "</li>";
    echo "</ul>";
    // Enlace para volver a la lista de libros
    echo '<a href="https://w3.abanico.net/jacob/tarea7/cliente.php?action=get_lista_libros" alt="Lista de libros">Volver a la lista de libros</a>';
} else { //sino muestra la lista de autores
  // Pedimos al la api que nos devuelva una lista de autores. La respuesta se da en formato JSON
  $lista_autores = file_get_contents('https://w3.abanico.net/jacob/tarea7/api.php?action=get_lista_autores');
  // Convertimos el fichero JSON en array
	// var_dump($lista_autores);
  $lista_autores = json_decode($lista_autores, true);
?>
    <ul>
    <!-- Mostramos una entrada por cada autor -->
    <?php foreach($lista_autores as $autores): ?>
        <li>
            <!-- Enlazamos cada nombre de autor con su informacion (primer if) -->
            <a href="<?php echo "https://w3.abanico.net/jacob/tarea7/cliente.php?action=get_datos_autor&id=" . $autores["id"]  ?>">
            <?php echo $autores["nombre"] . " " . $autores["apellidos"] ?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
  <?php
} ?>
 </body>
</html>
