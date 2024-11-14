<?php
// login.php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['username'];
    $password = $_POST['password'];

    // Conectar a la base de datos
    $servername = "localhost";
    $db_username = "tu_usuario";
    $db_password = "tu_contraseña";
    $dbname = "facetruck";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Verificar el correo en la base de datos
    $sql = "SELECT id, password FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            echo 'Inicio de sesión exitoso!';
            // Redirigir al perfil de usuario
            header("Location: perfil.html");
            exit();
        } else {
            $_SESSION['error'] = 'Contraseña incorrecta';
        }
    } else {
        $_SESSION['error'] = 'Correo electrónico no encontrado';
    }

    $stmt->close();
    $conn->close();

    // Redirigir de vuelta a la página de inicio de sesión
    header("Location: login.html");
    exit();
}
?>