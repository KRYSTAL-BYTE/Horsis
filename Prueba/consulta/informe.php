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

// Obtiene el valor del código de placa desde la sesión
if (isset($_SESSION['plateCode'])) {
    $plateCode = $_SESSION['plateCode'];
    
    // Prepara la consulta SQL para obtener datos de la tabla 'placa' y 'cliente'
    $sql = "
        SELECT p.CODIGO, p.COLOR, p.TIPO, p.MODELO, p.NOVEDAD, p.SERIE, c.NOMBRE, c.APELLIDO, c.FECHA_EXPE, c.FECHA_NACI, c.LUGAR_EXPE, c.TIPO_SANG
        FROM placa p
        JOIN cliente c ON p.SERIE = c.DOCUMENTO_ID
        WHERE p.CODIGO = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $plateCode);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si hay resultados
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $error_message = "No se encontró información para este código de placa.";
        $row = null;
    }

    $stmt->close();
} else {
    $error_message = "No se ha proporcionado un código de placa.";
    $row = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe General</title>
    <link rel="stylesheet" href="informe.css">
</head>
<body>
    <div class="container">
        <h2>Informe General</h2>
        <?php if ($row): ?>
        <table>
            <tr>
                <th>Código</th>
                <td><?php echo htmlspecialchars($row['CODIGO']); ?></td>
            </tr>
            <tr>
                <th>Color</th>
                <td><?php echo htmlspecialchars($row['COLOR']); ?></td>
            </tr>
            <tr>
                <th>Tipo</th>
                <td><?php echo htmlspecialchars($row['TIPO']); ?></td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td><?php echo htmlspecialchars($row['MODELO']); ?></td>
            </tr>
            <tr>
                <th>Novedad</th>
                <td><?php echo htmlspecialchars($row['NOVEDAD']); ?></td>
            </tr>
            <tr>
                <th>Serie</th>
                <td><?php echo htmlspecialchars($row['SERIE']); ?></td>
            </tr>
            <tr>
                <th>Nombre del Cliente</th>
                <td><?php echo htmlspecialchars($row['NOMBRE']); ?></td>
            </tr>
            <tr>
                <th>Apellido del Cliente</th>
                <td><?php echo htmlspecialchars($row['APELLIDO']); ?></td>
            </tr>
            <tr>
                <th>Fecha de Expedición</th>
                <td><?php echo htmlspecialchars($row['FECHA_EXPE']); ?></td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento</th>
                <td><?php echo htmlspecialchars($row['FECHA_NACI']); ?></td>
            </tr>
            <tr>
                <th>Lugar de Expedición</th>
                <td><?php echo htmlspecialchars($row['LUGAR_EXPE']); ?></td>
            </tr>
            <tr>
                <th>Tipo de Sangre</th>
                <td><?php echo htmlspecialchars($row['TIPO_SANG']); ?></td>
            </tr>
        </table>
        <?php else: ?>
        <p><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="button-container">
            <a href="/prueba/consulta/welcome.html" class="button">Volver</a>
            <a href="/prueba/login/index.html" class="button-logout">Salir</a>
        </div>
    </div>
</body>
</html>
