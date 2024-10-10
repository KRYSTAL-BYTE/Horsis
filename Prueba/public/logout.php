<?php
// Inicia la sesión si aún no se ha iniciado
session_start();

// Elimina todas las variables de sesión
$_SESSION = array();

// Si se utiliza una cookie de sesión, la eliminamos
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Destruye la sesión
session_destroy();
?>