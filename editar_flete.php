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
                <li><a href="publicar_vacante.php">Publicar y consultar mis vacantes "operador"</a></li>
                <li><a href="publicar_flete.php">Publicar y consultar mis Fletes eventuales</a></li>
                <li><a href="publicar_oferta_ruta.php">Publicar y consultar oferta de ruta</a></li>
             </ul>';
} else {
    echo "Error: Acceso no autorizado.";
    exit;
}

// Obtener el ID del flete desde la URL
$flete_id = $_GET['id'];

// Obtener los datos del flete
$sql_flete = "SELECT * FROM buscar_fletes WHERE id = ? AND usuario_id = ?";
$stmt_flete = $conn->prepare($sql_flete);
$stmt_flete->bind_param("ii", $flete_id, $usuario_id);
$stmt_flete->execute();
$result_flete = $stmt_flete->get_result();

if ($result_flete->num_rows > 0) {
    $row_flete = $result_flete->fetch_assoc(); // Obtener los datos del flete
} else {
    echo "Error: Flete no encontrado.";
    exit;
}

$stmt_flete->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y validar los datos del formulario
    $vigencia = $_POST['vigencia'];
    $estado_partida = $_POST['estado_partida'];
    $municipio_partida = $_POST['municipio_partida'];
    $estado_destino = $_POST['estado_destino'];
    $municipio_destino = $_POST['municipio_destino'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    $tipo_viaje = $_POST['tipo_viaje'];
    $kilometraje_aproximado = $_POST['kilometraje_aproximado'];
    $tipo_vehiculo_solicitado = $_POST['tipo_vehiculo_solicitado'];
    $descripcion = $_POST['descripcion'];
    $pago_ofrecido = $_POST['pago_ofrecido'];

    // Actualizar los datos del flete
    $sql_update = "UPDATE buscar_fletes SET vigencia = ?, estado_partida = ?, municipio_partida = ?, estado_destino = ?, municipio_destino = ?, fecha_publicacion = ?, tipo_viaje = ?, kilometraje_aproximado = ?, tipo_vehiculo_solicitado = ?, descripcion = ?, pago_ofrecido = ? WHERE id = ? AND usuario_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssssssssii", $vigencia, $estado_partida, $municipio_partida, $estado_destino, $municipio_destino, $fecha_publicacion, $tipo_viaje, $kilometraje_aproximado, $tipo_vehiculo_solicitado, $descripcion, $pago_ofrecido, $flete_id, $usuario_id);

    // Ejecutar la actualización y verificar si se ha completado correctamente
    if ($stmt_update->execute()) {
        header("Location: publicar_flete.php"); // Redirigir a publicar_flete.php después de guardar los cambios
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
    <title>Editar Flete - FaceTruck</title>
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
        <h2>Editar Flete</h2>
        <form method="post" class="form-container">
            <label for="vigencia">Vigente:</label>
            <select id="vigencia" name="vigencia">
                <option value="1" <?php if ($row_flete['vigencia'] == '1') echo 'selected'; ?>>Sí</option>
                <option value="0" <?php if ($row_flete['vigencia'] == '0') echo 'selected'; ?>>No</option>
            </select>

            <label for="estado_partida">Estado de Partida:</label>
            <input type="text" id="estado_partida" name="estado_partida" value="<?php echo $row_flete['estado_partida']; ?>" required>

            <label for="municipio_partida">Municipio de Partida:</label>
            <input type="text" id="municipio_partida" name="municipio_partida" value="<?php echo $row_flete['municipio_partida']; ?>" required>

            <label for="estado_destino">Estado de Destino:</label>
            <input type="text" id="estado_destino" name="estado_destino" value="<?php echo $row_flete['estado_destino']; ?>" required>

            <label for="municipio_destino">Municipio de Destino:</label>
            <input type="text" id="municipio_destino" name="municipio_destino" value="<?php echo $row_flete['municipio_destino']; ?>" required>

            <label for="fecha_publicacion">Fecha de Publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" value="<?php echo $row_flete['fecha_publicacion']; ?>" required>

            <label for="tipo_viaje">Tipo de Viaje:</label>
            <select id="tipo_viaje" name="tipo_viaje">
                <option value="Local" <?php if ($row_flete['tipo_viaje'] == 'Local') echo 'selected'; ?>>Local</option>
                <option value="Foráneo" <?php if ($row_flete['tipo_viaje'] == 'Foráneo') echo 'selected'; ?>>Foráneo</option>
            </select>

            <label for="kilometraje_aproximado">Kilometraje Aproximado:</label>
            <input type="text" id="kilometraje_aproximado" name="kilometraje_aproximado" value="<?php echo $row_flete['kilometraje_aproximado']; ?>" required>

            <label for="tipo_vehiculo_solicitado">Tipo de Vehículo Solicitado:</label>
            <input type="text" id="tipo_vehiculo_solicitado" name="tipo_vehiculo_solicitado" value="<?php echo $row_flete['tipo_vehiculo_solicitado']; ?>">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $row_flete['descripcion']; ?></textarea>

            <label for="pago_ofrecido">Pago Ofrecido:</label>
            <input type="text" id="pago_ofrecido" name="pago_ofrecido" value="<?php echo $row_flete['pago_ofrecido']; ?>" required>

            <button type="submit" class="button">Guardar cambios</button>
        </form>
        <div class="edit-button">
            <button onclick="location.href='publicar_flete.php'" class="button">Regresar</button>
        </div>
    </div>
</body>
</html>