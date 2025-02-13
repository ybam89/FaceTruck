<?php
session_start(); // Inicia la sesión para poder usar $_SESSION

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificación de reCAPTCHA
    $recaptcha_secret = 'YOUR_SECRET_KEY'; // Reemplaza 'YOUR_SECRET_KEY' con tu clave secreta de reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response']; // Obtiene la respuesta de reCAPTCHA del formulario
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    
    // Solicita la verificación de reCAPTCHA a Google
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);
    
    // Verifica si la validación de reCAPTCHA fue exitosa
    if (!$recaptcha->success) {
        $_SESSION['error'] = 'Error de verificación de reCAPTCHA. Inténtalo de nuevo.';
        header("Location: login.php");
        exit();
    }

    $correo = $_POST['username']; // Obtiene el correo electrónico del formulario
    $password = $_POST['password']; // Obtiene la contraseña del formulario

    // Datos de conexión a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "pjyWa2THaRii5L2kC4LlvO8uofNIQ7dlTzyI0LpIuUea0Q44sz";
    $dbname = "facetruck";

    // Crea una nueva conexión a la base de datos
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verifica si la conexión a la base de datos ha fallado
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Prepara una consulta SQL para verificar el correo en la base de datos
    $sql = "SELECT id, password FROM usuarios WHERE correo = ?";
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
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        // Verifica si la contraseña proporcionada coincide con la almacenada en la base de datos
        if (password_verify($password, $hashed_password)) {
            // Almacena el id del usuario en la sesión
            $_SESSION['operador_id'] = $id;
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
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- Script para cargar reCAPTCHA -->
</head>
<body>
    <div class="login-container">
        <img src="img/camion.jpg" alt="Camión" width="585" height="148" class="header-image"> <!-- Imagen de encabezado -->
      <h2>Inicio de Sesión - FaceTruck ok</h2> <!-- Título de la página -->
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
            
            <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div> <!-- Añadir reCAPTCHA al formulario -->
            
            <input type="submit" value="Iniciar Sesión"> <!-- Botón para enviar el formulario -->
        </form>
        <button onclick="location.href='registro.php'" aria-label="Registrarse">Registrarse</button> <!-- Botón para ir a la página de registro -->
        <button onclick="location.href='olvide_contraseña.html'" aria-label="Olvidé mi contraseña">Olvidé mi contraseña</button> <!-- Botón para ir a la página de recuperación de contraseñas -->
</div>
</body>
</html>