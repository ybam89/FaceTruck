<?php
// login.php

// Verifica si el formulario ha sido enviado usando el método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene el correo electrónico y la contraseña del formulario
    $correo = $_POST['username'];
    $password = $_POST['password'];

    // Datos de conexión a la base de datos
    $servername = "localhost";
    $db_username = "tu_usuario";
    $db_password = "tu_contraseña";
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
            echo 'Inicio de sesión exitoso!';
            // Redirige al perfil del usuario
            header("Location: perfil.html");
            exit();
        } else {
            // Si la contraseña es incorrecta, muestra un mensaje de error
            echo 'Contraseña incorrecta';
        }
    } else {
        // Si no se encontró el correo en la base de datos, muestra un mensaje de error
        echo 'Correo electrónico no encontrado';
    }

    // Cierra la declaración y la conexión a la base de datos
    $stmt->close();
    $conn->close();
}
?>