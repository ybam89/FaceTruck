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
$stmt = $conn->prepare("SELECT * FROM Usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Error: No se encontró el usuario.";
    exit;
}
$usuario = $result->fetch_assoc();
$stmt->close();
$conn->close();
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
    </style>
</head>
<body>
    <div class="container">
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
    </div>
</body>
</html>