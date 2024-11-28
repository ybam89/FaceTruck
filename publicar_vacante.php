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
                <li><a href="publicar_vacante.php">Publicar vacante operador</a></li>
                <li><a href="publicar_flete.php">Publicar Flete eventual</a></li>
                <li><a href="publicar_oferta_ruta.php">Publicar oferta de ruta</a></li>
             </ul>';
} else {
    echo "Error: Acceso no autorizado.";
    exit;
}

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

    // Insertar los datos en la tabla ofertas_empleo
    $sql = "INSERT INTO ofertas_empleo (vigente, estado, municipio, fecha_publicacion, sueldo, tipo_viaje, descripcion_ruta, tipo_vehiculo_remolque, requisitos, prestaciones, contacto, usuario_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $vigente, $estado, $municipio, $fecha_publicacion, $sueldo, $tipo_viaje, $descripcion_ruta, $tipo_vehiculo_remolque, $requisitos, $prestaciones, $contacto, $usuario_id);

    if ($stmt->execute()) {
        echo "Vacante publicada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Vacante - FaceTruck</title>
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
        <h2>Publicar Vacante</h2>
        <form method="post" class="form-container">
            <label for="vigente">Vigente:</label>
            <select id="vigente" name="vigente">
                <option value="Sí">Sí</option>
                <option value="No">No</option>
            </select>

            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" required>

            <label for="municipio">Municipio:</label>
            <input type="text" id="municipio" name="municipio" required>

            <label for="fecha_publicacion">Fecha de publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" required>

            <label for="sueldo">Sueldo:</label>
            <input type="text" id="sueldo" name="sueldo" required>

            <label for="tipo_viaje">Tipo de viaje:</label>
            <select id="tipo_viaje" name="tipo_viaje">
                <option value="Foráneo">Foráneo</option>
                <option value="Local">Local</option>
            </select>

            <label for="descripcion_ruta">Descripción de ruta:</label>
            <textarea id="descripcion_ruta" name="descripcion_ruta" rows="4" required></textarea>

            <label for="tipo_vehiculo_remolque">Tipo de vehículo y remolque (si aplica):</label>
            <input type="text" id="tipo_vehiculo_remolque" name="tipo_vehiculo_remolque">

            <label for="requisitos">Requisitos:</label>
            <textarea id="requisitos" name="requisitos" rows="4" required></textarea>

            <label for="prestaciones">Prestaciones:</label>
            <textarea id="prestaciones" name="prestaciones" rows="4" required></textarea>

            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto" required>

            <button type="submit" class="button">Guardar cambios</button>
        </form>
        <div class="edit-button">
            <button onclick="location.href='perfil.php'" class="button">Regresar</button>
        </div>
    </div>
</body>
</html>