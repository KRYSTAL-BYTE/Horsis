<?php
session_start(); // Iniciar la sesión

// Configura la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "horsis";

// Crea la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtiene el código de placa de la sesión
$codigo = isset($_SESSION['plateCode']) ? $_SESSION['plateCode'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Primero obtenemos el valor de la llave foránea `SERIE` de la tabla `placa` usando el `CODIGO`
    $sql_select_serie = "SELECT SERIE FROM placa WHERE CODIGO = ?";
    $stmt_select_serie = $conn->prepare($sql_select_serie);
    $stmt_select_serie->bind_param("s", $codigo);
    $stmt_select_serie->execute();
    $result_serie = $stmt_select_serie->get_result();

    if ($result_serie->num_rows > 0) {
        $placa_data = $result_serie->fetch_assoc();
        $serie = $placa_data['SERIE'];

        // Eliminar los datos de la tabla `placa` utilizando la columna `SERIE`
        $sql_delete_placa = "DELETE FROM placa WHERE SERIE = ?";
        $stmt_delete_placa = $conn->prepare($sql_delete_placa);
        $stmt_delete_placa->bind_param("i", $serie);
        $stmt_delete_placa->execute();

        // Eliminar los datos de la tabla `cliente` utilizando la columna `DOCUMENTO_ID`
        $sql_delete_cliente = "DELETE FROM cliente WHERE DOCUMENTO_ID = ?";
        $stmt_delete_cliente = $conn->prepare($sql_delete_cliente);
        $stmt_delete_cliente->bind_param("i", $serie);
        $stmt_delete_cliente->execute();

        // Cierre de sentencias
        $stmt_delete_placa->close();
        $stmt_delete_cliente->close();

        // Redirigir a gestion.php
        header("Location: /prueba/consulta/gestion.html");
        exit(); // Asegura que el script se detenga después de la redirección
    } else {
        echo "No se encontró la placa con el código proporcionado.";
    }

    $stmt_select_serie->close();
}

$conn->close();
?>
