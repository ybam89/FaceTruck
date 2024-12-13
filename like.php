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

// Verificar si el usuario ya ha dado "like" a la publicación
$sql = "SELECT COUNT(*) FROM likes WHERE usuario_id = ? AND post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $post_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    // El usuario ya dio "like", así que lo eliminamos
    $sql = "DELETE FROM likes WHERE usuario_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $post_id);
    $stmt->execute();
    $stmt->close();

    // Restar el "like"
    $sql = "UPDATE publicaciones SET likes = likes - 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
} else {
    // El usuario no ha dado "like", así que añadimos el "like"
    $sql = "INSERT INTO likes (usuario_id, post_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $post_id);
    $stmt->execute();
    $stmt->close();

    // Sumar el "like"
    $sql = "UPDATE publicaciones SET likes = likes + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
}

// Obtener el número actualizado de "likes"
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