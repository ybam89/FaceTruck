<?php
session_start();

// Datos de conexión a la base de datos
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "facetruck";

// Crear una nueva conexión a la base de datos
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verifica si la conexión a la base de datos ha fallado
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];

// Consultar el tipo de usuario
$sql = "SELECT tipo_usuario FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($tipo_usuario);
$stmt->fetch();
$stmt->close();

// Obtener los datos del usuario según el tipo
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
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($pregunta_uno, $pregunta_dos, $pregunta_tres);
$stmt->fetch();
$stmt->close();

// Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pregunta_uno = $_POST['pregunta_uno'];
    $pregunta_dos = $_POST['pregunta_dos'];
    $pregunta_tres = $_POST['pregunta_tres'];

    switch ($tipo_usuario) {
        case 'operador':
            $sql = "UPDATE operadores SET pregunta_uno_operadores = ?, pregunta_dos_operadores = ?, pregunta_tres_operadores = ? WHERE usuario_id = ?";
            break;
        case 'hombreCamion':
            $sql = "UPDATE hombres_camion SET pregunta_uno_hombres_camion = ?, pregunta_dos_hombres_camion = ?, pregunta_tres_hombres_camion = ? WHERE usuario_id = ?";
            break;
        case 'empresa':
            $sql = "UPDATE empresas SET pregunta_uno_empresas = ?, pregunta_dos_empresas = ?, pregunta_tres_empresas = ? WHERE usuario_id = ?";
            break;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $pregunta_uno, $pregunta_dos, $pregunta_tres, $usuario_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Perfil actualizado con éxito.';
    } else {
        $_SESSION['error'] = 'Error al actualizar el perfil. Inténtalo de nuevo.';
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-container img {
            width: 100%;
            max-width: 200px;
            margin-bottom: 20px;
        }
        .login-container h2 {
            color: #007BFF;
        }
        .login-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .login-container input, .login-container select, .login-container textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: block;
        }
        .login-container input[type="submit"] {
            background-color: #007BFF;
            border: none;
            color: white;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            padding: 10px;
        }
        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin: 10px 0;
        }
        .success-message {
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="img/camion.jpg" alt="Camión">
        <h2>Editar Perfil - <?php echo ucfirst($tipo_usuario); ?></h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['message'])) {
            echo '<div class="success-message">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>
        <form action="editar_perfil.php" method="post">
            <label for="pregunta_uno">Pregunta Uno</label>
            <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $pregunta_uno; ?>" required>

            <label for="pregunta_dos">Pregunta Dos</label>
            <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $pregunta_dos; ?>" required>

            <label for="pregunta_tres">Pregunta Tres</label>
            <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $pregunta_tres; ?>" required>

            <input type="submit" value="Guardar Cambios">
        </form>
    </div>
</body>
</html>