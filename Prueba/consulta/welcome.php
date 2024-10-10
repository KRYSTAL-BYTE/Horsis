<?php
session_start(); // Inicia la sesión si aún no se ha iniciado

// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu servidor es diferente
$db = 'horsis'; // Nombre de tu base de datos
$user = 'root'; // Usuario de la base de datos
$pass = ''; // Contraseña de la base de datos

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

// Verifica si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['plate'])) {
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
        
        // Si se encuentra, redirige a la página resultado.php con el código de la placa
        header("Location: /prueba/consulta/informe.php");
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>Placa no encontrada.</p>";
    }

    $stmt->close();
}

// Cierra la conexión a la base de datos
$conn->close();
?>

