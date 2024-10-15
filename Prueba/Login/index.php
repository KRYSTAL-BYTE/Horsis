<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu servidor es diferente
$db = 'horsis'; // Nombre de tu base de datos
$user = 'root'; // Usuario de la base de datos
$pass = ''; // Contraseña de la base de datos

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

// Verificación si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los valores del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepara la consulta para prevenir inyecciones SQL
    $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    // Verifica si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verifica la contraseña (comparación directa)
        if ($password === $stored_password) {
            // Redirige a la página de gestión si las credenciales son correctas
            header("Location: /prueba/consulta/gestion.html");
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }

    // Cierra la declaración
    $stmt->close();
}

// Cierra la conexión a la base de datos
$conn->close();
?>
