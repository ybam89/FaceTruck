<?php
session_start(); // Inicia la sesión

// Incluir el archivo de conexión a la base de datos
include 'db.php';

// Obtener el ID del usuario y el tipo de usuario desde la sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

date_default_timezone_set('America/Mexico_City');

// Incluir al inicio del archivo, después de la conexión a la base de datos y la obtención del usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contenido'])) {
    $contenido = sanitizeInput($_POST['contenido']);
    $imagen = ''; // Manejar la carga de imágenes aquí si es necesario
    $fecha_publicacion = date('Y-m-d H:i:s'); // Fecha y hora actual

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
    }

    // Usar sentencias preparadas para prevenir inyecciones SQL
    $stmt = $conn->prepare("INSERT INTO publicaciones (usuario_id, contenido, imagen, fecha_publicacion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $contenido, $imagen, $fecha_publicacion);
    $stmt->execute();
    $stmt->close();

    // Redirigir después de procesar el formulario para evitar duplicaciones
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['disponibilidad'])) {
    $disponibilidad = (int)$_POST['disponibilidad'];
    if ($tipo_usuario == 'operador') {
        $stmt = $conn->prepare("UPDATE operadores SET disponibilidad = ? WHERE usuario_id = ?");
    } elseif ($tipo_usuario == 'hombreCamion') {
        $stmt = $conn->prepare("UPDATE hombres_camion SET disponibilidad = ? WHERE usuario_id = ?");
    }
    $stmt->bind_param("ii", $disponibilidad, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch publicaciones del usuario
$publicaciones = [];
$stmt = $conn->prepare("SELECT * FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC LIMIT 10");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $publicaciones[] = $row;
}
$stmt->close();

// Consulta para obtener la información del usuario según el tipo
switch ($tipo_usuario) {
    case 'operador':
        $sql = "SELECT pregunta_uno_operadores, pregunta_dos_operadores, pregunta_tres_operadores, disponibilidad FROM operadores WHERE usuario_id = ?";
        break;
    case 'hombreCamion':
        $sql = "SELECT pregunta_uno_hombres_camion, pregunta_dos_hombres_camion, pregunta_tres_hombres_camion, disponibilidad FROM hombres_camion WHERE usuario_id = ?";
        break;
    case 'empresa':
        $sql = "SELECT pregunta_uno_empresas, pregunta_dos_empresas, pregunta_tres_empresas FROM empresas WHERE usuario_id = ?";
        break;
    default:
        echo "Error: Tipo de usuario no válido.";
        exit;
}

$stmt = $conn->prepare($sql); // Prepara la consulta SQL
$stmt->bind_param("i", $usuario_id); // Vincula el parámetro ID del usuario
$stmt->execute(); // Ejecuta la consulta
$result = $stmt->get_result(); // Obtiene el resultado de la consulta

// Verificar si se encontró algún resultado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Obtener los datos del usuario
    if ($tipo_usuario == 'operador' || $tipo_usuario == 'hombreCamion') {
        $disponibilidad_actual = $row['disponibilidad'] ?? '';
    }
} else {
    echo "No se encontró información del usuario.";
    exit;
}

$stmt->close(); // Cierra la declaración preparada

// Consulta para obtener el correo y foto de perfil del usuario desde la tabla Usuarios
$sql_email = "SELECT correo, foto_perfil FROM Usuarios WHERE id = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("i", $usuario_id);
$stmt_email->execute();
$result_email = $stmt_email->get_result();

// Verificar si se encontró algún resultado para el correo y la foto de perfil
if ($result_email->num_rows > 0) {
    $row_email = $result_email->fetch_assoc();
    $correo = $row_email['correo'];
    $foto_perfil = $row_email['foto_perfil']; // Obtener la ruta de la foto de perfil
} else {
    $correo = "Correo no encontrado";
    $foto_perfil = 'img/camion.jpg'; // Imagen predeterminada
}

$stmt_email->close(); // Cierra la declaración preparada

// Fetch job offers for 'empresa' users before closing the connection
$job_offers = [];
if ($tipo_usuario == 'empresa') {
    $sql_ofertas = "SELECT * FROM ofertas_empleo";
    $result_ofertas = $conn->query($sql_ofertas);
    if ($result_ofertas->num_rows > 0) {
        while($row_oferta = $result_ofertas->fetch_assoc()) {
            $job_offers[] = $row_oferta;
        }
    }
}

// Establecer la imagen de perfil predeterminada si no hay una imagen de perfil
$foto_perfil = $foto_perfil ?? 'img/camion.jpg'; // Usa un valor predeterminado si no está definido

// Menús según el tipo de usuario
$menu = '';
switch ($tipo_usuario) {
    case 'operador':
        $menu = '<ul>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="ofertas_empleo.php">Ofertas de empleo</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                 </ul>';
        break;
    case 'hombreCamion':
        $menu = '<ul>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                    <li><a href="ofertas_empresas.php">Ofertas de empresas</a></li>
                    <li><a href="buscar_operadores.php">Buscar operadores</a></li>
                    <li><a href="buscar_fletes.php">Buscar fletes eventuales</a></li>
                    <li><a href="publicar_vacante.php">Publicar y consultar mis vacantes "operador"</a></li>
                 </ul>';
        break;
    case 'empresa':
        $menu = '<ul>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                    <li><a href="buscar_operadores.php">Buscar operadores</a></li>
                    <li><a href="buscar_hombres_camion.php">Buscar Hombres camión</a></li>
                    <li><a href="buscar_ofertas_rutas.php">Buscar ofertas de rutas</a></li>
                    <li><a href="publicar_vacante.php">Publicar y consultar mis vacantes "operador"</a></li>
                    <li><a href="publicar_flete.php">Publicar y consultar mis Fletes eventuales</a></li>
                    <li><a href="publicar_oferta_ruta.php">Publicar y consultar oferta de ruta</a></li>
                </ul>';
        break;
}

// Añade esta línea al final
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario - FaceTruck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>