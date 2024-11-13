<?php
// login.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Aquí puedes añadir tu lógica de autenticación, como verificar el usuario en una base de datos.
    if ($username == 'usuarioDemo' && $password == 'contraseñaDemo') {
        echo 'Inicio de sesión exitoso!';
    } else {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error de Inicio de Sesión</title>
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
                .error-container {
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .error-container h2 {
                    color: #D8000C;
                }
                .error-container p {
                    color: #666666;
                }
            </style>
            <script>
                setTimeout(function() {
                    window.location.href = "login.html";
                }, 3000);
            </script>
        </head>
        <body>
            <div class="error-container">
                <h2>Nombre de usuario o contraseña incorrectos</h2>
                <p>Serás redirigido al inicio de sesión en 3 segundos...</p>
            </div>
        </body>
        </html>
        ';
    }
}
?>
