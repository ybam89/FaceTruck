<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "pjyWa2THaRii5L2kC4LlvO8uofNIQ7dlTzyI0LpIuUea0Q44sz"; // Asegúrate de que esta contraseña es correcta
$dbname = "facetruck"; // Verifica que este es el nombre correcto de la base de datos

// Crear la conexión usando MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para sanitizar entradas
function sanitizeInput($input) {
    global $conn;
    return htmlspecialchars($conn->real_escape_string($input));
}
?>