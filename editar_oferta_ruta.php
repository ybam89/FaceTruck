<?php
session_start(); // Inicia la sesión

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Termina la ejecución si hay un error en la conexión
}

// Obtener el ID del usuario y el tipo de usuario desde la sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}
$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Verificar si se ha recibido el ID de la oferta de ruta a editar
if (!isset($_GET['id'])) {
    echo "Error: No se ha recibido el ID de la oferta de ruta.";
    exit;
}
$id = $_GET['id'];

// Obtener los datos actuales de la oferta de ruta
$sql = "SELECT * FROM ofertas_empresas WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Error: No se encontró la oferta de ruta.";
    exit;
}

$oferta = $result->fetch_assoc();

// Procesar el formulario de edición de oferta de ruta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editar') {
    // Recibir y validar los datos del formulario
    $vigente = $_POST['vigente'] == 'Sí' ? 1 : 0;
    $estado = $_POST['estado'];
    $municipio = $_POST['municipio'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    $pago_ofrecido = $_POST['pago_ofrecido'];
    $tipo_viaje = $_POST['tipo_viaje'];
    $descripcion_ruta = $_POST['descripcion_ruta'];
    $tipo_vehiculo_remolque = $_POST['tipo_vehiculo_remolque'];
    $requisitos = $_POST['requisitos'];
    $contacto = $_POST['contacto'];

    // Actualizar los datos en la tabla ofertas_empresas
    $sql = "UPDATE ofertas_empresas SET vigente = ?, estado = ?, municipio = ?, fecha_publicacion = ?, pago_ofrecido = ?, tipo_viaje = ?, descripcion_ruta = ?, tipo_vehiculo_remolque = ?, requisitos = ?, contacto = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssiii", $vigente, $estado, $municipio, $fecha_publicacion, $pago_ofrecido, $tipo_viaje, $descripcion_ruta, $tipo_vehiculo_remolque, $requisitos, $contacto, $id, $usuario_id);

    if ($stmt->execute()) {
        header("Location: publicar_oferta_ruta.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta de Ruta - FaceTruck</title>
    <style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .logout-button {
            background-color: #FF0000;
            color: white;
            padding: 3px 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            position: absolute;
            top: 20px;
            right: 20px;
            text-decoration: none;
        }
        .logout-button:hover {
            background-color: #cc0000;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-button">Cerrar sesión</a>
        <h2>Editar Oferta de Ruta</h2>
        <form method="post" class="form-container">
            <input type="hidden" name="action" value="editar">
            <label for="vigente">Vigente:</label>
            <select id="vigente" name="vigente">
                <option value="Sí" <?php echo $oferta['vigente'] == 1 ? 'selected' : ''; ?>>Sí</option>
                <option value="No" <?php echo $oferta['vigente'] == 0 ? 'selected' : ''; ?>>No</option>
            </select>

            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($oferta['estado']); ?>" required>

            <label for="municipio">Municipio:</label>
            <input type="text" id="municipio" name="municipio" value="<?php echo htmlspecialchars($oferta['municipio']); ?>" required>

            <label for="fecha_publicacion">Fecha de publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" value="<?php echo $oferta['fecha_publicacion']; ?>" required>

            <label for="pago_ofrecido">Pago ofrecido:</label>
            <input type="text" id="pago_ofrecido" name="pago_ofrecido" value="<?php echo htmlspecialchars($oferta['pago_ofrecido']); ?>" required>

            <label for="tipo_viaje">Tipo de viaje:</label>
            <select id="tipo_viaje" name="tipo_viaje">
                <option value="Foráneo" <?php echo $oferta['tipo_viaje'] == 'Foráneo' ? 'selected' : ''; ?>>Foráneo</option>
                <option value="Local" <?php echo $oferta['tipo_viaje'] == 'Local' ? 'selected' : ''; ?>>Local</option>
            </select>

            <label for="descripcion_ruta">Descripción de ruta:</label>
            <textarea id="descripcion_ruta" name="descripcion_ruta" rows="4" required><?php echo htmlspecialchars($oferta['descripcion_ruta']); ?></textarea>

            <label for="tipo_vehiculo_remolque">Tipo de vehículo y remolque (si aplica):</label>
            <input type="text" id="tipo_vehiculo_remolque" name="tipo_vehiculo_remolque" value="<?php echo htmlspecialchars($oferta['tipo_vehiculo_remolque']); ?>">

            <label for="requisitos">Requisitos:</label>
            <textarea id="requisitos" name="requisitos" rows="4" required><?php echo htmlspecialchars($oferta['requisitos']); ?></textarea>

            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto" value="<?php echo htmlspecialchars($oferta['contacto']); ?>" required>

            <button type="submit" class="button">Guardar cambios</button>
        </form>
        <div class="edit-button">
            <button onclick="location.href='publicar_oferta_ruta.php'" class="button">Regresar</button>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$stmt->close();
$conn->close();
?>