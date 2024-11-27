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

// Consulta para obtener la información del usuario según el tipo
switch ($tipo_usuario) {
    case 'operador':
        $sql = "SELECT pregunta_uno_operadores, pregunta_dos_operadores, pregunta_tres_operadores FROM operadores WHERE usuario_id = ?";
        break;
    case 'hombreCamion':
        $sql = "SELECT pregunta_uno_hombres_camion, pregunta_dos_hombres_camion, pregunta_tres_hombres_camion FROM hombres_camion WHERE usuario_id = ?";
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

if ($result_email->num_rows > 0) {
    $row_email = $result_email->fetch_assoc();
    $correo = $row_email['correo'];
    $foto_perfil = $row_email['foto_perfil']; // Obtener la ruta de la foto de perfil
} else {
    $correo = "Correo no encontrado";
    $foto_perfil = 'img/camion.jpg'; // Imagen predeterminada
}

$stmt_email->close(); // Cierra la declaración preparada
$conn->close(); // Cierra la conexión a la base de datos

// Establecer la imagen de perfil predeterminada si no hay una imagen de perfil
$foto_perfil = $foto_perfil ?? 'img/camion.jpg'; // Usa un valor predeterminado si no está definido
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario - FaceTruck</title>
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
        }
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
            margin-bottom: 20px;
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
        .profile-picture form {
            display: inline-block;
            margin-top: 10px;
        }
        .profile-picture input[type="file"] {
            display: none;
        }
        .profile-picture label {
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
        .profile-picture label:hover {
            background-color: #0056b3;
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
        .edit-button {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-picture">
            <img src="<?php echo $foto_perfil; ?>" alt="Foto de Perfil">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="fileToUpload" id="fileToUpload">
                <label for="fileToUpload" class="button">Cambiar foto de perfil</label>
                <button type="submit" class="button">Actualizar foto</button>
            </form>
            <p><?php echo $correo; ?></p>
            <p><?php echo ucfirst($tipo_usuario); ?></p>
        </div>

        <!-- Mostrar el formulario según el tipo de usuario -->
        <?php if ($tipo_usuario == 'operador'): ?>
            <h2>Formulario para Operadores</h2>
            <div class="form-container">
                <label for="pregunta_uno">Pregunta uno operadores</label>
                <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $row['pregunta_uno_operadores']; ?>" readonly>
                
                <label for="pregunta_dos">Pregunta dos operadores</label>
                <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $row['pregunta_dos_operadores']; ?>" readonly>
                
                <label for="pregunta_tres">Pregunta tres operadores</label>
                <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $row['pregunta_tres_operadores']; ?>" readonly>
            </div>
        <?php elseif ($tipo_usuario == 'hombreCamion'): ?>
            <h2>Formulario para Hombres Camión</h2>
            <div class="form-container">
                <label for="pregunta_uno">Pregunta uno hombres camión</label>
                <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $row['pregunta_uno_hombres_camion']; ?>" readonly>
                
                <label for="pregunta_dos">Pregunta dos hombres camión</label>
                <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $row['pregunta_dos_hombres_camion']; ?>" readonly>
                
                <label for="pregunta_tres">Pregunta tres hombres camión</label>
                <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $row['pregunta_tres_hombres_camion']; ?>" readonly>
            </div>
        <?php elseif ($tipo_usuario == 'empresa'): ?>
            <h2>Formulario para Empresas</h2>
            <div class="form-container">
                <label for="pregunta_uno">Pregunta uno empresas</label>
                <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $row['pregunta_uno_empresas']; ?>" readonly>
                
                <label for="pregunta_dos">Pregunta dos empresas</label>
                <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $row['pregunta_dos_empresas']; ?>" readonly>
                
                <label for="pregunta_tres">Pregunta tres empresas</label>
                <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $row['pregunta_tres_empresas']; ?>" readonly>
            </div>
        <?php endif; ?>

        <div class="edit-button">
            <button onclick="location.href='editar_perfil.php'" class="button">Editar perfil</button>
        </div>
    </div>
</body>
</html>