<?php
/**
 * @brief Establece una conexión con la base de datos.
 * 
 * @returns mysqli: Retorna la conexión a la base de datos.
 * @verbatim
 * Ejemplo de uso:
 * $conn = Conectar();
 * @endverbatim
 */
function Conectar() {
    // Conexión en servidor local
    $server = "localhost";
    $user = "root";
    $password = "";
    $dbName = "mediciones";
    $mysql = new mysqli("localhost", "root", "", "mediciones");

    $conn = mysqli_connect($server, $user, $password, $dbName);

    // Comprobamos si la conexión falla
    if (!$conn) {
        http_response_code(500);
        die("Error: " . mysqli_connect_error());
    } else {
        // echo "conectado";
    }
    
    return $conn;
}
?>
