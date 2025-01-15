<?php
$servername = "localhost";
$username = "root"; // Cambia esto por el nuevo nombre de usuario
$password = "pjyWa2THaRii5L2kC4LlvO8uofNIQ7dlTzyI0LpIuUea0Q44sz"; // Cambia esto por la nueva contraseña
$dbname = "mi_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>