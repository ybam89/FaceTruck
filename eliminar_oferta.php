<?php
session_start(); // Inicia la sesión

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Termina la ejecución si hay un error en la conexión
}

// Obtener el ID de la oferta a eliminar
if (!isset($_POST['id'])) {
    echo "Error: No se ha proporcionado un ID de oferta.";
    exit;
}
$id_oferta = $_POST['id'];

// Consulta para eliminar la oferta de empleo
$sql = "DELETE FROM ofertas_empleo WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_oferta);

if ($stmt->execute()) {
    echo "Oferta de empleo eliminada con éxito.";
    header("Location: perfil.php");
} else {
    echo "Error al eliminar la oferta de empleo: " . $conn->error;
}

$stmt->close(); // Cierra la declaración preparada
$conn->close(); // Cierra la conexión a la base de datos
?>