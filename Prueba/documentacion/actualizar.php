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
    // Procesa la actualización de los datos
    $nuevo_color = $_POST['color'];
    $nuevo_tipo = $_POST['tipo'];
    $nuevo_modelo = $_POST['modelo'];
    $nueva_novedad = $_POST['novedad'];
    
    // Actualiza los datos de la placa
    $sql_update_placa = "UPDATE placa SET COLOR = ?, TIPO = ?, MODELO = ?, NOVEDAD = ? WHERE CODIGO = ?";
    $stmt_update_placa = $conn->prepare($sql_update_placa);
    $stmt_update_placa->bind_param("sssss", $nuevo_color, $nuevo_tipo, $nuevo_modelo, $nueva_novedad, $codigo);
    $stmt_update_placa->execute();

    // Procesa la actualización de los datos del cliente
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_apellido = $_POST['apellido'];
    $nueva_fecha_expe = $_POST['fecha_expe'];
    $nueva_fecha_naci = $_POST['fecha_naci'];
    $nuevo_lugar_expe = $_POST['lugar_expe'];
    $nuevo_tipo_sang = $_POST['tipo_sang'];
    
    // Actualiza los datos del cliente
    $sql_update_cliente = "UPDATE cliente SET NOMBRE = ?, APELLIDO = ?, FECHA_EXPE = ?, FECHA_NACI = ?, LUGAR_EXPE = ?, TIPO_SANG = ? WHERE DOCUMENTO_ID = ?";
    $stmt_update_cliente = $conn->prepare($sql_update_cliente);
    $stmt_update_cliente->bind_param("ssssssi", $nuevo_nombre, $nuevo_apellido, $nueva_fecha_expe, $nueva_fecha_naci, $nuevo_lugar_expe, $nuevo_tipo_sang, $serie);
    $stmt_update_cliente->execute();

    $stmt_update_placa->close();
    $stmt_update_cliente->close();
}

// Consulta para obtener datos de la placa
$sql_placa = "SELECT * FROM placa WHERE CODIGO = ?";
$stmt_placa = $conn->prepare($sql_placa);
$stmt_placa->bind_param("s", $codigo); // Usa 's' para STRING
$stmt_placa->execute();
$result_placa = $stmt_placa->get_result();

if ($result_placa->num_rows > 0) {
    // Obtiene los datos de la placa
    $placa_data = $result_placa->fetch_assoc();
    $serie = $placa_data['SERIE'];

    // Consulta para obtener datos del cliente usando la serie
    $sql_cliente = "SELECT * FROM cliente WHERE DOCUMENTO_ID = ?";
    $stmt_cliente = $conn->prepare($sql_cliente);
    $stmt_cliente->bind_param("i", $serie); // Usa 'i' para INTEGER
    $stmt_cliente->execute();
    $result_cliente = $stmt_cliente->get_result();

    // Muestra los datos del cliente
    if ($result_cliente->num_rows > 0) {
        $cliente_data = $result_cliente->fetch_assoc();
    } else {
        echo "<p>No se encontraron datos para el cliente con Documento ID: " . htmlspecialchars($serie) . "</p>";
    }

    // Muestra los datos de la placa y cliente en un formulario editable
    echo '<div class="container">';
    echo '<h1>Actualizar Datos del Cliente</h1>'; // Título de la página
    echo '<form action="" method="post">';
    echo '<h3>Datos del Cliente</h3>';
    echo '<label for="documento_id">Documento ID:</label>';
    echo '<input type="text" id="documento_id" name="documento_id" value="' . htmlspecialchars($cliente_data['DOCUMENTO_ID']) . '" readonly><br>'; // Campo no editable
    echo '<label for="nombre">Nombre:</label>';
    echo '<input type="text" id="nombre" name="nombre" value="' . htmlspecialchars($cliente_data['NOMBRE']) . '"><br>';
    echo '<label for="apellido">Apellido:</label>';
    echo '<input type="text" id="apellido" name="apellido" value="' . htmlspecialchars($cliente_data['APELLIDO']) . '"><br>';
    echo '<label for="fecha_expe">Fecha de Expedición:</label>';
    echo '<input type="date" id="fecha_expe" name="fecha_expe" value="' . htmlspecialchars($cliente_data['FECHA_EXPE']) . '"><br>';
    echo '<label for="fecha_naci">Fecha de Nacimiento:</label>';
    echo '<input type="date" id="fecha_naci" name="fecha_naci" value="' . htmlspecialchars($cliente_data['FECHA_NACI']) . '"><br>';
    echo '<label for="lugar_expe">Lugar de Expedición:</label>';
    echo '<input type="text" id="lugar_expe" name="lugar_expe" value="' . htmlspecialchars($cliente_data['LUGAR_EXPE']) . '"><br>';
    echo '<label for="tipo_sang">Tipo de Sangre:</label>';
    echo '<input type="text" id="tipo_sang" name="tipo_sang" value="' . htmlspecialchars($cliente_data['TIPO_SANG']) . '"><br>';
    
    echo '<h3>Datos de la Placa</h3>';
echo '<label for="codigo">Código de la Placa:</label>';
echo '<input type="text" id="codigo" name="codigo" value="' . htmlspecialchars($placa_data['CODIGO']) . '" readonly><br>';
echo '<label for="color">Color:</label>';
echo '<input type="text" id="color" name="color" value="' . htmlspecialchars($placa_data['COLOR']) . '"><br>';
echo '<label for="tipo">Tipo:</label>';
echo '<input type="text" id="tipo" name="tipo" value="' . htmlspecialchars($placa_data['TIPO']) . '"><br>';
echo '<label for="modelo">Modelo:</label>';
echo '<input type="text" id="modelo" name="modelo" value="' . htmlspecialchars($placa_data['MODELO']) . '"><br>';
echo '<label for="novedad">Novedad:</label>';
echo '<textarea id="novedad" name="novedad">' . htmlspecialchars($placa_data['NOVEDAD']) . '</textarea><br>';


    echo '<input type="submit" value="Actualizar">';
    echo '</form>';
    echo '</div>';
} else {
    echo "<p>No se encontraron datos para la placa con Código: " . htmlspecialchars($codigo) . "</p>";
}

$stmt_placa->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Datos del Cliente</title> <!-- Título de la pestaña del navegador -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            position: relative; /* Necesario para la posición absoluta de los botones */
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #333333; /* Color de fondo del contenedor */
            color: #fff; /* Color del texto */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #fff; /* Color del título */
            text-align: center; /* Centra el título */
        }
        h3 {
            color: #fff; /* Color de los subtítulos */
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #ddd; /* Color de las etiquetas */
        }
        input[type="text"], input[type="date"], textarea {
            width: calc(100% - 16px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #666; /* Color del borde de los campos */
            border-radius: 4px;
            background-color: #444; /* Fondo de los campos de texto */
            color: #fff; /* Color del texto en los campos de texto */
        }
        input[type="submit"] {
            width: calc(100% - 16px); /* Hace que el botón tenga el mismo ancho que los campos de texto */
            background-color: #007bff; /* Color de fondo del botón (azul) */
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            box-sizing: border-box; /* Asegura que el padding y border no aumenten el ancho total */
        }
        input[type="submit"]:hover {
            background-color: #0056b3; /* Color de fondo del botón al pasar el cursor (azul oscuro) */
        }
        .btn-container {
            position: absolute; /* Posiciona el contenedor de los botones de forma absoluta */
            bottom: 10px; /* Distancia desde la parte inferior */
            right: 10px; /* Distancia desde la parte derecha */
            display: flex;
            flex-direction: column; /* Coloca los botones en una columna */
            gap: 10px; /* Espacio entre los botones */
        }
        .btn-menu {
            background-color: #dc3545; /* Color rojo */
            color: white;
            text-align: center;
            padding: 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            box-sizing: border-box; /* Asegura que el padding y border no aumenten el ancho total */
            width: 150px; /* Ajusta el ancho de los botones */
            display: inline-block;
        }
        .btn-menu:hover {
            background-color: #c82333; /* Color rojo más oscuro al pasar el cursor */
        }
    </style>
</head>
<body>
    <!-- El contenido PHP generado dinámicamente será insertado aquí -->

    <div class="btn-container">
        <a href="/prueba/consulta/gestion.html" class="btn-menu">Volver al menú</a>
        <a href="/prueba/login/index.html" class="btn-menu">Salir</a>
    </div>
</body>
</html>
