<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para contar el número de vacantes activas para 'operador'
$sql_vacantes = "SELECT COUNT(*) as num_vacantes FROM vacantes WHERE tipo = 'operador' AND estado = 'activa'";
$result_vacantes = $conn->query($sql_vacantes);

if ($result_vacantes->num_rows > 0) {
    $row_vacantes = $result_vacantes->fetch_assoc();
    $num_vacantes = $row_vacantes['num_vacantes'];
} else {
    $num_vacantes = 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario - FaceTruck</title>
</head>
<body>
    <div class="container">
        <p>Numero de vacantes activas para 'operador': <?php echo $num_vacantes; ?></p>
    </div>
</body>
</html>