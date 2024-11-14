<?php
// registro.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipoUsuario = $_POST['tipoUsuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña

    // Conectar a la base de datos
    $servername = "localhost";
    $db_username = "tu_usuario";
    $db_password = "tu_contraseña";
    $dbname = "facetruck";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Insertar datos en la tabla 'usuarios'
    $sql = "INSERT INTO usuarios (nombre, correo, username, password, tipo_usuario) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $correo, $username, $password, $tipoUsuario);
    if ($stmt->execute()) {
        echo "Registro exitoso!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>