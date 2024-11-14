<?php
// login.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['root'];
    $password = $_POST[''];

    // Conectar a la base de datos
    $servername = "localhost";
    $db_username = "tu_usuario";
    $db_password = "tu_contraseña";
    $dbname = "facetruck";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Verificar el usuario en la base de datos
    $sql = "SELECT id, password FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            echo 'Inicio de sesión exitoso!';
            // Redirigir al perfil de usuario
            header("Location: perfil.html");
        } else {
            echo 'Contraseña incorrecta';
        }
    } else {
        echo 'Nombre de usuario no encontrado';
    }

    $stmt->close();
    $conn->close();
}
?>