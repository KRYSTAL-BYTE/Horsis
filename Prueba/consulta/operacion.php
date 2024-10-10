<?php
session_start(); // Inicia la sesión

// Verifica si la sesión tiene el código de placa
if (!isset($_SESSION['plateCode'])) {
    // Si no hay código de placa en la sesión, redirige al formulario de búsqueda
    header("Location: /prueba/consulta/welcomed.php");
    exit();
}

// Incluye el archivo HTML
include 'operacion.html';
?>
