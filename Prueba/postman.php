<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "horsis"; // Usa tu nombre de base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtén el método HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Leer datos
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM placa WHERE CODIGO = '$id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            $sql = "SELECT * FROM placa";
            $result = $conn->query($sql);
            $rows = array();
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        }
        break;
        
    case 'POST':
        // Crear un nuevo registro
        $data = json_decode(file_get_contents('php://input'), true);
        $codigo = $data['CODIGO'];
        $color = $data['COLOR'];
        $tipo = $data['TIPO'];
        $modelo = $data['MODELO'];
        $novedad = $data['NOVEDAD'];
        

        $sql = "INSERT INTO placa (CODIGO, COLOR, TIPO, MODELO, NOVEDAD) VALUES ('$codigo', '$color', '$tipo', '$modelo', '$novedad')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Registro creado exitosamente"]);
        } else {
            echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        }
        break;
        
        case 'PUT':
            // Obtener los datos enviados en el cuerpo de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
        
            // Validar que los campos esperados están presentes
            if (isset($data['CODIGO'], $data['COLOR'], $data['TIPO'], $data['MODELO'], $data['NOVEDAD'])) {
                // Limpiar y asignar los datos recibidos
                $codigo = $conn->real_escape_string($data['CODIGO']);
                $color = $conn->real_escape_string($data['COLOR']);
                $tipo = $conn->real_escape_string($data['TIPO']);
                $modelo = $conn->real_escape_string($data['MODELO']);
                $novedad = $conn->real_escape_string($data['NOVEDAD']);
                
                // Preparar la consulta de actualización
                $sql = "UPDATE placa SET COLOR = '$color', TIPO = '$tipo', MODELO = '$modelo', NOVEDAD = '$novedad' WHERE CODIGO = '$codigo'";
        
                // Ejecutar la consulta
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(["message" => "Registro actualizado exitosamente"]);
                } else {
                    echo json_encode(["error" => "Error al actualizar: " . $conn->error]);
                }
            } else {
                // Error si faltan campos en la solicitud
                echo json_encode(["error" => "Datos incompletos para actualizar el registro"]);
            }
            break;
        
        
        case 'DELETE':
            // Verifica si se recibió el parámetro 'id'
            if (isset($_GET['id'])) {
                $id = $conn->real_escape_string($_GET['id']); // Asegura que el valor de 'id' es seguro
                $sql = "DELETE FROM placa WHERE CODIGO = '$id'"; 
                
                if ($conn->query($sql) === TRUE) {
                    // Verifica si se eliminó algún registro
                    if ($conn->affected_rows > 0) {
                        echo json_encode(["message" => "Registro eliminado exitosamente"]);
                    } else {
                        echo json_encode(["message" => "No se encontró ningún registro con ese ID"]);
                    }
                } else {
                    echo json_encode(["error" => "Error al eliminar el registro: " . $conn->error]);
                }
            } else {
                echo json_encode(["error" => "Parámetro 'id' no proporcionado"]);
            }
            break;
        }

// Cierra la conexión
$conn->close();
?>
