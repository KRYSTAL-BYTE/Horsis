<?php
// Inicia sesión (si es necesario)
session_start();

// Verifica si el usuario ha iniciado sesión (opcional)
if (!isset($_SESSION['username'])) {
    header("Location: /prueba/login/index.php"); // Redirige al inicio de sesión si no ha iniciado sesión
    exit();
}

// Aquí puedes agregar más lógica si es necesario (como cargar datos del usuario)

// Incluye el archivo HTML para mostrar la página
include 'gestion.html';
?>
