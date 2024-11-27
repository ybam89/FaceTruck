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

if (!isset($_SESSION['usuario_id'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;

    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $sql = "UPDATE Usuarios SET foto_perfil = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target_file, $usuario_id);
            if ($stmt->execute()) {
                echo "La foto de perfil se ha actualizado correctamente.";
                header("Location: perfil.php");
                exit();
            } else {
                echo "Error al actualizar la base de datos.";
            }
            $stmt->close();
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "El archivo no es una imagen.";
    }
}

$conn->close();
?>