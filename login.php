<?php
session_start(); // Inicia la sesión para poder usar $_SESSION

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['username']; // Obtiene el correo electrónico del formulario
    $password = $_POST['password']; // Obtiene la contraseña del formulario

    // Datos de conexión a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "facetruck";

    // Crea una nueva conexión a la base de datos
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verifica si la conexión a la base de datos ha fallado
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Prepara una consulta SQL para verificar el correo en la base de datos
    $sql = "SELECT id, password, tipo_usuario FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    // Asigna el valor del correo a la consulta preparada
    $stmt->bind_param("s", $correo);
    // Ejecuta la consulta
    $stmt->execute();
    // Almacena el resultado de la consulta
    $stmt->store_result();

    // Verifica si se encontró un usuario con el correo proporcionado
    if ($stmt->num_rows > 0) {
        // Asigna los resultados de la consulta a variables
        $stmt->bind_result($id, $hashed_password, $tipo_usuario);
        $stmt->fetch();
        // Verifica si la contraseña proporcionada coincide con la almacenada en la base de datos
        if (password_verify($password, $hashed_password)) {
            // Almacena el id del usuario y tipo_usuario en la sesión
            $_SESSION['usuario_id'] = $id;
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            echo 'Inicio de sesión exitoso!<br>';
            echo 'ID de usuario: ' . $id;
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "perfil.php";
                    }, 5000);
                  </script>';
            exit();
        } else {
            // Si la contraseña es incorrecta, establece un mensaje de error en la sesión
            $_SESSION['error'] = 'Contraseña incorrecta';
        }
    } else {
        // Si no se encontró el correo en la base de datos, establece un mensaje de error en la sesión
        $_SESSION['error'] = 'Correo electrónico no encontrado';
    }

    // Cierra la declaración y la conexión a la base de datos
    $stmt->close();
    $conn->close();

    // Redirige de vuelta a la página de inicio de sesión
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Define la codificación de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura la vista para dispositivos móviles -->
    <title>Iniciar Sesión - FaceTruck</title> <!-- Título de la página -->
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
        .login-container input {
            width: calc(100% - 20px); /* Ancho de los inputs */
            padding: 10px; /* Relleno interno */
            margin: 10px 0; /* Margen */
            border: 1px solid #ccc; /* Bordes de los inputs */
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
    </style>
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> --> <!-- Script para cargar reCAPTCHA -->
</head>
<body>
    <div class="login-container">
        <img src="img/camion.jpg" alt="Camión"> <!-- Imagen de encabezado -->
        <h2>Inicio de Sesión - FaceTruck</h2> <!-- Título de la página -->
        <?php
        if (isset($_SESSION['error'])) {
            // Muestra el mensaje de error si existe en la sesión
            echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Elimina el mensaje de error de la sesión
        }
        ?>
        <form action="login.php" method="post"> <!-- Formulario de inicio de sesión -->
            <label for="username">Correo Electrónico</label>
            <input type="email" id="username" name="username" placeholder="Correo Electrónico" autocomplete="username" required> <!-- Campo de entrada para el correo electrónico -->
            
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Contraseña" autocomplete="current-password" required> <!-- Campo de entrada para la contraseña -->
            
            <!-- <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div> --> <!-- Añadir reCAPTCHA al formulario -->
            
            <input type="submit" value="Iniciar Sesión"> <!-- Botón para enviar el formulario -->
        </form>
        <button onclick="location.href='registro.php'" aria-label="Registrarse">Registrarse</button> <!-- Botón para ir a la página de registro -->
        <button onclick="location.href='olvide_contraseña.html'" aria-label="Olvidé mi contraseña">Olvidé mi contraseña</button> <!-- Botón para ir a la página de recuperación de contraseña -->
    </div>
</body>
</html>