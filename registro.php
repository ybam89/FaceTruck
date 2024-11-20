<?php
session_start(); // Inicia la sesión para poder usar $_SESSION

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica si el formulario fue enviado con el método POST
    $correo = $_POST['email']; // Obtiene el correo electrónico del formulario
    $password = $_POST['password']; // Obtiene la contraseña del formulario
    $confirm_password = $_POST['confirm_password']; // Obtiene la confirmación de la contraseña del formulario
    $tipo_usuario = $_POST['user_type']; // Obtiene el tipo de usuario del formulario

    // Validación de la contraseña
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un número.';
        header("Location: registro.php"); // Redirige a la página de registro en caso de error
        exit(); // Termina el script
    }

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header("Location: registro.php"); // Redirige a la página de registro en caso de error
        exit(); // Termina el script
    }

    // Datos de conexión a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "facetruck";

    // Crear una nueva conexión a la base de datos
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verifica si la conexión a la base de datos ha fallado
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error); // Termina el script en caso de error
    }

    // Verificar si el correo ya está registrado
    $sql = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql); // Prepara una consulta SQL
    $stmt->bind_param("s", $correo); // Asigna el valor del correo a la consulta preparada
    $stmt->execute(); // Ejecuta la consulta
    $stmt->store_result(); // Almacena el resultado de la consulta

    if ($stmt->num_rows > 0) { // Verifica si se encontró un usuario con el correo proporcionado
        $_SESSION['error'] = 'El correo electrónico ya está registrado.';
        $stmt->close(); // Cierra la declaración
        $conn->close(); // Cierra la conexión a la base de datos
        header("Location: registro.php"); // Redirige a la página de registro en caso de error
        exit(); // Termina el script
    }

    // Registrar el nuevo usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Encripta la contraseña
    $sql = "INSERT INTO usuarios (correo, password, tipo_usuario) VALUES (?, ?, ?)"; // Actualiza la consulta para incluir el tipo de usuario
    $stmt = $conn->prepare($sql); // Prepara una consulta SQL
    $stmt->bind_param("sss", $correo, $hashed_password, $tipo_usuario); // Asigna los valores del correo, la contraseña encriptada y el tipo de usuario a la consulta preparada

    if ($stmt->execute()) { // Ejecuta la consulta y verifica si fue exitosa
        $_SESSION['usuario_id'] = $stmt->insert_id; // Establece el ID del usuario recién registrado en la sesión
        $_SESSION['message'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
        header("Location: editar_perfil.php"); // Redirige a la página de edición de perfil
        exit(); // Termina el script
    } else {
        $_SESSION['error'] = 'Error al registrar el usuario. Inténtalo de nuevo.';
    }

    $stmt->close(); // Cierra la declaración
    $conn->close(); // Cierra la conexión a la base de datos
    header("Location: registro.php"); // Redirige a la página de registro en caso de error
    exit(); // Termina el script
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Define la codificación de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura la vista para dispositivos móviles -->
    <title>Registro - FaceTruck</title> <!-- Título de la página -->
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
        .login-container input, .login-container select {
            width: calc(100% - 20px); /* Ancho de los inputs y el select */
            padding: 10px; /* Relleno interno */
            margin: 10px 0; /* Margen */
            border: 1px solid #ccc; /* Bordes de los inputs y el select */
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
    <script>
        function validateForm() { // Función para validar el formulario
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            if (password !== confirmPassword) { // Verifica si las contraseñas coinciden
                alert("Las contraseñas no coinciden."); // Muestra una alerta en caso de error
                return false; // Previene el envío del formulario
            }
            return true; // Permite el envío del formulario
        }
    </script>
</head>
<body>
    <div class="login-container">
        <img src="img/camion.jpg" alt="Camión"> <!-- Imagen de encabezado -->
        <h2>Registro - FaceTruck</h2> <!-- Título de la página -->
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
        <form action="registro.php" method="post" onsubmit="return validateForm()"> <!-- Formulario de registro -->
            <label for="user_type">Tipo de Usuario</label> <!-- Etiqueta para el tipo de usuario -->
            <select id="user_type" name="user_type" required> <!-- Campo de selección para el tipo de usuario -->
                <option value="operador">Operador</option>
                <option value="hombreCamion">Hombre Camión</option>
                <option value="empresa">Empresa</option>
            </select>

            <label for="email">Correo Electrónico</label> <!-- Etiqueta para el correo electrónico -->
            <input type="email" id="email" name="email" placeholder="Correo Electrónico" required> <!-- Campo de entrada para el correo electrónico -->
            
            <label for="password">Contraseña</label> <!-- Etiqueta para la contraseña -->
            <input type="password" id="password" name="password" placeholder="Contraseña" required> <!-- Campo de entrada para la contraseña -->
            
            <label for="confirm_password">Confirmar Contraseña</label> <!-- Etiqueta para confirmar la contraseña -->
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required> <!-- Campo de entrada para confirmar la contraseña -->
            
            <input type="submit" value="Registrarse"> <!-- Botón de envío del formulario -->
        </form>
        <button onclick="location.href='login.php'" aria-label="Iniciar Sesión">Iniciar Sesión</button> <!-- Botón para ir a la página de inicio de sesión -->
    </div>
</body>
</html>