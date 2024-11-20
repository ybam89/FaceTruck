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
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario y el tipo de usuario desde la sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    die("No se ha iniciado sesión correctamente.");
}
$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Consulta para obtener la información del usuario según el tipo
switch ($tipo_usuario) {
    case 'operador':
        $sql = "SELECT * FROM operadores WHERE usuario_id = ?";
        break;
    case 'hombreCamion':
        $sql = "SELECT * FROM hombres_camion WHERE usuario_id = ?";
        break;
    case 'empresa':
        $sql = "SELECT * FROM empresas WHERE usuario_id = ?";
        break;
    default:
        die("Tipo de usuario no válido.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id); // Vincula el parámetro ID del usuario
$stmt->execute();
$result = $stmt->get_result(); // Ejecuta la consulta y obtiene el resultado

// Verificar si se encontró algún resultado
if ($result->num_rows > 0) {
    // Obtener los datos del usuario
    $row = $result->fetch_assoc();
    $nombre_completo = $row['nombre_completo'] ?? $row['nombre'];
    $edad = $row['edad'] ?? '';
    $ciudad = $row['ciudad'] ?? '';
    $estado = $row['estado'] ?? '';
    $telefono = $row['telefono'] ?? '';
    $correo = $row['correo'] ?? '';
    $foto_perfil = $row['foto_perfil'] ?? '';
    $experiencia_anos = $row['experiencia_anos'] ?? '';
    $tipos_unidades = $row['tipos_unidades'] ?? '';
    $empresas = $row['empresas'] ?? '';
    $rutas = $row['rutas'] ?? '';
    $licencia_tipo = $row['licencia_tipo'] ?? '';
    $licencia_vigencia = $row['licencia_vigencia'] ?? '';
    $materiales_peligrosos = $row['materiales_peligrosos'] ?? 0;
    $otros_certificados = $row['otros_certificados'] ?? '';
    $disponibilidad_viajar = $row['disponibilidad_viajar'] ?? 0;
    $disponibilidad_horarios = $row['disponibilidad_horarios'] ?? 0;
    $nivel_mecanica = $row['nivel_mecanica'] ?? '';
    $nivel_seguridad_vial = $row['nivel_seguridad_vial'] ?? '';
    $habilidad_gps = $row['habilidad_gps'] ?? '';
    $manejo_bitacoras = $row['manejo_bitacoras'] ?? '';
} else {
    echo "No se encontró información del usuario.";
}

$stmt->close();
$conn->close();

// Establecer la imagen de perfil predeterminada si no hay una imagen de perfil
if (empty($foto_perfil)) {
    $foto_perfil = 'img/imgprofile.jpg';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario - FaceTruck</title>
    <style>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        .section p {
            margin: 5px 0;
        }
        .profile-picture {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-picture img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-picture form {
            margin-top: 10px;
        }
        .profile-picture input[type="file"] {
            display: none;
        }
        .profile-picture label {
            cursor: pointer;
            color: #007BFF;
            text-decoration: underline;
        }
        .profile-picture button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .edit-button {
            text-align: center;
            margin-top: 20px;
        }
        .edit-button button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout button {
            background-color: #FF0000;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="logout">
        <form action="logout.php" method="post">
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>
    <div class="container">
        <div class="profile-picture">
            <img src="<?php echo $foto_perfil; ?>" alt="Foto de Perfil">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="fileToUpload" id="fileToUpload">
                <label for="fileToUpload">Cambiar foto de perfil</label>
                <button type="submit" value="Upload Image">Actualizar foto</button>
            </form>
        </div>

        <h2>Información Personal</h2>
        <div class="section">
            <label>Nombre completo:</label>
            <p><?php echo $nombre_completo; ?></p>
            <label>Edad:</label>
            <p><?php echo $edad; ?> años</p>
            <label>Domicilio:</label>
            <p><?php echo $ciudad . ", " . $estado; ?></p>
            <label>Teléfono de contacto:</label>
            <p><?php echo $telefono; ?></p>
            <label>Correo electrónico:</label>
            <p><?php echo $correo; ?></p>
        </div>

        <h2>Experiencia Laboral</h2>
        <div class="section">
            <label>Años de experiencia manejando unidades pesadas:</label>
            <p><?php echo $experiencia_anos; ?></p>
            <label>Tipo de unidades que he manejado:</label>
            <p><?php echo $tipos_unidades; ?></p>
            <label>Empresas anteriores y duración del empleo:</label>
            <p><?php echo $empresas; ?></p>
            <label>Rutas manejadas:</label>
            <p><?php echo $rutas; ?></p>
        </div>

        <h2>Licencia y Certificaciones</h2>
        <div class="section">
            <label>Tipo de licencia de conducir y vigencia:</label>
            <p><?php echo $licencia_tipo . " (Vigencia: " . $licencia_vigencia . ")"; ?></p>
            <label>¿Cuenta con certificación para manejo de materiales peligrosos?</label>
            <p><?php echo $materiales_peligrosos ? 'Sí' : 'No'; ?></p>
            <label>Otros certificados o capacitaciones relevantes:</label>
            <p><?php echo $otros_certificados; ?></p>
        </div>

        <h2>Disponibilidad</h2>
        <div class="section">
            <label>¿Está dispuesto a viajar o hacer rutas nacionales?</label>
            <p><?php echo $disponibilidad_viajar ? 'Sí' : 'No'; ?></p>
            <label>Disponibilidad para horarios variables o nocturnos:</label>
            <p><?php echo $disponibilidad_horarios ? 'Sí' : 'No'; ?></p>
        </div>

        <h2>Competencias</h2>
        <div class="section">
            <label>Nivel de conocimiento en mecánica básica:</label>
            <p><?php echo $nivel_mecanica; ?></p>
            <label>Nivel de conocimiento en seguridad vial y normas de tránsito:</label>
            <p><?php echo $nivel_seguridad_vial; ?></p>
            <label>Habilidad para el uso de GPS y otras herramientas de navegación:</label>
            <p><?php echo $habilidad_gps; ?></p>
            <label>Manejo de bitácoras o reportes de viaje:</label>
            <p><?php echo $manejo_bitacoras; ?></p>
        </div>

        <div class="edit-button">
            <button onclick="location.href='editar_perfil.php'">Editar Información</button>
        </div>
    </div>
</body>
</html> 