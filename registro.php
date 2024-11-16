<?php
// registro.php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene los datos del formulario
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

    // Crear la conexión
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Insertar el nuevo usuario en la tabla usuarios
        $sql = "INSERT INTO usuarios (correo, password, tipo_usuario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $correo, $password_hash, $tipoUsuario);
        $stmt->execute();

        // Obtener el id generado para el nuevo usuario
        $usuario_id = $stmt->insert_id;

        // Insertar en la tabla correspondiente según el tipo de usuario
        if ($tipoUsuario == 'HombreCamion') {
            $sql = "INSERT INTO HombreCamion (id) VALUES (?)";
        } elseif ($tipoUsuario == 'Empresa') {
            $sql = "INSERT INTO Empresa (id) VALUES (?)";
        } else {
            $sql = "INSERT INTO operadores (id) VALUES (?)";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();

        // Confirmar la transacción
        $conn->commit();

        // Iniciar sesión y asignar operador_id
        $_SESSION['operador_id'] = $usuario_id;

        // Redirigir a perfil.php después del registro exitoso
        header("Location: editar_perfil.php");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        die("Error: " . $e->getMessage());
    } finally {
        // Cerrar la declaración y la conexión
        $stmt->close();
        $conn->close();
    }
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
            width: 300px;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container label {
            margin-bottom: 5px;
        }
        .login-container input {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-container button {
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Registro - FaceTruck</h2>
        <form action="registro.php" method="post">
            <label for="tipoUsuario">Tipo de Usuario</label>
            <select id="tipoUsuario" name="tipoUsuario" required>
                <option value="Operador">Operador</option>
                <option value="HombreCamion">Hombre Camión</option>
                <option value="Empresa">Empresa</option>
            </select>
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Confirmar Contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Registrarse</button>
        </form>
    </div>
</body>
</html>