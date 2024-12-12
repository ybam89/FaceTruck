<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    echo "Error: No se ha iniciado sesión correctamente o falta el ID de la publicación.";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$post_id = $_GET['id'];

$sql = "UPDATE publicaciones SET likes = likes + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

$sql = "SELECT likes FROM publicaciones WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($likes);
$stmt->fetch();
$stmt->close();

$conn->close();
echo $likes;
?>