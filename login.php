<?php
session_start(); // Inicia la sesión para poder usar $_SESSION

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validación del token CSRF
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        // Si el token no coincide, muestra un mensaje de error genérico
        $_SESSION['error'] = 'Error de validación. Inténtalo de nuevo.';
        header("Location: login.php");
        exit();
    }

    // Verificación de reCAPTCHA (Temporalmente deshabilitado)
    /*
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
    */

    // Incluir el archivo de conexión a la base de datos
    require 'db.php';

    // Sanitización de las entradas del usuario
    $correo = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

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
            $_SESSION['error'] = 'Correo o contraseña incorrectos.';
        }
    } else {
        // Si no se encuentra un usuario con el correo proporcionado, establece un mensaje de error en la sesión
        $_SESSION['error'] = 'Correo o contraseña incorrectos.';
    }

    // Cierra la declaración y la conexión a la base de datos
    $stmt->close();
    $conn->close();

    // Redirige al usuario de vuelta a la página de inicio de sesión con un mensaje de error
    header("Location: login.php");
    exit();
}

// Genera un nuevo token CSRF para el formulario
$_SESSION['token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <form action="login.php" method="post">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <label for="username">Correo Electrónico:</label>
            <input type="email" id="username" name="username" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <!-- Campo de reCAPTCHA (Temporalmente deshabilitado) -->
            <!-- <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div> -->
            <input type="submit" value="Iniciar Sesión">
        </form>

        <!-- Muestra mensajes de error si existen -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="error-message">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>