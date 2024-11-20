<?php
session_start(); // Inicia la sesión para poder usar $_SESSION

// Datos de conexión a la base de datos
$servername = "localhost"; // Dirección del servidor de base de datos
$db_username = "root"; // Nombre de usuario de la base de datos
$db_password = ""; // Contraseña del usuario de la base de datos
$dbname = "facetruck"; // Nombre de la base de datos

// Crear una nueva conexión a la base de datos
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verifica si la conexión a la base de datos ha fallado
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Termina el script en caso de error de conexión
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id']; // Asigna el valor del ID del usuario desde la sesión a una variable

// Consultar el tipo de usuario
$sql = "SELECT tipo_usuario FROM usuarios WHERE id = ?"; // Consulta SQL para obtener el tipo de usuario
$stmt = $conn->prepare($sql); // Prepara la consulta SQL
$stmt->bind_param("i", $usuario_id); // Vincula el parámetro ID del usuario a la consulta
$stmt->execute(); // Ejecuta la consulta
$stmt->bind_result($tipo_usuario); // Vincula el resultado de la consulta a la variable tipo_usuario
$stmt->fetch(); // Obtiene el resultado de la consulta
$stmt->close(); // Cierra la declaración preparada

// Obtener los datos del usuario según el tipo
switch ($tipo_usuario) {
    case 'operador':
        $sql = "SELECT pregunta_uno_operadores, pregunta_dos_operadores, pregunta_tres_operadores FROM operadores WHERE usuario_id = ?"; // Consulta SQL para operadores
        break;
    case 'hombreCamion':
        $sql = "SELECT pregunta_uno_hombres_camion, pregunta_dos_hombres_camion, pregunta_tres_hombres_camion FROM hombres_camion WHERE usuario_id = ?"; // Consulta SQL para hombres camión
        break;
    case 'empresas':
        $sql = "SELECT pregunta_uno_empresas, pregunta_dos_empresas, pregunta_tres_empresas FROM empresas WHERE usuario_id = ?"; // Consulta SQL para empresas
        break;
}

$stmt = $conn->prepare($sql); // Prepara la consulta SQL basada en el tipo de usuario
$stmt->bind_param("i", $usuario_id); // Vincula el parámetro ID del usuario a la consulta
$stmt->execute(); // Ejecuta la consulta
$stmt->bind_result($pregunta_uno, $pregunta_dos, $pregunta_tres); // Vincula los resultados de la consulta a las variables correspondientes
$stmt->fetch(); // Obtiene el resultado de la consulta
$stmt->close(); // Cierra la declaración preparada

// Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica si el formulario fue enviado con el método POST
    $pregunta_uno = $_POST['pregunta_uno']; // Asigna el valor de la pregunta uno desde el formulario
    $pregunta_dos = $_POST['pregunta_dos']; // Asigna el valor de la pregunta dos desde el formulario
    $pregunta_tres = $_POST['pregunta_tres']; // Asigna el valor de la pregunta tres desde el formulario

    switch ($tipo_usuario) {
        case 'operador':
            $sql = "UPDATE operadores SET pregunta_uno_operadores = ?, pregunta_dos_operadores = ?, pregunta_tres_operadores = ? WHERE usuario_id = ?"; // Consulta SQL para actualizar operadores
            break;
        case 'hombreCamion':
            $sql = "UPDATE hombres_camion SET pregunta_uno_hombres_camion = ?, pregunta_dos_hombres_camion = ?, pregunta_tres_hombres_camion = ? WHERE usuario_id = ?"; // Consulta SQL para actualizar hombres camión
            break;
        case 'empresa':
            $sql = "UPDATE empresas SET pregunta_uno_empresas = ?, pregunta_dos_empresas = ?, pregunta_tres_empresas = ? WHERE usuario_id = ?"; // Consulta SQL para actualizar empresas
            break;
    }

    $stmt = $conn->prepare($sql); // Prepara la consulta SQL para actualizar los datos
    $stmt->bind_param("sssi", $pregunta_uno, $pregunta_dos, $pregunta_tres, $usuario_id); // Vincula los parámetros a la consulta SQL
    if ($stmt->execute()) { // Ejecuta la consulta y verifica si fue exitosa
        $_SESSION['message'] = 'Perfil actualizado con éxito.'; // Asigna un mensaje de éxito a la sesión
    } else {
        $_SESSION['error'] = 'Error al actualizar el perfil. Inténtalo de nuevo.'; // Asigna un mensaje de error a la sesión
    }
    $stmt->close(); // Cierra la declaración preparada
}

$conn->close(); // Cierra la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Define la codificación de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura la vista para dispositivos móviles -->
    <title>Editar Perfil</title> <!-- Título de la página -->
    <style>
        body {
            font-family: Arial, sans-serif; /* Define la fuente de la página */
            background-color: #f0f0f0; /* Color de fondo de la página */
            display: flex; /* Usar flexbox para centrar el contenido */
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
            height: 100vh; /* Altura de la página completa */
            margin: 0; /* Sin margen */
        }
        .login-container {
            background-color: #ffffff; /* Color de fondo del contenedor de login */
            padding: 20px; /* Espaciado interno */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra del contenedor */
            text-align: center; /* Centrar el texto */
        }
        .login-container img {
            width: 100%; /* Ancho completo */
            max-width: 200px; /* Ancho máximo */
            margin-bottom: 20px; /* Margen inferior */
        }
        .login-container h2 {
            color: #007BFF; /* Color del texto del título */
        }
        .login-container label {
            display: block; /* Mostrar como bloque */
            margin: 10px 0 5px; /* Margen */
            font-weight: bold; /* Texto en negrita */
        }
        .login-container input, .login-container select, .login-container textarea {
            width: calc(100% - 20px); /* Ancho de los inputs y textarea */
            padding: 10px; /* Relleno interno */
            margin: 10px 0; /* Margen */
            border: 1px solid #ccc; /* Bordes de los inputs y textarea */
            border-radius: 4px; /* Bordes redondeados */
            display: block; /* Mostrar como bloque */
        }
        .login-container input[type="submit"] {
            background-color: #007BFF; /* Color de fondo del botón de envío */
            border: none; /* Sin borde */
            color: white; /* Color del texto del botón de envío */
            cursor: pointer; /* Cambiar el cursor a mano */
            margin-top: 10px; /* Margen superior */
            width: 100%; /* Ancho completo */
            padding: 10px; /* Relleno interno */
        }
        .login-container input[type="submit"]:hover {
            background-color: #0056b3; /* Color de fondo del botón de envío al pasar el ratón */
        }
        .login-container button {
            width: 100%; /* Ancho completo */
            padding: 10px; /* Relleno interno */
            background-color: #007BFF; /* Color de fondo de los botones */
            border: none; /* Sin borde */
            color: white; /* Color del texto de los botones */
            border-radius: 4px; /* Bordes redondeados */
            cursor: pointer; /* Cambiar el cursor a mano */
            margin-top: 10px; /* Margen superior */
        }
        .login-container button:hover {
            background-color: #0056b3; /* Color de fondo de los botones al pasar el ratón */
        }
        .error-message {
            color: red; /* Color del texto del mensaje de error */
            margin: 10px 0; /* Margen del mensaje de error */
        }
        .success-message {
            color: green; /* Color del texto del mensaje de éxito */
            margin: 10px 0; /* Margen del mensaje de éxito */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="img/camion.jpg" alt="Camión"> <!-- Imagen de encabezado -->
        <h2>Editar Perfil - <?php echo ucfirst($tipo_usuario); ?></h2> <!-- Título de la página -->
        <?php
        if (isset($_SESSION['error'])) { // Verifica si hay un mensaje de error en la sesión
            echo '<div class="error-message">' . $_SESSION['error'] . '</div>'; // Muestra el mensaje de error
            unset($_SESSION['error']); // Elimina el mensaje de error de la sesión
        }
        if (isset($_SESSION['message'])) { // Verifica si hay un mensaje de éxito en la sesión
            echo '<div class="success-message">' . $_SESSION['message'] . '</div>'; // Muestra el mensaje de éxito
            unset($_SESSION['message']); // Elimina el mensaje de éxito de la sesión
        }
        ?>
        <form action="editar_perfil.php" method="post"> <!-- Formulario de edición de perfil -->
            <label for="pregunta_uno">Pregunta Uno</label> <!-- Etiqueta para la pregunta uno -->
            <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $pregunta_uno; ?>" required> <!-- Campo de entrada para la pregunta uno -->

            <label for="pregunta_dos">Pregunta Dos</label> <!-- Etiqueta para la pregunta dos -->
            <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $pregunta_dos; ?>" required> <!-- Campo de entrada para la pregunta dos -->

            <label for="pregunta_tres">Pregunta Tres</label> <!-- Etiqueta para la pregunta tres -->
            <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $pregunta_tres; ?>" required> <!-- Campo de entrada para la pregunta tres -->

            <input type="submit" value="Guardar Cambios"> <!-- Botón para enviar el formulario -->
        </form>
    </div>
</body>
</html>