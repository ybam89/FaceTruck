<?php
session_start();

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

// Obtener el ID de la oferta desde la URL
$oferta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($oferta_id == 0) {
    echo "Error: ID de la oferta no válido.";
    exit;
}

// Consultar la información de la oferta
$sql_oferta = "SELECT oe.*, u.* FROM ofertas_empleo oe
               JOIN usuarios u ON oe.usuario_id = u.id
               WHERE oe.id = ?";
$stmt = $conn->prepare($sql_oferta);
$stmt->bind_param("i", $oferta_id);
$stmt->execute();
$result_oferta = $stmt->get_result();

if ($result_oferta->num_rows == 0) {
    echo "Error: No se encontró la oferta.";
    exit;
}

$oferta = $result_oferta->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Oferta de Empleo</title>
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
        .logout-button, .back-button, .profile-button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 10px;
            margin-right: 10px;
        }
        .logout-button:hover, .back-button:hover, .profile-button:hover {
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
                <ul>
                    <li><a href="ofertas_empleo.php">Ofertas de empleo</a></li>
                </ul>
            </div>
        </div>
        <a href="logout.php" class="logout-button">Cerrar sesión</a>
        <button onclick="location.href='ofertas_empleo.php'" class="back-button">Atrás</button>
        <button onclick="location.href='perfil.php'" class="profile-button">Volver al perfil</button>
        <h2>Detalles de la Oferta de Empleo</h2>
        <ul>
            <li><strong>Vigente:</strong> <?php echo htmlspecialchars($oferta['vigente'] == '1' ? 'Sí' : 'No'); ?></li>
            <li><strong>Estado:</strong> <?php echo htmlspecialchars($oferta['estado']); ?></li>
            <li><strong>Municipio:</strong> <?php echo htmlspecialchars($oferta['municipio']); ?></li>
            <li><strong>Fecha de Publicación:</strong> <?php echo htmlspecialchars($oferta['fecha_publicacion']); ?></li>
            <li><strong>Sueldo:</strong> <?php echo htmlspecialchars($oferta['sueldo']); ?></li>
            <li><strong>Tipo de Viaje:</strong> <?php echo htmlspecialchars($oferta['tipo_viaje']); ?></li>
            <li><strong>Descripción de Ruta:</strong> <?php echo htmlspecialchars($oferta['descripcion_ruta']); ?></li>
            <li><strong>Tipo de Vehículo y Remolque:</strong> <?php echo htmlspecialchars($oferta['tipo_vehiculo_remolque']); ?></li>
            <li><strong>Requisitos:</strong> <?php echo htmlspecialchars($oferta['requisitos']); ?></li>
            <li><strong>Prestaciones:</strong> <?php echo htmlspecialchars($oferta['prestaciones']); ?></li>
            <li><strong>Contacto:</strong> <?php echo htmlspecialchars($oferta['contacto']); ?></li>
        </ul>

        <h2>Detalles del Usuario</h2>
        <ul>
            <li><strong>Nombre:</strong> <?php echo htmlspecialchars($oferta['nombre']); ?></li>
            <li><strong>Correo:</strong> <?php echo htmlspecialchars($oferta['correo']); ?></li>
            <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($oferta['telefono']); ?></li>
            <li><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($oferta['tipo_usuario']); ?></li>
        </ul>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>