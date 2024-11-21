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
    die("No se ha iniciado sesión correctamente."); // Termina la ejecución si no se encuentran las variables de sesión
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
        die("Tipo de usuario no válido."); // Termina la ejecución si el tipo de usuario no es válido
}

$stmt = $conn->prepare($sql); // Prepara la consulta SQL
$stmt->bind_param("i", $usuario_id); // Vincula el parámetro ID del usuario
$stmt->execute(); // Ejecuta la consulta
$result = $stmt->get_result(); // Obtiene el resultado de la consulta

// Verificar si se encontró algún resultado
if ($result->num_rows > 0) {
    // Obtener los datos del usuario
    $row = $result->fetch_assoc();
} else {
    echo "No se encontró información del usuario."; // Muestra un mensaje si no se encontró información del usuario
}

$stmt->close(); // Cierra la declaración preparada

// Consulta para obtener el correo del usuario desde la tabla Usuarios
$sql_email = "SELECT correo FROM Usuarios WHERE id = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("i", $usuario_id);
$stmt_email->execute();
$result_email = $stmt_email->get_result();

if ($result_email->num_rows > 0) {
    $row_email = $result_email->fetch_assoc();
    $correo = $row_email['correo'];
} else {
    $correo = "Correo no encontrado";
}

$stmt_email->close(); // Cierra la declaración preparada
$conn->close(); // Cierra la conexión a la base de datos

// Establecer la imagen de perfil predeterminada si no hay una imagen de perfil
if (empty($foto_perfil)) {
    $foto_perfil = 'img/camion.jpg'; // Asigna la imagen de perfil predeterminada
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
        .profile-picture p {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
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
            <p><?php echo $correo; ?></p>
            <p><?php echo ucfirst($tipo_usuario); ?></p> <!-- Mostrar el tipo de usuario -->
        </div>

        <div class="edit-button">
            <button onclick="location.href='editar_perfil.php'">Editar Información</button>
        </div>
    </div>
</body>
</html>