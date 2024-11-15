<?php
// registro.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipoUsuario = $_POST['tipoUsuario'];
    $correo = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que las contraseñas coinciden
    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden.");
    }

    // Validar formato de la contraseña
    if (!preg_match('/(?=.*[A-Z])[A-Za-z\d]{8,}/', $password)) {
        die("La contraseña debe tener al menos 8 caracteres, una mayúscula, y solo letras y números.");
    }

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Conectar a la base de datos
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "facetruck";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Aquí debes agregar la lógica para asignar el operador_id
    $operador_id = obtenerOperadorId($conn);

    // Depuración
    if ($operador_id === null) {
        die("Error: operador_id es NULL");
    }

    // Insertar datos en la tabla 'usuarios'
    $sql = "INSERT INTO usuarios (correo, operador_id, password, tipo_usuario) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $correo, $operador_id, $password_hash, $tipoUsuario);
    if ($stmt->execute()) {
        echo "Registro exitoso!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

function obtenerOperadorId($conn) {
    // Implementa la lógica para obtener el operador_id
    $sql = "SELECT id FROM operadores ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'] + 1;
    } else {
        return 1; // Si no hay operadores, empieza con el ID 1
    }
}
?>