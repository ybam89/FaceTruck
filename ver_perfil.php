<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}

if (!isset($_GET['correo'])) {
    echo "Error: No se ha proporcionado un correo de usuario.";
    exit;
}

$correo = $_GET['correo'];
$usuario_id_actual = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

$stmt = $conn->prepare("SELECT * FROM Usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Error: No se encontró el usuario.";
    exit;
}
$usuario = $result->fetch_assoc();
$usuario_id = $usuario['id'];
$stmt->close();

// Verificar si el usuario actual sigue al usuario del perfil
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM seguidores WHERE seguidor_id = ? AND seguido_id = ?");
$stmt->bind_param("ii", $usuario_id_actual, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$siguiendo = $row['count'] > 0;
$stmt->close();

// Generar el menú basado en el tipo de usuario
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
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
        .profile-picture {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-picture img {
            display: block;
            margin: auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container input[type="text"] {
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
        <a href="universo_facetruck.php" class="button">Regresar</a>
        <div class="profile-picture">
            <img src="<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'img/camion.jpg'); ?>" alt="Foto de Perfil">
        </div>
        <div class="form-container">
            <label for="correo">Correo:</label>
            <input type="text" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" readonly>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" readonly>

            <label for="tipo_usuario">Tipo de Usuario:</label>
            <input type="text" id="tipo_usuario" name="tipo_usuario" value="<?php echo htmlspecialchars($usuario['tipo_usuario']); ?>" readonly>
        </div>
        <button id="follow-button" class="button" data-seguidor-id="<?php echo $usuario_id_actual; ?>" data-seguido-id="<?php echo $usuario_id; ?>">
            <?php echo $siguiendo ? 'Dejar de seguir' : 'Seguir'; ?>
        </button>
    </div>
    <script>
        document.getElementById('follow-button').addEventListener('click', function() {
            var button = this;
            var seguidorId = button.getAttribute('data-seguidor-id');
            var seguidoId = button.getAttribute('data-seguido-id');
            var action = button.innerText === 'Seguir' ? 'seguir' : 'dejar_de_seguir';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === 'ok') {
                        button.innerText = action === 'seguir' ? 'Dejar de seguir' : 'Seguir';
                    } else {
                        alert('Error al realizar la acción. Inténtalo de nuevo.');
                    }
                }
            };
            xhr.send('action=' + action + '&seguidor_id=' + encodeURIComponent(seguidorId) + '&seguido_id=' + encodeURIComponent(seguidoId));
        });
    </script>
</body>
</html>