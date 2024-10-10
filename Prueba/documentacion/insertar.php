<?php
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

// Verifica si los datos están definidos en $_POST
$codigo = isset($_POST['CODIGO']) ? $_POST['CODIGO'] : null;
$color = isset($_POST['COLOR']) ? $_POST['COLOR'] : null;
$tipo = isset($_POST['TIPO']) ? $_POST['TIPO'] : null;
$modelo = isset($_POST['MODELO']) ? $_POST['MODELO'] : null;
$novedad = isset($_POST['NOVEDAD']) ? $_POST['NOVEDAD'] : null;
$documento = isset($_POST['DOCUMENTO_ID']) ? $_POST['DOCUMENTO_ID'] : null;
$nombre = isset($_POST['NOMBRE']) ? $_POST['NOMBRE'] : null;
$apellido = isset($_POST['APELLIDO']) ? $_POST['APELLIDO'] : null;
$expedicion = isset($_POST['FECHA_EXPE']) ? $_POST['FECHA_EXPE'] : null;
$nacimiento = isset($_POST['FECHA_NACI']) ? $_POST['FECHA_NACI'] : null;
$lugar = isset($_POST['LUGAR_EXPE']) ? $_POST['LUGAR_EXPE'] : null;
$sanguineo = isset($_POST['TIPO_SANG']) ? $_POST['TIPO_SANG'] : null;

// Verifica que todos los datos requeridos estén presentes
if ($codigo && $color && $tipo && $modelo && $novedad && $documento && $nombre && $apellido && $expedicion && $nacimiento && $lugar && $sanguineo) {
    // Verifica si el DOCUMENTO_ID ya existe en la tabla cliente
    $sql_check = "SELECT DOCUMENTO_ID FROM cliente WHERE DOCUMENTO_ID = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $documento);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "Ya existe un registro de este número de cédula, por favor consultar.";
    } else {
        // Inserta en la tabla 'cliente'
        $sql_cliente = "INSERT INTO cliente (DOCUMENTO_ID, NOMBRE, APELLIDO, FECHA_EXPE, FECHA_NACI, LUGAR_EXPE, TIPO_SANG) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_cliente = $conn->prepare($sql_cliente);
        $stmt_cliente->bind_param("sssssss", $documento, $nombre, $apellido, $expedicion, $nacimiento, $lugar, $sanguineo);

        if ($stmt_cliente->execute()) {
            echo "Registro creado exitosamente.<br>";
        } else {
            echo "Error al insertar en cliente: " . $stmt_cliente->error . "<br>";
        }

        // Inserta en la tabla 'placa'
        $sql_placa = "INSERT INTO placa (CODIGO, COLOR, TIPO, MODELO, NOVEDAD, SERIE) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_placa = $conn->prepare($sql_placa);
        $stmt_placa->bind_param("sssssi", $codigo, $color, $tipo, $modelo, $novedad, $documento);

        if ($stmt_placa->execute()) {
            echo "";
        } else {
            echo "Error al insertar en placa: " . $stmt_placa->error;
        }
    }

    // Cierra las conexiones
    $stmt_check->close();
    $stmt_cliente->close();
    $stmt_placa->close();
} else {
    echo "";
}

$conn->close();
?>
