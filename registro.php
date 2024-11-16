<?php
session_start(); // Inicia la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificación de reCAPTCHA
    $recaptcha_secret = 'YOUR_SECRET_KEY'; // Reemplaza 'YOUR_SECRET_KEY' con tu clave secreta de reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    if (!$recaptcha->success) {
        $_SESSION['error'] = 'Error de verificación de reCAPTCHA. Inténtalo de nuevo.';
        header("Location: registro.php");
        exit();
    }

    $correo = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validación de la contraseña
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un número.';
        header("Location: registro.php");
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header("Location: registro.php");
        exit();
    }

    // Datos de conexión a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "facetruck";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Verificar si el correo ya está registrado
    $sql = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = 'El correo electrónico ya está registrado.';
        $stmt->close();
        $conn->close();
        header("Location: registro.php");
        exit();
    }

    // Registrar el nuevo usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (correo, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $correo, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = 'Error al registrar el usuario. Inténtalo de nuevo.';
    }

    $stmt->close();
    $conn->close();
    header("Location: registro.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - FaceTruck</title>
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
        .login-container input {
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            if (password !== confirmPassword) {
                alert("Las contraseñas no coinciden.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login-container">
        <img src="img/camion.jpg" alt="Camión">
        <h2>Registro - FaceTruck</h2>
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
        <form action="registro.php" method="post" onsubmit="return validateForm()">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" placeholder="Correo Electrónico" required>
            
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            
            <label for="confirm_password">Confirmar Contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            
            <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
            
            <input type="submit" value="Registrarse">
        </form>
        <button onclick="location.href='login.php'" aria-label="Iniciar Sesión">Iniciar Sesión</button>
    </div>
</body>
</html>