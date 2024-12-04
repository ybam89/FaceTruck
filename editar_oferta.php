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

// Menús según el tipo de usuario
$menu = '';
if ($tipo_usuario == 'empresa') {
    $menu = '<ul>
                <li><a href="buscar_operadores.php">Buscar operadores</a></li>
                <li><a href="buscar_hombres_camion.php">Buscar Hombres camión</a></li>
                <li><a href="buscar_ofertas_rutas.php">Buscar ofertas de rutas</a></li>
                <li><a href="publicar_vacante.php">Publicar y consulta vacante operador</a></li>
                <li><a href="publicar_flete.php">Publicar Flete eventual</a></li>
                <li><a href="publicar_oferta_ruta.php">Publicar oferta de ruta</a></li>
             </ul>';
} else {
    echo "Error: Acceso no autorizado.";
    exit;
}

// Obtener el ID de la oferta de empleo desde la URL
$oferta_id = $_GET['id'];

// Obtener los datos de la oferta de empleo
$sql_oferta = "SELECT * FROM ofertas_empleo WHERE id = ? AND usuario_id = ?";
$stmt_oferta = $conn->prepare($sql_oferta);
$stmt_oferta->bind_param("ii", $oferta_id, $usuario_id);
$stmt_oferta->execute();
$result_oferta = $stmt_oferta->get_result();

if ($result_oferta->num_rows > 0) {
    $row_oferta = $result_oferta->fetch_assoc(); // Obtener los datos de la oferta de empleo
} else {
    echo "Error: Oferta de empleo no encontrada.";
    exit;
}

$stmt_oferta->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y validar los datos del formulario
    $vigente = $_POST['vigente'];
    $estado = $_POST['estado'];
    $municipio = $_POST['municipio'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    $sueldo = $_POST['sueldo'];
    $tipo_viaje = $_POST['tipo_viaje'];
    $descripcion_ruta = $_POST['descripcion_ruta'];
    $tipo_vehiculo_remolque = $_POST['tipo_vehiculo_remolque'];
    $requisitos = $_POST['requisitos'];
    $prestaciones = $_POST['prestaciones'];
    $contacto = $_POST['contacto'];

    // Actualizar los datos de la oferta de empleo
    $sql_update = "UPDATE ofertas_empleo SET vigente = ?, estado = ?, municipio = ?, fecha_publicacion = ?, sueldo = ?, tipo_viaje = ?, descripcion_ruta = ?, tipo_vehiculo_remolque = ?, requisitos = ?, prestaciones = ?, contacto = ? WHERE id = ? AND usuario_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssssssssii", $vigente, $estado, $municipio, $fecha_publicacion, $sueldo, $tipo_viaje, $descripcion_ruta, $tipo_vehiculo_remolque, $requisitos, $prestaciones, $contacto, $oferta_id, $usuario_id);

    // Ejecutar la actualización y verificar si se ha completado correctamente
    if ($stmt_update->execute()) {
        header("Location: publicar_vacante.php"); // Redirigir a perfil.php después de guardar los cambios
        exit;
    } else {
        echo "Error: " . $stmt_update->error;
    }

    $stmt_update->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta - FaceTruck</title>
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
        .dropdown-menu {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #007BFF;
            color: white;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            padding: 2px 2px;
            position: absolute;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 10px 40px;
            text-decoration: none;
            display: block;
            white-space: nowrap;
            text-align: left;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown-menu:hover .dropdown-content {
            display: block;
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
        .edit-button {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dropdown-menu">Menú
            <div class="dropdown-content">
                <?php echo $menu; ?>
            </div>
        </div>
        <a href="logout.php" class="logout-button">Cerrar sesión</a>
        <h2>Editar Oferta de Empleo</h2>
        <form method="post" class="form-container">
            <label for="vigente">Vigente:</label>
            <select id="vigente" name="vigente">
                <option value="1" <?php if ($row_oferta['vigente'] == 'Sí') echo 'selected'; ?>>Sí</option>
                <option value="0" <?php if ($row_oferta['vigente'] == 'No') echo 'selected'; ?>>No</option>
            </select>

            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" value="<?php echo $row_oferta['estado']; ?>" required>

            <label for="municipio">Municipio:</label>
            <input type="text" id="municipio" name="municipio" value="<?php echo $row_oferta['municipio']; ?>" required>

            <label for="fecha_publicacion">Fecha de publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" value="<?php echo $row_oferta['fecha_publicacion']; ?>" required>

            <label for="sueldo">Sueldo:</label>
            <input type="text" id="sueldo" name="sueldo" value="<?php echo $row_oferta['sueldo']; ?>" required>

            <label for="tipo_viaje">Tipo de viaje:</label>
            <select id="tipo_viaje" name="tipo_viaje">
                <option value="Foráneo" <?php if ($row_oferta['tipo_viaje'] == 'Foráneo') echo 'selected'; ?>>Foráneo</option>
                <option value="Local" <?php if ($row_oferta['tipo_viaje'] == 'Local') echo 'selected'; ?>>Local</option>
            </select>

            <label for="descripcion_ruta">Descripción de ruta:</label>
            <textarea id="descripcion_ruta" name="descripcion_ruta" rows="4" required><?php echo $row_oferta['descripcion_ruta']; ?></textarea>

            <label for="tipo_vehiculo_remolque">Tipo de vehículo y remolque (si aplica):</label>
            <input type="text" id="tipo_vehiculo_remolque" name="tipo_vehiculo_remolque" value="<?php echo $row_oferta['tipo_vehiculo_remolque']; ?>">

            <label for="requisitos">Requisitos:</label>
            <textarea id="requisitos" name="requisitos" rows="4" required><?php echo $row_oferta['requisitos']; ?></textarea>

            <label for="prestaciones">Prestaciones:</label>
            <textarea id="prestaciones" name="prestaciones" rows="4" required><?php echo $row_oferta['prestaciones']; ?></textarea>

            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto" value="<?php echo $row_oferta['contacto']; ?>" required>

            <button type="submit" class="button">Guardar cambios</button>
        </form>
        <div class="edit-button">
            <button onclick="location.href='publicar_vacante.php'" class="button">Regresar</button>
        </div>
    </div>
</body>
</html>