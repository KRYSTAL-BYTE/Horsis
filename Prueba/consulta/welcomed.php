<?php
session_start(); // Inicia la sesión

// Configuración de la base de datos
$host = 'localhost';
$db = 'horsis';
$user = 'root';
$pass = '';

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

// Verifica si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Inicializa la variable de mensaje de error
$error_message = "";

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['plate'])) {
        $plateCode = htmlspecialchars($_POST['plate']); // Sanitiza el código de placa
        
        // Prepara la consulta SQL para verificar si existe el código de placa
        $stmt = $conn->prepare("SELECT CODIGO FROM placa WHERE CODIGO = ?");
        $stmt->bind_param("s", $plateCode); // Enlaza el parámetro

        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si se encontró la placa en la base de datos
        if ($result->num_rows > 0) {
            // Almacena el código de la placa en la sesión
            $_SESSION['plateCode'] = $plateCode;
            
            // Verifica que la sesión esté correctamente almacenada
            error_log("Plate Code Stored: " . $_SESSION['plateCode']); // Para depuración
            
            // Redirige a la página operacion.php
            header("Location: /prueba/consulta/operacion.html"); // Asegúrate de que la ruta sea correcta
            exit();
        } else {
            // Si la placa no se encuentra, guarda el valor en la sesión para mostrarlo en el formulario
            $_SESSION['plateInput'] = $plateCode;
            $error_message = "<p style='color:red; text-align:center;'>Placa no encontrada.</p>";
        }

        $stmt->close();
    } else {
        $error_message = "<p style='color:red; text-align:center;'>Por favor, ingrese un código de placa.</p>";
    }
}

// Cierra la conexión a la base de datos
$conn->close();

// Incluye el archivo HTML
include 'welcomed.html';
?>
