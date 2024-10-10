<?php

// Incluir el archivo de conexión
include_once 'conexion.php'; // Asegúrate de que el archivo se llama conexion.php

// Configurar los encabezados para la API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conectar a la base de datos
$conn = Conectar();

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Manejar las peticiones según el método
switch($method) {
    case 'GET':
        if (isset($_GET['idMedicion'])) {
            getMedicionById($conn, $_GET['idMedicion']);
        } else {
            getAllMediciones($conn);
        }
        break;

    case 'POST':
        addMedicion($conn);
        break;

    case 'PUT':
        updateMedicion($conn);
        break;

    case 'DELETE':
        deleteMedicion($conn);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}

// --------------------------------------------------------------
// @brief Obtiene todas las mediciones de la base de datos.
// @param conn Mysqli: Conexión a la base de datos.
// --------------------------------------------------------------
function getAllMediciones($conn) {
    $sql = "SELECT * FROM mediciones";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        $mediciones = [];
        while($row = mysqli_fetch_assoc($result)){
            $mediciones[] = $row;
        }
        echo json_encode($mediciones);
    } else {
        echo json_encode(["message" => "No se encontraron mediciones"]);
    }
}

// --------------------------------------------------------------
// @brief Obtiene una medición de la base de datos por ID.
// @param conn Mysqli: Conexión a la base de datos.
// @param id int: ID de la medición a obtener.
// --------------------------------------------------------------
function getMedicionById($conn, $id) {
    $sql = "SELECT * FROM mediciones WHERE idMedicion = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($result)){
        echo json_encode($row);
    } else {
        echo json_encode(["message" => "Medición no encontrada"]);
    }
}

// --------------------------------------------------------------
// @brief Agrega una nueva medición a la base de datos.
// @param conn Mysqli: Conexión a la base de datos.
// --------------------------------------------------------------
function addMedicion($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    $sql = "INSERT INTO mediciones (fecha, lugar, valor, tipodegas) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssis", $data['fecha'], $data['lugar'], $data['valor'], $data['tipodegas']);

    if (mysqli_stmt_execute($stmt)) {
        http_response_code(201);
        echo json_encode(["message" => "Medición agregada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error al agregar la medición"]);
    }
}

// // --------------------------------------------------------------
// @brief Actualiza una medición existente en la base de datos.
// @param conn Mysqli: Conexión a la base de datos.
// --------------------------------------------------------------
function updateMedicion($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "UPDATE mediciones SET fecha = ?, lugar = ?, valor = ?, tipodegas = ? WHERE idMedicion = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssisi", $data['fecha'], $data['lugar'], $data['valor'], $data['tipodegas'], $data['idMedicion']);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["message" => "Medición actualizada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error al actualizar la medición"]);
    }
}

// @brief Eliminar una medición
/**
 * @brief Elimina una medición de la base de datos.
 * 
 * @param $conn mysqli: La conexión a la base de datos.
 * @return void
 * @verbatim
 * Ejemplo de uso:
 * deleteMedicion($conn);
 * @endverbatim
 */
function deleteMedicion($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "DELETE FROM mediciones WHERE idMedicion = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $data['idMedicion']);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["message" => "Medición eliminada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error al eliminar la medición"]);
    }
}

?>
